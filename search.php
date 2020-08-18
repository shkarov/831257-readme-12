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
    $post_id = getPostIdFromRequest($_GET);
    if (addLike($connect, $post_id, $user_id_login)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header('Location: '.$referer);
    }
}

$posts = isset($_GET['search_string']) ? getPostsSearch($connect, $_GET['search_string']) : [];

$template = ($posts === []) ? "no-results.php" : "search-results.php";

$page_content = include_template($template, ['posts' => $posts, 'search_string' => $_GET['search_string']]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: страница результатов поиска', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
