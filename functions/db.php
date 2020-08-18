<?php

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param mysqli $link  Ресурс соединения
 * @param string $sql  SQL запрос с плейсхолдерами вместо значений
 * @param array  $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Устанавливает соединение с базой данных(БД) и возвращает объект соединения
 *
 * @param array $conf Массив с параматрами для подключения к БД
 *
 * @return mysqli Объект-соединение с БД
*/
function dbConnect(array $conf) : mysqli
{
    $db_conf = $conf['db'];
    $con =  mysqli_connect($db_conf['host'], $db_conf['user'], $db_conf['password'], $db_conf['database']);
    if (!$con) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, "utf8");
    return($con);
}

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
 * @param mysqli $con  Объект-соединение с БД
 * @param int    $typeId  (может быть  null) id типа контента
 * @param string $sort вид сортировки
 * @param int    $page  номер страницы для вывода результатов запросв
 * @param int    $limit количество постов на странице
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
 * @param mysqli $con  Объект-соединение с БД
 * @param int    $typeId  (может быть  null) id типа контента
 * @param string $sort вид сортировки
 *
 * @return int
 *
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
 * @param mysqli $con Объект-соединение с БД
 * @param int    $postId, id выбранного поста
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
 * @param mysqli $con Объект-соединение с БД
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
 * Запись нового поста ФОТО в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post глобальный массив $_POST
 * @param array  $files глобальный массив $_FILES
 *
 * @return array возвращает id добавленного поста либо null
 */
function dbAddPostPhoto(mysqli $con, int $user_id, array $post, array $files) : ?int
{
    $post_id = null;
    $creation_time = date("Y-m-d H:i:s");
    $heading = $post['photo-heading'];
    $content_type_id = 1;

    $file_path = 'uploads/';

    $file = $files['userpic-file-photo'];
    //файл получен с локального ПК
    if (!empty($file['name'])) {
        //формируем новое имя файла
        $file_name = hash_hmac_file('md5', $file['tmp_name'], (string) $user_id);
        $file_ext = mb_substr($file['type'], mb_strpos($file['type'], '/') + 1);
        $file_ext = $file_ext === 'jpeg' ? 'jpg' : $file_ext;
        $file_url = $file_path.$file_name.'.'.$file_ext;

        if (!move_uploaded_file($file['tmp_name'], $file_url)) {
            echo "Ошибка перемещения файла";
        };
    //файл получен по интернет-ссылке
    } else {
        //определяем тип файла и формируем расширение
        $file_headers = get_headers($post['photo-url']);
        $content_type = '';
        foreach ($file_headers as $key => $value) {
            if (mb_strpos($value, 'Content-Type:') !== false) {
                $content_type = ltrim(mb_substr($value, -10));
            }
        }
        $file_ext = mb_substr($content_type, mb_strpos($content_type, '/') + 1);
        $file_ext = $file_ext === 'jpeg' ? 'jpg' : $file_ext;

        //вычисляем имя загружаемого файла
        $file_name_old = mb_substr($post['photo-url'], mb_strpos($post['photo-url'], '/', -1));
        //формируем новое имя файла
        $file_name = hash_hmac_file('md5', $file_name_old, (string) $user_id);

        $file_origin = file_get_contents($post['photo-url']);
        if (!$file_origin) {
            echo "Ошибка загрузки файла";
        }

        $file_url = $file_path.$file_name.'.'.$file_ext;
        if (!file_put_contents($file_url, $file_origin)) {
            echo "Ошибка создания файла";
        }
    }

    $sql = 'INSERT post (creation_time, heading, picture, user_id, content_type_id) VALUES (?,?,?,?,?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $creation_time, $heading, $file_url, $user_id, $content_type_id);
    mysqli_stmt_execute($stmt);
    $post_id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    //Запись тегов поста
    dbWriteTags($con, $post['photo-tags'], $post_id);

    return $post_id;
}

/**
 * Запись нового поста ВИДЕО в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $$user_id id  пользователя
 * @param array  $post глобальный массив $_POST
 *
 * @return array возвращает id добавленного поста либо null
 */
function dbAddPostVideo(mysqli $con, int $user_id, array $post) : ?int
{
    $post_id = null;
    $creation_time = date("Y-m-d H:i:s");
    $heading = $post['video-heading'];
    $url = $post['video-url'];
    $content_type_id = 2;

    $sql = 'INSERT post (creation_time, heading, video, user_id, content_type_id) VALUES (?,?,?,?,?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $creation_time, $heading, $url, $user_id, $content_type_id);
    mysqli_stmt_execute($stmt);
    $post_id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    //Запись тегов поста
    dbWriteTags($con, $post['video-tags'], $post_id);

    return $post_id;
}

