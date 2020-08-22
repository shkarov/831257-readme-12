<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

// проверка клика иконки лайк
checkLike($connect, $_GET, $user_id_login);

// проверка клика иконки repost
checkRepost($connect, $_GET, $user_id_login);

$types = dbGetTypes($connect);

$type_id = getTypeFromRequest($_GET);

$posts = dbGetPostsFeed($connect, $user_id_login, $type_id);

$page_content = include_template("myfeed.php", ['types' => $types, 'posts' => $posts, 'type_id' => $type_id]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: моя лента', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
