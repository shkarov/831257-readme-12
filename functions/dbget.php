<?php

/**
 * Отправляет запрос на чтение к таблице content_type в текущей БД и возвращает Ассоциативный массив
 *
 * @param mysqli $con Объект-соединение с БД
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetTypes(mysqli $con) : array
{
    $sql = "SELECT * FROM content_type";
    $result = mysqli_query($con, $sql);
    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Отправляет запрос на чтение к таблицам post, user,content_type в текущей БД и возвращает Ассоциативный массив
 *
 * @param mysqli $con    Объект-соединение с БД
 * @param int    $typeId (может быть  null) id типа контента
 * @param string $sort   вид сортировки
 * @param int    $page   номер страницы для вывода результатов запросв
 * @param int    $limit  количество постов на странице
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetPostsPopular(mysqli $con, ?int $typeId, string $sort, int $page, int $limit) : array
{
    $offset = ($page - 1) * $limit;

    $sql = "SELECT p.*, u.login, u.avatar, c.class
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id";

    if (!is_null($typeId)) {
        $sql = $sql." WHERE c.id = ?";
    }

    $sql = $sql." ORDER BY p.".$sort." DESC";
    $sql = $sql." LIMIT ".$limit." OFFSET ".$offset;

    if (!is_null($typeId)) {
        $stmt = db_get_prepare_stmt($con, $sql, [$typeId]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($con, $sql);
    }

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Возвращает количество строк в результате запроса к таблице `post`
 *
 * @param mysqli $con    Объект-соединение с БД
 * @param int    $typeId (может быть  null) id типа контента
 * @param string $sort   вид сортировки
 *
 * @return int
 */
