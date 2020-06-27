<?php

require_once 'bootstrap.php';

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
}

$user_id = $_SESSION['id'];
$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];

$types = dbGetTypes($connect);

$typeId = getTypeFromRequest($_GET);

$posts = dbGetPostsFeed($connect, $user_id, $typeId);

$page_content = include_template("myfeed.php", ['types' => $types, 'posts' => $posts, 'type_id' => $typeId]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: моя лента', 'user' => $user_name, 'avatar' => $user_avatar]);

print($layout_content);
