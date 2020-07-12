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
    $user_avatar = $user['avatar'];
    $user_creation_time = $user['creation_time'];
    $user_posts_count = $user['posts'];
    $user_subscribers = $user['subscribers'];

    // проверка наличия подписки
    $subscribe = dbFindSubscribe($connect, $user_id, $user_id_login);
}

// кликнута иконка лайк и профиль НЕ текущего пользователя
if (isset($_GET['like_onClick']) && $user_id != $user_id_login) {
    $post_id = (int) $_GET['post_id'];
    // нет такого лайка в БД
    if (!dbFindLike($connect, $post_id, $user_id)) {
        if (dbAddLike($connect, $post_id, $user_id)) {
            $referer = $_SERVER['HTTP_REFERER'];
            header('Location: '.$referer);
        }
    }
}

// Нажата кнопка Подписаться/Отписаться
if (isset($_GET['subscribeButton_onClick'])) {
    //профиль НЕ текущего пользователя
    if ($user_id != $user_id_login) {
        if ($subscribe) {
            dbDelSubscribe($connect, $user_id, $user_id_login);
        } else {
            dbAddSubscribe($connect, $user_id, $user_id_login);
        }
        $url = "profile.php?user_id="."$user_id";
        header('Location: '.$url);
    }
}

$tab = getTabFromRequest($_GET);

if ($tab === 'posts') {
$posts = dbGetUserPosts($connect, $user_id);
$page_content = include_template("profile-posts.php", ['posts' => $posts, 'user_id' => $user_id]);
}

if ($tab === 'likes') {
    $posts = dbGetUserPostsWithLikes($connect, $user_id);
    $page_content = include_template("profile-likes.php", ['posts' => $posts, 'user_id' => $user_id]);
}

if ($tab === 'subscribes') {
    $posts = dbGetUserSubscriptions($connect, $user_id);
    $page_content = include_template("profile-subscriptions.php", ['posts' => $posts, 'user_id' => $user_id]);
}

$page_stats = include_template("profile-stats.php", ['content' => $page_content, 'user_id' => $user_id, 'user' => $user_name, 'avatar' => $user_avatar, 'user_creation_time' => $user_creation_time,
                                'posts_count' => $user_posts_count, 'subscribers' => $user_subscribers, 'subscribe' => $subscribe, 'tab' => $tab]);

$layout_content = include_template("layout.php", ['content' => $page_stats, 'title' => 'readme: профиль', 'user_id' => $user_id_login, 'user' => $user_name_login, 'avatar' => $user_avatar_login]);

print($layout_content);
