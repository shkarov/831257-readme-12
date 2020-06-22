<?php

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
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
 * @param $conf array Массив с параматрами для подключения к БД
 *
 * @return mysqli Объект-соединение с БД
*/
function dbConnect(array $conf) : mysqli
{
    $dbConf = $conf['db'];
    $con =  mysqli_connect($dbConf['host'], $dbConf['user'], $dbConf['password'], $dbConf['database']);
    if (!$con) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, "utf8");
    return($con);
}

/**
 * Отправляет запрос на чтение к таблице content_type в текущей БД и возвращает Ассоциативный массив
 *
 * @param $con mysqli Объект-соединение с БД
 *
 * @return  array Ассоциативный массив Результат запроса
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
 * @param $con mysqli Объект-соединение с БД
 * @param $typeId int (может быть  null) id типа контента
 * @param $sort string (может быть  null) вид сортировки
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetPosts(mysqli $con, ?int $typeId, ?string $sort) : array
{
    $sql = "SELECT p.*, u.login, u.avatar, c.class
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id";


    if (!is_null($typeId)) {
        $sql = $sql." WHERE c.id = ?";
    }

    if (is_null($sort)) {
        $sort = 'views';
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

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Отправляет запрос на чтение данных о конкретном посте, к таблицам post, user,content_type в текущей БД и возвращает Ассоциативный массив
 *
 * @param $con mysqli Объект-соединение с БД
 * @param $postId int or null, id выбранного поста
 *
 * @return  array Ассоциативный массив Информация о посте
 */
function dbGetSinglePost(mysqli $con, ?int $postId) : array
{
    if (is_null($postId)) {
        return [];
    }

    $sql = "SELECT p.*, u.login, u.avatar, u.subscribers, u.posts, c.class
            FROM   post AS p
                   JOIN user AS u
                   ON u.id = p.user_id
                   JOIN content_type AS c
                   ON c.id = p.content_type_id
            WHERE  p.id = ?";

    $stmt = db_get_prepare_stmt($con, $sql, [$postId]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_assoc($result);
}

/**
 * Запись нового поста в БД
 *
 * @param  mysqli $con Объект-соединение с БД
 * @param  int $$user_id id  пользователя
 * @param  array $post глобальный массив $_POST
 * @param  array $files глобальный массив $_FILES
 *
 * @return array возвращает id добавленного поста либо null
 */
function dbAddPost(mysqli $con, int $user_id, array $post, array $files) : ?int
{
    if ($post === []) {
        return null;
    }
    $type_id = $post['type_id'];

    switch ($type_id) {
        case 1:
            return dbAddPostPhoto($con, $user_id, $post, $files);
        case 2:
            return dbAddPostVideo($con, $user_id, $post);
        case 3:
            return dbAddPostText($con, $user_id, $post);
        case 4:
            return dbAddPostQuote($con, $user_id, $post);
        case 5:
            return dbAddPostLink($con, $user_id, $post);
        default:
            return checkPhotoForm($post);
    }
}

/**
 * Запись нового поста ФОТО в БД
 *
 * @param  mysqli $con Объект-соединение с БД
 * @param  int $$user_id id  пользователя
 * @param  array $post глобальный массив $_POST
 * @param  array $files глобальный массив $_FILES
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
 * @param  mysqli $con Объект-соединение с БД
 * @param  int $$user_id id  пользователя
 * @param  array $post глобальный массив $_POST
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
 * @param  mysqli $con Объект-соединение с БД
 * @param  int $$user_id id  пользователя
 * @param  array $post глобальный массив $_POST
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
 * @param  mysqli $con Объект-соединение с БД
 * @param  int $$user_id id  пользователя
 * @param  array $post глобальный массив $_POST
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
 * @param  mysqli $con Объект-соединение с БД
 * @param  int $$user_id id  пользователя
 * @param  array $post глобальный массив $_POST
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
 * @param  mysqli $con Объект-соединение с БД
 * @param  string $input_tags значение поля Теги формы
 * @param  int $post_id id нового поста
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
    return;
}

/**
 * Отправляет запрос на поиск записи с полем $email к таблице user
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $email адрес электронной почты из формы регистрации
 *
 * @return  bool
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
 * @return  bool
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
 * Запись нового пользователя в БД
 *
 * @param  mysqli $con Объект-соединение с БД
 * @param  array $post массив с данными создаваемого пользователя
 * @param  array $files массив с данными о загружаемом аватаре пользователя
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

    $avatar = savePicture($con, $files);

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
function dbGetUser(mysqli $con, string $email) : array
{
    $sql = "SELECT id, `login`, `password`, avatar FROM user WHERE email = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_assoc($result);
}
