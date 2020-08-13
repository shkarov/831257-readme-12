<?php

require_once 'bootstrap.php';

$errors = checkFormRegistration($connect, $_POST, $_FILES);

if ($errors === []) {
    $user_id = dbAddUser($connect, $_POST, $_FILES);
    if (!is_null($user_id)) {
        header('Location: /');
    }
}

$page_content = include_template("adding-user.php", ['errors' => $errors]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: регистрация']);

print($layout_content);
