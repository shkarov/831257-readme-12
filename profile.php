<?php

require_once 'bootstrap.php';

if (!isset($_SESSION['login'])) {
    header('Location: /');
}

$user_id_login = $_SESSION['id'];
$user_name_login = $_SESSION['login'];
$user_avatar_login = $_SESSION['avatar'];

$user = dbGetUserById($connect, (int) $_GET['user_id']);

//профиль текущего пользователя
if ($user['id'] === $user_id_login) {
    $user_id = $_SESSION['id'];
    $user_name = $_SESSION['login'];
    $user_avatar = $_SESSION['avatar'];
    $user_creation_time = $_SESSION['creation_time_user'];
    $user_posts_count = $_SESSION['posts'];
    $user_subscribers = $_SESSION['subscribers'];
    $subscribe = false;
} else {
    //профиль НЕ текущего пользователя
    $user_id = $user['id'];
    $user_name = $user['login'];
    $user_email = $user['email'];
    $user_avatar = $user['avatar'];
    $user_creation_time = $user['creation_time'];
    $user_posts_count = $user['posts'];
    $user_subscribers = $user['subscribers'];

    // проверка наличия подписки
    $subscribe = dbFindSubscribe($connect, $user_id, $user_id_login);
}

// проверка клика иконки лайк
checkLike($connect, $_GET, $user_id_login);

// проверка клика иконки repost
checkRepost($connect, $_GET, $user_id_login);

// Нажата кнопка Подписаться/Отписаться на пользователя, профиль которого просматривается
if (isset($_GET['subscribeButton_onClick'])) {

    //пользователь существует и профиль НЕ текущего пользователя
    if (isValidUser($connect, $user_id, $user_id_login)) {
        if ($subscribe) {
            dbDelSubscribe($connect, $user_id, $user_id_login);
        } else {
            if (dbAddSubscribe($connect, $user_id, $user_id_login)) {
                sendEmail($config['smtp'], 'subscribe', [['email' => $user_email, 'login' => $user_name]], ['id' => $user_id_login, 'login' => $user_name_login]);
            };
        }
        $url = "profile.php?user_id="."$user_id";
        header('Location: '.$url);
    }
}

// Нажата кнопка Подписаться/Отписаться на пользователя, который подписан на пользователя, профиль которого просматривается
if (isset($_GET['subscribeButtonMutual_onClick'])) {
    $user_id_for_subscribe = (int) $_GET['user_id_subscriber'];

    //пользователь существует и профиль НЕ текущего пользователя
    if (isValidUser($connect, $user_id_for_subscribe, $user_id_login)) {
        if ($_GET['subscribeButtonMutual_onClick'] === 'del') {
            dbDelSubscribe($connect, $user_id_for_subscribe, $user_id_login);
        }
        if ($_GET['subscribeButtonMutual_onClick'] === 'add') {
            if (addSubscribe($connect, $user_id_for_subscribe, $user_id_login)) {
                $user_for_subscribe = dbGetUserById($connect, $user_id_for_subscribe);
                sendEmail($config['smtp'], 'subscribe', [['email' => $user_for_subscribe['email'], 'login' => $user_for_subscribe['login']]], ['id' => $user_id_login, 'login' => $user_name_login]);
            }
        }
        $url = "profile.php?user_id="."$user_id_for_subscribe";
        header('Location: '.$url);
    }
}

$tab = getTabFromRequest($_GET);

switch ($tab) {
    case 'posts':
        $posts = dbGetUserPosts($connect, $user_id);
        $page_content = include_template("profile-posts.php", ['posts' => $posts, 'user_id' => $user_id]);
        break;
    case 'likes':
        $posts = dbGetUserPostsWithLikes($connect, $user_id);
        $page_content = include_template("profile-likes.php", ['posts' => $posts, 'user_id' => $user_id]);
        break;
    case 'subscribes':
        $posts = dbGetUserSubscribersWithMutualSubscription($connect, $user_id, $user_id_login);
        $page_content = include_template("profile-subscriptions.php", ['posts' => $posts, 'user_id' => $user_id, 'user_id_login' => $user_id_login]);
        break;
}

$page_stats = include_template("profile-stats.php", ['content' => $page_content, 'user_id' => $user_id, 'user' => $user_name, 'avatar' => $user_avatar, 'user_creation_time' => $user_creation_time,
                                'posts_count' => $user_posts_count, 'subscribers' => $user_subscribers, 'subscribe' => $subscribe, 'tab' => $tab, 'user_id_login' => $user_id_login]);

$layout_content = include_template("layout.php", ['content' => $page_stats, 'title' => 'readme: профиль', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