/**
 * Запись нового поста ТЕКСТ в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post глобальный массив $_POST
 *
 * @return array возвращает id добавленного поста либо null
 */
function dbAddPostText(mysqli $con, int $user_id, array $post) : ?int
{
    $post_id = null;
    $creation_time = date("Y-m-d H:i:s");
    $heading = $post['text-heading'];
    $text = $post['text-text'];
    $content_type_id = 3;

    $sql = 'INSERT post (creation_time, heading, `text`, user_id, content_type_id) VALUES (?,?,?,?,?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $creation_time, $heading, $text, $user_id, $content_type_id);
    mysqli_stmt_execute($stmt);
    $post_id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    //Запись тегов поста
    dbWriteTags($con, $post['text-tags'], $post_id);

    return $post_id;
}

/**
 * Запись нового поста ЦИТАТА в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post глобальный массив $_POST
 *
 * @return array возвращает id добавленного поста либо null
 */
function dbAddPostQuote(mysqli $con, int $user_id, array $post) : ?int
{
    $post_id = null;
    $creation_time = date("Y-m-d H:i:s");
    $heading = $post['quote-heading'];
    $text = $post['quote-text'];
    $author = $post['quote-author'];
    $content_type_id = 4;

    $sql = 'INSERT post (creation_time, heading, `text`, author_quote, user_id, content_type_id) VALUES (?,?,?,?,?,?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssii', $creation_time, $heading, $text, $author, $user_id, $content_type_id);
    mysqli_stmt_execute($stmt);
    $post_id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    //Запись тегов поста
    dbWriteTags($con, $post['quote-tags'], $post_id);

    return $post_id;
}

/**
 * Запись нового поста ССЫЛКА в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post глобальный массив $_POST
 *
 * @return array возвращает id добавленного поста либо null
 */
function dbAddPostLink(mysqli $con, int $user_id, array $post) : ?int
{
    $post_id = null;
    $creation_time = date("Y-m-d H:i:s");
    $heading = $post['link-heading'];
    $link = $post['link-url'];
    $content_type_id = 5;

    $sql = 'INSERT post (creation_time, heading, link, user_id, content_type_id) VALUES (?,?,?,?,?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssii', $creation_time, $heading, $link, $user_id, $content_type_id);
    mysqli_stmt_execute($stmt);
    $post_id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    //Запись тегов поста
    dbWriteTags($con, $post['link-tags'], $post_id);

    return $post_id;
}

/**
 * Запись тегов поста в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $input_tags значение поля Теги формы
 * @param int    $post_id id нового поста
 *
 * @return
 */
function dbWriteTags(mysqli $con, string $input_tags, ?int $post_id ) : void
{
    $field_tags = trim($input_tags);

    if (!empty($field_tags)) {
        $tags = explode(' ', $field_tags);

        foreach ($tags as $key => $tag) {
            //поиск тага
            $sql_tag = "SELECT id FROM hashtag WHERE name = ?";
            $stmt_tag = mysqli_prepare($con, $sql_tag);
            mysqli_stmt_bind_param($stmt_tag, 's', $tag);
            mysqli_stmt_execute($stmt_tag);
            $res_tag = mysqli_stmt_get_result($stmt_tag);
            $rows_tag = mysqli_fetch_all($res_tag, MYSQLI_ASSOC);

            //такого тага ещё нет в базе
            if (empty($rows_tag)) {
                $sql_tag_new = 'INSERT hashtag (name) VALUES (?)';
                $stmt_tag_new = mysqli_prepare($con, $sql_tag_new);
                mysqli_stmt_bind_param($stmt_tag_new, 's', $tag);
                mysqli_stmt_execute($stmt_tag_new);
                $hashtag_id = mysqli_stmt_insert_id($stmt_tag_new);
                mysqli_stmt_close($stmt_tag_new);
            //такой таг уже есть в базе
            } else {
                $hashtag_id = $rows_tag[0]['id'];
            }

            $sql_post_tag = 'INSERT post_hashtag (post_id, hashtag_id) VALUES (?,?)';
            $stmt_post_tag = mysqli_prepare($con, $sql_post_tag);
            mysqli_stmt_bind_param($stmt_post_tag, 'ii', $post_id, $hashtag_id);
            mysqli_stmt_execute($stmt_post_tag);
            mysqli_stmt_close($stmt_post_tag);
        }
    }
}

/**
 * Отправляет запрос на поиск записи с полем $email к таблице user
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $email адрес электронной почты из формы регистрации
 *
 * @return bool
 */
function dbFindEmail(mysqli $con, string $email) : bool
{
    $sql = "SELECT id FROM user WHERE email = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_rows = mysqli_num_rows($result);

    return $result_rows === 0 ? false : true;
}

