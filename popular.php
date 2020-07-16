<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    addLike($connect, (int) $_GET['post_id'], $user_id_login);
    $referer = $_SERVER['HTTP_REFERER'];
    header('Location: '.$referer);
};

$types = dbGetTypes($connect);

$type_id = getTypeFromRequest($_GET);

$sort = getSortFromRequest($_GET);

// количество постов на странице
$limit_posts_per_page = 6;

//количество страниц для полного вывода результата запроса
$count_pages = (int) ceil(dbGetPostsPopularCount($connect, $type_id, $sort) / $limit_posts_per_page);

// номер страницы
$page = 1;
if (isset($_GET['page'])) {
    if ((int) $_GET['page'] > $count_pages) {
        $page = $count_pages;
    } elseif ((int) $_GET['page'] > 1) {
        $page = (int) $_GET['page'];
    }
}

$posts = dbGetPostsPopular($connect, $type_id, $sort, $page, $limit_posts_per_page);

$page_content = include_template("main.php", ['types' => $types, 'posts' => $posts, 'type_id' => $type_id, 'sort' => $sort, 'page' => $page]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
