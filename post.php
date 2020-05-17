<?php

require_once 'bootstrap.php';

$is_auth = rand(0, 1);

$user_name = 'Boris';

$postId = getPostIdFromRequest($_GET);

$post = dbGetSinglePost($connect, $postId);

if ($post === []) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

$page_content = include_template("post-details.php", $post);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name, 'is_auth' => $is_auth]);

print($layout_content);
