<?php

require_once 'bootstrap.php';

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
}

$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];

$types = dbGetTypes($connect);

$typeId = getTypeFromRequest($_GET);

$sort = getSortFromRequest($_GET);

$posts = dbGetPosts($connect, $typeId, $sort);

$page_content = include_template("main.php", ['types' => $types, 'posts' => $posts, 'type_id' => $typeId, 'sort' => $sort]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name, 'avatar' => $user_avatar]);

print($layout_content);
