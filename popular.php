<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

// проверка клика иконки лайк
checkLike($connect, $_GET, $user_id_login);

$types = dbGetTypes($connect);

$type_id = getTypeFromRequest($_GET);

$sort = getSortFromRequest($_GET);

// количество постов на странице
$limit_posts_per_page = 6;

//количество страниц для полного вывода результата запроса
$count_pages = (int) ceil(dbGetPostsPopularCount($connect, $type_id, $sort) / $limit_posts_per_page);

// номер страницы для пагинации
$page = getPageNumber($_GET, $count_pages);

$posts = dbGetPostsPopular($connect, $type_id, $sort, $page, $limit_posts_per_page);

$page_content = include_template("main.php", ['types' => $types, 'posts' => $posts, 'type_id' => $type_id, 'sort' => $sort, 'page' => $page, 'count_pages' => $count_pages]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
