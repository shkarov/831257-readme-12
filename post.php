<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

$post_id = getPostIdFromRequest($_GET, $_POST);

if (!dbFindPost($connect, $post_id)) {
    header("HTTP/1.0 404 Not Found");
    exit;
}

// повторный просмотр страницы
if (!isset($_GET['review']) && !isset($_POST['review'])) {
    addView($connect, $post_id, $user_id_login);
}

$post = dbGetPostWithUserInfo($connect, $post_id);

$errors = checkFormComment($_POST);

if (isset($_POST['comment']) && $errors === []) {
    if (addComment($connect, $_POST['post_id'], $user_id_login, $_POST['comment'])) {
        $url = "profile.php?user_id=".$post['user_id'];
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: '.$url);
    }
}

// проверка клика иконки лайк
checkLike($connect, $_GET, $user_id_login);

// проверка клика иконки repost
checkRepost($connect, $_GET, $user_id_login);

// автор поста
$user_id = $post['user_id'];

// проверка наличия подписки
$subscribe = dbFindSubscribe($connect, $user_id, $user_id_login);

// Нажата кнопка Подписаться/Отписаться на пользователя, профиль которого просматривается
if (isset($_GET['subscribeButton_onClick'])) {

    //пользователь существует и профиль НЕ текущего пользователя
    if (isValidUser($connect, $user_id, $user_id_login)) {
        if ($subscribe) {
            dbDelSubscribe($connect, $user_id, $user_id_login);
        } else {
            if (dbAddSubscribe($connect, $user_id, $user_id_login)) {
                sendEmail($config['smtp'], 'subscribe', [['email' => $post['email'], 'login' => $post['login']]], ['id' => $user_id_login, 'login' => $user_name_login]);
            };
        }
        $url = "profile.php?user_id="."$user_id";
        header('Location: '.$url);
    }
}

$comments = dbGetPostComments($connect, $post_id);

$page_content = include_template("post-details.php", ['post' => $post, 'comments' => $comments, 'user_id_login' => $user_id_login, 'user_avatar_login' => $user_avatar_login, 'errors' => $errors, 'subscribe' => $subscribe]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: публикация', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