/**
 * Отправляет запрос на поиск записи с полем $login к таблице user
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $login логин пользователя из формы регистрации
 *
 * @return bool
 */
function dbFindLogin(mysqli $con, string $login) : bool
{
    $sql = "SELECT id FROM user WHERE login = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_rows = mysqli_num_rows($result);

    return $result_rows === 0 ? false : true;
}

/**
 * Отправляет запрос на поиск записи с полем $id к таблице post
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 *
 * @return bool
 */
function dbFindPost(mysqli $con, int $post_id) : bool
{
    $sql = "SELECT id FROM post WHERE id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_rows = mysqli_num_rows($result);

    return $result_rows === 0 ? false : true;
}

/**
 * Запись нового пользователя в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param array  $post массив с данными создаваемого пользователя
 * @param array  $files массив с данными о загружаемом аватаре пользователя
 *
 * @return array возвращает id добавленного пользователя либо null
 */
function dbAddUser(mysqli $con, array $post, array $files) : ?int
{
    $user_id = null;
    if (empty($post)) {
        return null;
    }

    $creation_time = date("Y-m-d H:i:s");
    $email = $post['email'];
    $login = $post['login'];
    $password = password_hash($post['password'], PASSWORD_DEFAULT);

    $avatar = savePicture($files);

    $sql = 'INSERT user (creation_time, email, `login`, `password`, avatar) VALUES (?,?,?,?,?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'sssss', $creation_time, $email, $login, $password, $avatar);
    mysqli_stmt_execute($stmt);
    $user_id = mysqli_stmt_insert_id($stmt);
    mysqli_stmt_close($stmt);

    return $user_id;
}

/**
 * Отправляет запрос на поиск записи с полем $email к таблице user
 *
 * @param mysqli $con   Объект-соединение с БД
 * @param string $email адрес электронной почты из формы регистрации
 *
 * @return array ассоциативный массив
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
 * @param mysqli $con   Объект-соединение с БД
 * @param int    $user_id id пользователя
 *
 * @return array ассоциативный массив
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
 * @param mysqli $con Объект-соединение с БД
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
 * @param mysqli $con Объект-соединение с БД
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
 * @param mysqli $con Объект-соединение с БД
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
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id пользователя
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
 * @param mysqli $con Объект-соединение с БД
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
 * Запись нового комментария в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $$user_id id пользователя, добавившего комментарий
 * @param string $comment текст комментария
 *
 * @return bool
 */
function dbAddComment(mysqli $con, int $post_id, int $user_id, string $comment) : bool
{
    $creation_time = date("Y-m-d H:i:s");

    $sql1 = 'INSERT comment (creation_time, `text`, user_id, post_id) VALUES (?,?,?,?)';
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, 'ssii', $creation_time, $comment, $user_id, $post_id);

    $sql2 = 'UPDATE post
             SET    comments = comments + 1
             WHERE  id = ?';

    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $post_id);

    mysqli_begin_transaction($con);

    $result1 = mysqli_stmt_execute($stmt1);
    $result2 = mysqli_stmt_execute($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($con);
        return true;
      }
    mysqli_rollback($con);
    return false;
}

/**
 * Увеличение счетчика просмотров поста
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 *
 * @return bool
 */
function dbAddView(mysqli $con, int $post_id) : bool
{
    $sql = 'UPDATE post
            SET    views = views + 1
            WHERE  id = ?';

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);

    return mysqli_stmt_execute($stmt);
}

/**
 * Запись нового лайка в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $user_id id пользователя, добавившего лайк
 *
 * @return bool
 */
function dbAddLike(mysqli $con, int $post_id, int $user_id) : bool
{
    $creation_time = date("Y-m-d H:i:s");

    $sql1 = 'INSERT `like` (post_id, user_id, creation_time) VALUES (?,?,?)';
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, 'iis', $post_id, $user_id, $creation_time);

    $sql2 = 'UPDATE post
             SET    likes = likes + 1
             WHERE  id = ?';

    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $post_id);

    mysqli_begin_transaction($con);

    $result1 = mysqli_stmt_execute($stmt1);
    $result2 = mysqli_stmt_execute($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($con);
        return true;
      }
    mysqli_rollback($con);
    return false;
}

/**
 * Ищет лайк в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $user_id id пользователя
 *
 * @return bool Возвращает false если лайк не найден, иначе true
 */
function dbFindLike(mysqli $con, int $post_id, int $user_id) : bool
{
    $sql = "SELECT COUNT(*) AS `is_like`
            FROM   `like`
            WHERE  post_id = $post_id AND user_id = $user_id";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_array = mysqli_fetch_assoc($result);

    return ($result_array['is_like'] == 0) ? false : true;
}

