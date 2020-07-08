<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];

// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    $post_id = (int) $_GET['post_id'];
    // нет такого лайка в БД
    if (!dbFindLike($connect, $post_id, $user_id)) {
        if (dbAddLike($connect, $post_id, $user_id)) {
            $referer = $_SERVER['HTTP_REFERER'];
            header('Location: '.$referer);
        }
    }
}

$types = dbGetTypes($connect);

$type_id = getTypeFromRequest($_GET);

$posts = dbGetPostsFeed($connect, $user_id, $type_id);

$page_content = include_template("myfeed.php", ['types' => $types, 'posts' => $posts, 'type_id' => $type_id]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: моя лента', 'user_id' => $user_id, 'user' => $user_name, 'avatar' => $user_avatar]);

print($layout_content);
