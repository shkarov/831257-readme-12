<?php

require_once 'bootstrap.php';

$is_auth = rand(0, 1);

$user_name = 'Boris';
$userId = 1;

$types = dbGetTypes($connect);

$typeId = getTypeFromRequest($_GET, $_POST);

$errors = checkForm($_POST, $_FILES);

$page_content = include_template("adding-post.php", ['types' => $types, 'type_id' => $typeId, 'errors' => $errors]);

if ($errors === []) {
    $postId = dbAddPost($connect, $userId, $_POST, $_FILES);
    if (!is_null($postId)) {
        $post = dbGetSinglePost($connect, $postId);
        if ($post === []) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
        $page_content = include_template("post-details.php", ['post' => $post]);
    }
}

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name, 'is_auth' => $is_auth]);

print($layout_content);
