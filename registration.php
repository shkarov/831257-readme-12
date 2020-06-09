<?php

require_once 'bootstrap.php';

$is_auth = 0;

$errors = checkFormRegistration($connect, $_POST, $_FILES);

if ($errors === []) {
    $userId = dbAddUser($connect, $_POST, $_FILES);
    if (!is_null($userId)) {
        header('Location: index.php');
        exit();
    }
}

$page_content = include_template("adding-user.php", ['errors' => $errors]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: регистрация', 'is_auth' => $is_auth]);

print($layout_content);
