<?php

/**
 * Запись нового поста ФОТО в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post глобальный массив $_POST
 * @param array  $files глобальный массив $_FILES
 *
 * @return int возвращает id добавленного поста либо null
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
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post    глобальный массив $_POST
 *
 * @return int возвращает id добавленного поста либо null
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
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post    глобальный массив $_POST
 *
 * @return int возвращает id добавленного поста либо null
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
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post    глобальный массив $_POST
 *
 * @return int возвращает id добавленного поста либо null
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
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post    глобальный массив $_POST
 *
 * @return int возвращает id добавленного поста либо null
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
 * Запись нового пользователя в БД
 *
 * @param mysqli $con   Объект-соединение с БД
 * @param array  $post  массив с данными создаваемого пользователя
 * @param array  $files массив с данными о загружаемом аватаре пользователя
 *
 * @return int возвращает id добавленного пользователя либо null
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
 * Запись нового комментария в БД
 *
 * @param mysqli $con      Объект-соединение с БД
 * @param int    $post_id  id поста
 * @param int    $user_id id пользователя, добавившего комментарий
 * @param string $comment  текст комментария
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
 * @param mysqli $con     Объект-соединение с БД
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
 * @param mysqli $con     Объект-соединение с БД
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
 * Запись новой подписки в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id_creator id пользователя, на которого подписались
 * @param int    $user_id_subscriber id подписчика
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
    $result2 = mysqli_stmt_execute($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($con);
        return true;
      }
    mysqli_rollback($con);
    return false;
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
