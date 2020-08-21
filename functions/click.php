<?php

/*
//feed
// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    if (addLike($connect, $post_id, $user_id_login)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header('Location: '.$referer);
    }
}

//popular
// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    $post_id = getPostIdFromRequest($_GET);
    if (addLike($connect, $post_id, $user_id_login)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header('Location: '.$referer);
    }
}

//post
// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    if (addLike($connect, (int) $_GET['post_id'], $user_id_login)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header('Location: '.$referer);
    }
}

//profile
// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    if (addLike($connect, $post_id, $user_id_login)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header('Location: '.$referer);
    }
}

//search
// кликнута иконка лайк
if (isset($_GET['like_onClick'])) {
    $post_id = getPostIdFromRequest($_GET);
    if (addLike($connect, $post_id, $user_id_login)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header('Location: '.$referer);
    }
}
*/

function checkLike(mysqli $con, array $get, int $user_id) : void
{
    if (isset($get['like_onClick'])) {
        $post_id = getPostIdFromRequest($get);
        if (addLike($con, $post_id, $user_id)) {
            $url = $_SERVER['HTTP_REFERER'];
            header('Location: '.$url);
        }
    }
}

/*
//feed
// кликнута иконка repost
if (isset($_GET['repost_onClick'])) {
    if (addRepost($connect, $post_id, $user_id_login)) {
        $url = "profile.php?user_id=$user_id_login";
        header('Location: '.$url);
    }
}

//post
// кликнута иконка repost
if (isset($_GET['repost_onClick'])) {
    if (addRepost($connect, $post_id, $user_id_login)) {
        $url = "profile.php?user_id=$user_id_login";
        header('Location: '.$url);
    }
}

//profile
// кликнута иконка repost
if (isset($_GET['repost_onClick'])) {
    if (addRepost($connect, $post_id, $user_id_login)) {
        $url = "profile.php?user_id=$user_id_login";
        header('Location: '.$url);
    }
}
*/


function checkRepost(mysqli $con, array $get, int $user_id) : void
{
    if (isset($get['repost_onClick'])) {
        $post_id = getPostIdFromRequest($get);
        if (addRepost($con, $post_id, $user_id)) {
            $url = "profile.php?user_id=$user_id";
            header('Location: '.$url);
        }
    }
}


