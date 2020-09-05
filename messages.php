<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

$contacts = getContactsMessages($connect, $user_id_login);

$user_id = filter_input(INPUT_GET, 'user_id');
if (is_null($user_id)) {
    $user_id = filter_input(INPUT_POST, 'user_id');
}


if (!is_null($user_id)) {
    if (!isValidUser($connect, $user_id, $user_id_login)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header('Location: '.$referer);
    }
    if (!isUserPresentInList($contacts, $user_id)) {
        $contacts = addUserInList($connect, $contacts, $user_id);
    }
}

$messages = empty($user_id) ? [] : dbGetMessages($connect, $user_id, $user_id_login);

$errors = checkFormMessage($_POST);

if (isset($_POST['message']) && $errors === []) {

    if (dbAddMessage($connect, $_POST, $user_id_login)) {
        $url = "messages.php?user_id=$user_id";
        header('Location: '.$url);
    }
}

$page_content = include_template("messages-details.php", ['contacts' => $contacts, 'messages' => $messages, 'user_id_active' => $user_id, 'user_id_login' => $user_id_login, 'user_avatar_login' => $user_avatar_login, 'errors' => $errors]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: сообщения', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
