<?php

require_once 'bootstrap.php';

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];
$user_creatin_time = $_SESSION['creation_time'];
$user_posts_count = $_SESSION['posts'];
$user_subscribers = $_SESSION['subscribers'];

$types = dbGetTypes($connect);

$typeId = getTypeFromRequest($_GET);

//$sort = getSortFromRequest($_GET);

//$posts = dbGetPostsUser($connect, $typeId, $sort);

//$page_content = include_template("profile-details.php", ['types' => $types, 'posts' => $posts, 'type_id' => $typeId, 'sort' => $sort]);
$page_content = '';

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: профиль', 'user' => $user_name, 'avatar' => $user_avatar,
                                    'creatin_time' => $user_creatin_time, 'user_posts_count' => $user_posts_count, 'user_subscribers' => $user_subscribers]);

print($layout_content);



/**
 * Отправляет запрос на чтение к таблицам post, user,content_type в текущей БД и возвращает Ассоциативный массив
 *
 * @param $con mysqli Объект-соединение с БД
 * @param $typeId int (может быть  null) id типа контента
 * @param $sort string (может быть  null) вид сортировки
 *
 * @return array Ассоциативный массив Результат запроса
 */
function dbGetPostsUser(mysqli $con, ?int $typeId, ?string $sort) : array
{
    $sql = "SELECT p.*, u.*, c.class
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
