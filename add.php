<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

$types = dbGetTypes($connect);

$typeId = getTypeFromRequest($_GET, $_POST);

$errors = checkForm($_POST, $_FILES);

$page_content = include_template("adding-post.php", ['types' => $types, 'type_id' => $typeId, 'errors' => $errors]);

if ($errors === []) {
    $postId = dbAddPost($connect, $user_id_login, $_POST, $_FILES);
    if (!is_null($postId)) {

        $post_header = dbGetPostHeader($connect,$postId);

        $subscribers_list = dbGetUserSubscribers($connect, $user_id_login);

        sendEmail($config['smtp'], 'post', $subscribers_list, ['id' => $user_id_login, 'login' => $user_name_login], $post_header);

        $url = "post.php?post_id="."$postId";
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
        exit();
    }
}

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
