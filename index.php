<?php

require_once 'bootstrap.php';

$errors = login($connect, $_POST);

if (isset($_SESSION['login'])) {
    header('Location: feed.php');
}

$layout_content = include_template("start.php", ['errors' => $errors]);
print($layout_content);
