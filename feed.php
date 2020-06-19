<?php

require_once 'bootstrap.php';

session_start();

echo "id = ".$_SESSION['id']."\n";
echo 'login = '.$_SESSION['login']. "\n";
echo 'avatar = '. $_SESSION['avatar']. "\n";

/*
$is_auth = 1;

$user_name = $_SESSION['login'];
$user_avatar = $_SESSION['avatar'];

$types = dbGetTypes($connect);

$typeId = getTypeFromRequest($_GET);

$sort = getSortFromRequest($_GET);

$posts = dbGetPosts($connect, $typeId, $sort);


$page_content = include_template("myfeed.php", ['types' => $types, 'posts' => $posts, 'type_id' => $typeId, 'sort' => $sort]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: моя лента', 'user' => $user_name, 'is_auth' => $is_auth, 'avatar' => $user_avatar]);

print($layout_content);
*/
