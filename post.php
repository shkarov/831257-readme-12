<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

$post_id = getPostIdFromRequest($_GET, $_POST);

$post = dbGetPostWithUserInfo($connect, $post_id);

if ($post === []) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
$errors = checkFormComment($_POST);

if (isset($_POST['comment']) && $errors === []) {

    if (dbAddComment($connect, $user_id_login, $_POST)) {
        $url = "profile.php?user_id=".$post['user_id'];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
    }
}

$comments = dbGetPostComments($connect, $post_id);

// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    addLike($connect, (int) $_GET['post_id'], $user_id_login);
    $referer = $_SERVER['HTTP_REFERER'];
    header('Location: '.$referer);
}

// кликнута иконка repost
if (isset($_GET['repost_onClick'])) {
    addRepost($connect, $post_id, $user_id_login);
    $url = "profile.php?user_id=$user_id_login";
    header('Location: '.$url);
}

$page_content = include_template("post-details.php", ['post' => $post, 'comments' => $comments, 'user_login_id' => $user_id_login, 'user_login_avatar' => $user_avatar_login, 'errors' => $errors]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