/**
 * Запись новой подписки в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int $user_id_creator id пользователя, на которого подписались
 * @param int $user_id_subscriber id подписщика
 *
 * @return bool
 */
function dbAddSubscribe(mysqli $con, int $user_id_creator, int $user_id_subscriber) : bool
{
    $sql1 = 'INSERT subscription (creator_user_id, subscriber_user_id) VALUES (?,?)';
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, 'ii', $user_id_creator, $user_id_subscriber);

    $sql2 = 'UPDATE user
             SET    subscribers = subscribers + 1
             WHERE  id = ?';

    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $user_id_creator);

    mysqli_begin_transaction($con);

    $result1 = mysqli_stmt_execute($stmt1);
    $result2 = mysqli_stmt_execute($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($con);
        return true;
      }
    mysqli_rollback($con);
    return false;
}

/**
 * Удаление подписки в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int $user_id_creator id пользователя, на которого подписались
 * @param int $user_id_subscriber id подписщика
 *
 * @return bool
 */
function dbDelSubscribe(mysqli $con, int $user_id_creator, int $user_id_subscriber) : bool
{
    $sql1 = 'DELETE FROM subscription
             WHERE creator_user_id = ? AND subscriber_user_id = ?';

    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, 'ii', $user_id_creator, $user_id_subscriber);

    $sql2 = 'UPDATE user
             SET    subscribers = subscribers - 1
             WHERE  id = ?';

    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $user_id_creator);

    mysqli_begin_transaction($con);

    $result1 = mysqli_stmt_execute($stmt1);
    $result2 = mysqli_stmt_execute($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($con);
        return true;
      }
    mysqli_rollback($con);
    return false;
}

/**
 * Ищет подписку в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int $user_id_creator id пользователя, на которого подписались
 * @param int $user_id_subscriber id подписщика
 *
 * @return bool Возвращает false усли подписка не найдена, иначе true
 */
function dbFindSubscribe(mysqli $con, int $user_id_creator, int $user_id_subscriber) : bool
{
    $sql = "SELECT COUNT(*) AS `is_subscribe`
            FROM   subscription
            WHERE  creator_user_id = $user_id_creator AND subscriber_user_id = $user_id_subscriber";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_array = mysqli_fetch_assoc($result);

    return ($result_array['is_subscribe'] == 0) ? false : true;
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
 * Запись репоста в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param array  $post массив данных поста
 * @param int    $user_id id пользователя, делающего репост
 *
 * @return bool
 */
function dbAddRepost(mysqli $con, array $post, int $user_id) : bool
{
    $creation_time = date("Y-m-d H:i:s");
    $repost = 1;

    $sql1 = 'INSERT post (creation_time, heading, `text`, author_quote, picture, video, link, repost, original_user_id, user_id, content_type_id) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, 'sssssssiiii', $creation_time, $post['heading'], $post['text'], $post['author_quote'], $post['picture'], $post['video'], $post['link'], $repost, $post['user_id'], $user_id, $post['content_type_id']);

    $sql2 = 'UPDATE post
             SET    reposts = reposts + 1
             WHERE  id = ?';
    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $post['id']);

    mysqli_begin_transaction($con);

    $result1 = mysqli_stmt_execute($stmt1);
    //$post_id_new = mysqli_stmt_insert_id($stmt1);
    $result2 = mysqli_stmt_execute($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($con);
        return true;
      }
    mysqli_rollback($con);
    return false;
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

/**
 * Добавление пользователя в начало списка
 *
 * @param mysqli $con   Объект-соединение с БД
 * @param array  $contacts список пользователей
 * @param int    $user_id id пользователя
 *
 * @return array
 */
function addUserInList(mysqli $con, array $contacts, int $user_id) : array
{
    $user = dbGetUserById($con, $user_id);

    $arr = ['user_id' => $user_id,
            'creation_time' => date("Y-m-d H:i:s"),
            'text' => "",
            'login' => $user['login'],
            'avatar' => $user['avatar']
           ];

    array_unshift($contacts, $arr);

    return $contacts;
}

/**
 * Запись нового комментария в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param array  $post глобальный массив $_POST
 * @param int    $user_id_login id залогиненого пользователя
 *
 * @return bool
 */
function dbAddMessage(mysqli $con, array $post, int $user_id_login) : bool
{
    $creation_time = date("Y-m-d H:i:s");
    $user_id = $post['user_id'];
    $text = $post['message'];

    $sql = 'INSERT message (creation_time, `text`, recipient_user_id, sender_user_id) VALUES (?,?,?,?)';
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssii', $creation_time, $text, $user_id, $user_id_login);

    return mysqli_stmt_execute($stmt);
}
