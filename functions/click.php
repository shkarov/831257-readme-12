<?php

/**
 * Проверка клика иконки лайк
 * Если пост лайкнут, производит запись лайка в БД
 *
 * @param mysqli $con  Объект-соединение с БД
 * @param array  $get Ассоциативный массив, переданный методом get
 * @param int    $user_id id залогиненного пользователя
 *
 * @return void
 */
function checkLike(mysqli $con, array $get, int $user_id) : void
{
    if (isset($get['like_onClick'])) {
        $post_id = filter_input(INPUT_GET, 'post_id');
        if (addLike($con, $post_id, $user_id)) {
            $url = $_SERVER['HTTP_REFERER'];
            header('Location: '.$url);
        }
    }
}

/**
 * Проверка клика иконки репост
 * Если иконка репоста кликнута, производит запись репоста в БД
 *
 * @param mysqli $con  Объект-соединение с БД
 * @param array  $get Ассоциативный массив, переданный методом get
 * @param int    $user_id id залогиненного пользователя
 *
 * @return void
 */
function checkRepost(mysqli $con, array $get, int $user_id) : void
{
    if (isset($get['repost_onClick'])) {
        $post_id = filter_input(INPUT_GET, 'post_id');
        if (addRepost($con, $post_id, $user_id)) {
            $url = "profile.php?user_id=$user_id";
            header('Location: '.$url);
        }
    }
}
