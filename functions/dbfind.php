<?php

/**
 * Отправляет запрос на поиск записи с полем $email к таблице user
 *
 * @param mysqli $con   Объект-соединение с БД
 * @param string $email адрес электронной почты из формы регистрации
 *
 * @return bool
 */
function dbFindEmail(mysqli $con, string $email) : bool
{
    $sql = "SELECT id FROM user WHERE email = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_rows = mysqli_num_rows($result);

    return $result_rows === 0 ? false : true;
}

/**
 * Отправляет запрос на поиск записи с полем $login к таблице user
 *
 * @param mysqli $con   Объект-соединение с БД
 * @param string $login логин пользователя из формы регистрации
 *
 * @return bool
 */
function dbFindLogin(mysqli $con, string $login) : bool
{
    $sql = "SELECT id FROM user WHERE login = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_rows = mysqli_num_rows($result);

    return $result_rows === 0 ? false : true;
}

/**
 * Отправляет запрос на поиск записи с полем $id к таблице post
 *
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $post_id id поста
 *
 * @return bool
 */
function dbFindPost(mysqli $con, int $post_id) : bool
{
    $sql = "SELECT id FROM post WHERE id = ?";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $post_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_rows = mysqli_num_rows($result);

    return $result_rows === 0 ? false : true;
}

/**
 * Ищет лайк в БД
 *
 * @param mysqli $con     Объект-соединение с БД
 * @param int    $post_id id поста
 * @param int    $user_id id пользователя
 *
 * @return bool Возвращает false если лайк не найден, иначе true
 */
function dbFindLike(mysqli $con, int $post_id, int $user_id) : bool
{
    $sql = "SELECT COUNT(*) AS `is_like`
            FROM   `like`
            WHERE  post_id = $post_id AND user_id = $user_id";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_array = mysqli_fetch_assoc($result);

    return ($result_array['is_like'] == 0) ? false : true;
}

/**
 * Ищет подписку в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id_creator id пользователя, на которого подписались
 * @param int    $user_id_subscriber id подписчика
 *
 * @return bool Возвращает false усли подписка не найдена, иначе true
 */
function dbFindSubscribe(mysqli $con, int $user_id_creator, int $user_id_subscriber) : bool
{
    $sql = "SELECT COUNT(*) AS `is_subscribe`
            FROM   subscription
            WHERE  creator_user_id = $user_id_creator AND subscriber_user_id = $user_id_subscriber";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        exit("Ошибка MySQL: " . mysqli_error($con));
    }

    $result_array = mysqli_fetch_assoc($result);

    return ($result_array['is_subscribe'] == 0) ? false : true;
}
