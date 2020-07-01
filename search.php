<?php

require_once 'bootstrap.php';

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];

$posts = dbGetPostsSearch($connect, $_GET);

$template = ($posts === []) ? "no-results.php" : "search-results.php";

$page_content = include_template($template, ['posts' => $posts, 'search_string' => $_GET['search_string']]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: страница результатов поиска', 'user' => $user_name, 'avatar' => $user_avatar]);

print($layout_content);