function dbGetPostsPopularCount(mysqli $con, ?int $typeId, string $sort) : int
{
    $sql = "SELECT COUNT(*) AS count_posts
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id";

    if (!is_null($typeId)) {
        $sql = $sql." WHERE c.id = ?";
    }

    $sql = $sql." ORDER BY p.".$sort." DESC";

    if (!is_null($typeId)) {
        $stmt = db_get_prepare_stmt($con, $sql, [$typeId]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($con, $sql);
    }

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    $result_array = mysqli_fetch_assoc($result);

    return (int) $result_array['count_posts'];
}

/**
 * Отправляет запрос на чтение данных о конкретном посте
 *
 * @param mysqli $con    Объект-соединение с БД
 * @param int    $postId id выбранного поста
 *
 * @return array Ассоциативный массив Информация о посте
 */
function dbGetPostWithUserInfo(mysqli $con, int $postId) : array
{
    $sql = "SELECT p.*, u.login, u.avatar, u.email, u.subscribers, u.posts, u.creation_time AS user_creation_time, c.class, GROUP_CONCAT(DISTINCT tags.name ORDER BY tags.name ASC SEPARATOR ' ') AS hashtags
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id
                   LEFT JOIN (
                        SELECT h.name, ph.post_id
                        FROM   hashtag as h
                               JOIN post_hashtag as ph
                               ON   ph.hashtag_id = h.id
                        ) AS tags
                   ON   p.id = tags.post_id
            WHERE  p.id = ?
            GROUP BY p.id";

    $stmt = db_get_prepare_stmt($con, $sql, [$postId]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Запрос на получение заголовка поста
 *
 * @param mysqli $con    бъект-соединение с БД
 * @param int    $postId id выбранного поста
 *
 * @return string заголовок поста
 */
function dbGetPostHeader(mysqli $con, int $postId) : string
{
    $sql = "SELECT post.heading
            FROM   post
            WHERE  post.id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$postId]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    $arr = mysqli_fetch_assoc($result);

    return $arr['heading'];
}

/**
 * Отправляет запрос на поиск записи с полем $email к таблице user
 *
 * @param mysqli $con   Объект-соединение с БД
 * @param string $email адрес электронной почты из формы регистрации
 *
 * @return array
 */
function dbGetUserByEmail(mysqli $con, string $email) : array
{
    $sql = "SELECT id, `login`, `password`, avatar, creation_time, subscribers, posts FROM user WHERE email = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Отправляет запрос на поиск записи с полем $user_id к таблице user
 *
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id пользователя
 *
 * @return array
 */
function dbGetUserById(mysqli $con, int $user_id) : array
{
    $sql = "SELECT id, `login`, `password`, email, avatar, creation_time, posts, subscribers FROM user WHERE id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Выборка постов пользователей, на которых подписан залогиненный пользователь, с учетом типа контента
 * возвращает Ассоциативный массив, отсортированный по убыванию даты создания поста
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id авторизованного пользователя
 * @param int    $type_id (может быть  null) id типа контента
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetPostsFeed(mysqli $con, int $user_id, ?int $type_id) : array
{
    $sql = "SELECT p.*, u.login, u.avatar, c.class, c.class, GROUP_CONCAT(DISTINCT tags.name ORDER BY tags.name ASC SEPARATOR ' ') AS hashtags
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id
                   LEFT JOIN (
                             SELECT h.name, ph.post_id
                             FROM   hashtag as h
                                    JOIN post_hashtag as ph
                                    ON   ph.hashtag_id = h.id
                             ) AS tags
                   ON   p.id = tags.post_id
            WHERE  u.id IN (
                            SELECT creator_user_id
                            FROM   subscription
                            WHERE  subscriber_user_id = $user_id)";

    if (!is_null($type_id)) {
        $sql = $sql." && c.id = $type_id";
    }

    $sql = $sql." GROUP BY p.id
                  ORDER BY p.creation_time DESC";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выборка по строке полнотекстового поиска
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $str строка поиска
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetPostsSearchFulltext(mysqli $con, string $str) : array
{
    $sql = "SELECT p.*, u.login, u.avatar, c.class
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id
            WHERE  MATCH(heading, `text`) AGAINST(?)";

    $stmt = db_get_prepare_stmt($con, $sql, [$str]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выборка по хэштегу
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $str строка поиска
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetPostsSearchHashtag(mysqli $con, string $str) : array
{
    $sql = "SELECT p.*, u.login, u.avatar, c.class
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id
            WHERE  p.id IN (

                   SELECT p.id
                   FROM   post_hashtag AS ph
                          JOIN hashtag AS h
                          ON ph.hashtag_id = h.id
                          JOIN post AS p
                          ON ph.post_id = p.id
                   WHERE  h.name = ?
                  )

            ORDER BY p.creation_time DESC";

    $stmt = db_get_prepare_stmt($con, $sql, [$str]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выборка всех постов пользователя
 *
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id пользователя
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetUserPosts(mysqli $con, int $user_id) : array
{
    $sql = "SELECT   p.*, u.creation_time AS creation_time_user, u.email, u.login, u.avatar, u.subscribers, u.posts, c.class, GROUP_CONCAT(DISTINCT tags.name ORDER BY tags.name ASC SEPARATOR ' ') AS hashtags
            FROM     post AS p
                     JOIN user AS u
                     ON   u.id = p.user_id
                     JOIN content_type AS c
                     ON   c.id = p.content_type_id
                     LEFT JOIN (
                          SELECT h.name, ph.post_id
                          FROM   hashtag as h
                                 JOIN post_hashtag as ph
                                 ON   ph.hashtag_id = h.id
                          ) AS tags
                     ON   p.id = tags.post_id
            WHERE         u.id = ?
            GROUP BY p.id
            ORDER BY p.creation_time DESC";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выборка постов пользователя с лайками
 *
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id пользователя
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetUserPostsWithLikes(mysqli $con, int $user_id) : array
{
    $sql = "SELECT   p.id, p.picture, p.video, p.class, u.id AS user_id_who_liked_post, u.login, u.avatar, l.creation_time AS creation_time_like
            FROM     `like` AS l
                     JOIN user AS u
                     ON   u.id = l.user_id
                     JOIN (
                           SELECT post.*, c.class
                           FROM   post
                                  JOIN content_type AS c
                                  ON   c.id = post.content_type_id
	                       WHERE  post.user_id = ? AND  post.likes > 0
                          ) AS p
                     ON   p.id = l.post_id
            ORDER BY l.creation_time DESC";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выборка подписчиков пользователя
 *
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id пользователя
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetUserSubscribers(mysqli $con, int $user_id) : array
{
    $sql = "SELECT  u.id, u.login, u.email
                    FROM subscription AS s
                    JOIN user AS u
                    ON   u.id = s.subscriber_user_id
            WHERE   s.creator_user_id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выборка подписчиков пользователя + поле наличия подписки на него залогиненого пользователя
 *
 * @param mysqli $con           Объект-соединение с БД
 * @param int    $user_id       id пользователя
 * @param int    $user_id_login id залогиненого пользователя
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetUserSubscribersWithMutualSubscription(mysqli $con, int $user_id, int $user_id_login) : array
{

    $sql = "SELECT sele1.*, if(sele2.id IS NULL, false, true) AS mutual_subscribe

            FROM
                  (SELECT   u.id AS user_id_subscriber, u.creation_time AS creation_time_user, u.login, u.avatar, u.subscribers, u.posts
                   FROM     subscription AS s
                            JOIN user AS u
                            ON   u.id = s.subscriber_user_id
                   WHERE    s.creator_user_id = ?)
                   AS sele1

                   LEFT JOIN

                  (SELECT   u2.id
                   FROM     subscription AS s2
                            JOIN user AS u2
                            ON   u2.id = s2.creator_user_id
                   WHERE    s2.subscriber_user_id = ?)
                   AS sele2

            ON sele1.user_id_subscriber = sele2.id";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id, $user_id_login]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Отправляет запрос на чтение к текущей БД для получения комментариев к запросу
 *
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $post_id id поста
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetPostComments(mysqli $con, int $post_id) : array
{
    $sql = "SELECT   c.creation_time, c.text, u.id AS user_id, u.login, u.avatar
            FROM     comment AS c
                     JOIN user AS u
                     ON   u.id = c.user_id
                     JOIN post AS p
                     ON   p.id = c.post_id
            WHERE    p.id = ?
            ORDER BY p.creation_time DESC";

    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Отправляет запрос на поиск записи с $post_id к таблице post
 * (если поста с таким id нет в БД, функция вернет 0)
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id логин пользователя из формы регистрации
 *
 * @return int id пользователя, создателя поста
 */
function dbGetUserIdFromPost(mysqli $con, int $post_id) : int
{
    $sql = "SELECT user_id FROM post WHERE id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_array = mysqli_fetch_assoc($result);

    return (int) $result_array['user_id'];
}

/**
 * Отправляет запрос на чтение данных о конкретном посте
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 *
 * @return array Ассоциативный массив Информация о посте
 */
function dbGetPost(mysqli $con, int $post_id) : array
{
    $sql = "SELECT *
            FROM   post
            WHERE  post.id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$post_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Получение списка пользователей, имеющих сообщения с текущим пользователем
 * Список отсортирован по дате создания сообщения, с неуникальным полем user_id (требуется приведение массива к уникальным значениям)
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id пользователя
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetContactsMessages(mysqli $con, int $user_id) : array
{
    $sql = "SELECT sele1.*, u.login, u.avatar
            FROM   user AS u
                   JOIN (
                         (SELECT m1.recipient_user_id AS user_id, m1.creation_time, CONCAT(SUBSTRING(m1.text, 1, 10), '...') AS `text`
                          FROM   message AS m1
                          WHERE  m1.sender_user_id = ?)

                          UNION ALL

                         (SELECT m2.sender_user_id AS user_id, m2.creation_time, CONCAT(SUBSTRING(m2.text, 1, 10), '...') AS `text`
                          FROM   message AS m2
                          WHERE m2.recipient_user_id = ?)

                          ORDER BY user_id, creation_time DESC
                         ) AS sele1

                   ON u.id = sele1.user_id";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Получение сообщений пользователя с залогиненым пользователем
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id пользователя
 * @param int    $user_id_login id залогиненого пользователя
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetMessages(mysqli $con, int $user_id, int $user_id_login) : array
{
    $sql = "SELECT u.id AS user_id, u.login, u.avatar, m.creation_time, `text`
	        FROM   message AS m
                   JOIN user AS u
                   ON   u.id = m.sender_user_id
            WHERE  (m.sender_user_id = ? and m.recipient_user_id = ?) or (m.sender_user_id = ? and m.recipient_user_id = ?)
            ORDER BY m.creation_time";

    $stmt = db_get_prepare_stmt($con, $sql, [$user_id, $user_id_login, $user_id_login, $user_id]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
