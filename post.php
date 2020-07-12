<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];

$post_id = getPostIdFromRequest($_GET, $_POST);

$post = dbGetSinglePost($connect, $post_id);

if ($post === []) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$errors = checkFormComment($_POST);

if (isset($_POST['comment']) && $errors === []) {

    if (dbAddComment($connect, $user_id, $_POST)) {
        $url = "profile.php?user_id=".$post['user_id'];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
    }
}

$comments = dbGetPostComments($connect, $post_id);

// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {

    // нет такого лайка в БД
    if (!dbFindLike($connect, $post_id, $user_id)) {
        if (dbAddLike($connect, $post_id, $user_id)) {
            $referer = $_SERVER['HTTP_REFERER'];
            header('Location: '.$referer);
        }
    }
}

$page_content = include_template("post-details.php", ['post' => $post, 'comments' => $comments, 'user_login_id' => $user_id, 'user_login_avatar' => $user_avatar, 'errors' => $errors]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user_id' => $user_id, 'user' => $user_name, 'avatar' => $user_avatar]);

print($layout_content);
