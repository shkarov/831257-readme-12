<?php

/**
 * Запись нового поста в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id  пользователя
 * @param array  $post глобальный массив $_POST
 * @param array  $files глобальный массив $_FILES
 *
 * @return int возвращает id добавленного поста либо null
 */
function addPost(mysqli $con, int $user_id, array $post, array $files) : ?int
{
    if ($post === []) {
        return null;
    }
    $type_id = $post['type_id'];

    switch ($type_id) {
        case 1:
            return dbAddPostPhoto($con, $user_id, $post, $files);
        case 2:
            return dbAddPostVideo($con, $user_id, $post);
        case 3:
            return dbAddPostText($con, $user_id, $post);
        case 4:
            return dbAddPostQuote($con, $user_id, $post);
        case 5:
            return dbAddPostLink($con, $user_id, $post);
        default:
            return null;
    }
}

/**
 * Проверяет условия и отправляет запрос на добавление комментария к посту
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $user_id_login id пользователя, открывшего текущую сессию
 * @param string $comment текст комментария
 *
 * @return bool
 */
function addComment(mysqli $con, int $post_id, int $user_id_login, string $comment) : bool
{
    // пост существует
    if (dbFindPost($con, $post_id)) {
        //автор поста
        $user_id = dbGetUserIdFromPost($con, $post_id);
        // залогиненый пользователь комментирует не свой пост
        if ($user_id != $user_id_login) {
            return dbAddComment($con, $post_id, $user_id_login, $comment);
        }
    }
    return false;
}

/**
 * Проверяет условия и отправляет запрос на добавление лайка к посту
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $user_id_login id пользователя, открывшего текущую сессию
 *
 * @return bool
 */
function addLike(mysqli $con, int $post_id, int $user_id_login) : bool
{
    // пост существует
    if (dbFindPost($con, $post_id)) {
        // залогиненый пользователь лайкает не свой пост
        if (dbGetUserIdFromPost($con, $post_id) != $user_id_login) {

            // нет такого лайка в БД
            if (!dbFindLike($con, $post_id, $user_id_login)) {
                return dbAddLike($con, $post_id, $user_id_login);
            }
        }
    }
    return false;
}

/**
 * Проверяет условия и отправляет запрос на прирост счетчика просмотра поста
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $user_id_login id пользователя, открывшего текущую сессию
 *
 * @return bool
 */
function addView(mysqli $con, int $post_id, int $user_id_login) : bool
{
    // пост существует
    if (dbFindPost($con, $post_id)) {
        // залогиненый пользователь смотрит не свой пост
        if (dbGetUserIdFromPost($con, $post_id) != $user_id_login) {
            return dbAddView($con, $post_id);
        }
    }
    return false;
}

/**
 * Проверяет условия и отправляет запрос на добавление подписки на пользователя
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id пользователя, на которого подписываются
 * @param int    $user_id_login id пользователя, открывшего текущую сессию
 *
 * @return bool
 */
function addSubscribe(mysqli $con, int $user_id, int $user_id_login) : bool
{
    // залогиненый пользователь подписывается НЕ на себя
    if ($user_id != $user_id_login) {

        // нет такой подписки в БД
        if (!dbFindSubscribe($con, $user_id, $user_id_login)) {
            return dbAddSubscribe($con, $user_id, $user_id_login);
        }
    }
    return false;
}

/**
 * Проверяет условия и отправляет запрос на добавление лайка к посту
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $user_id_login id пользователя, открывшего текущую сессию
 *
 * @return bool
 */
function addRepost(mysqli $con, int $post_id, int $user_id_login) : bool
{
    $post = dbGetPost($con, $post_id);

    // пост найден
    if (!empty($post)) {
        // пользователь найден и залогиненый пользователь репостит не свой пост
        if (isValidUser($con, $post['user_id'], $user_id_login)) {
            return dbAddRepost($con, $post, $user_id_login);
        }
    }
    return false;
}
