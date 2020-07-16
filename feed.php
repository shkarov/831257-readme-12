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

$posts = dbGetPostsFeed($connect, $user_id_login, $type_id);

$page_content = include_template("myfeed.php", ['types' => $types, 'posts' => $posts, 'type_id' => $type_id]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: моя лента', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
