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
 * Возвращает id типа контента из массива параметров запроса, если такой тип существует, иначе возвращает null
 *
 * @param $arr array массив параметров запроса
 *
 * @return int or null
 */
function getTypeFromRequest(array $arr) : ?int
{
    if (!isset($arr['type_id'])) {
        return null;
    }
    if (!is_numeric($arr['type_id'])) {
        exit('Некорректный параметр type_id');
    }
    return (int) $arr['type_id'];
}

/**
 * Возвращает id типа контента из массива параметров запроса, если такой тип существует, иначе возвращает null
 *
 * @param $arr array массив параметров запроса
 *
 * @return int or null
 */
function getTypeFromRequestS(array $get, array $post) : ?int
{
    if (isset($get['type_id'])) {
        return (int) $get['type_id'];
    } elseif (isset($post['type_id'])) {
        return (int) $post['type_id'];
    }
    return null;
}

/**
 * Возвращает id поста из массива параметров запроса, если id найден, иначе возвращает null
 *
 * @arr array массив параметров запроса
 *
 * @return int or null
 */
function getPostIdFromRequest(array $arr) : ?int
{
    if (!isset($arr['post_id'])) {
        return null;
    }
    if (!is_numeric($arr['post_id'])) {
        exit('Некорректный параметр post_id');
    }
    return (int) $arr['post_id'];
}

/**
 * Возвращает признак сортировки из массива параметров запроса, если параметр найден, иначе возвращает null
 *
 * @arr array массив параметров запроса
 *
 * @return int or null
 */
function getSortFromRequest(array $arr) : ?int
{
    if (!isset($arr['sort'])) {
        return null;
    }
    if (!is_string($arr['sort'])) {
        exit('Некорректный параметр sort');
    }
    return $arr['sort'];
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
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getPostVal($arr, $name) {
    return $arr[$name] ?? "";
}
