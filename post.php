<?php

require_once 'bootstrap.php';

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
}

$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];

$postId = getPostIdFromRequest($_GET);

$post = dbGetSinglePost($connect, $postId);

if ($post === []) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$page_content = include_template("post-details.php", ['post' => $post]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name, 'avatar' => $user_avatar]);

print($layout_content);
