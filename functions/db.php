<?php

/**
 * Устанавливает соединение с базой данных(БД) и возвращает объект соединения
 *
 * @param array $conf Массив с параматрами для подключения к БД
 *
 * @return mysqli Объект-соединение с БД
*/
function dbConnect(array $conf) : mysqli
{
    $db_conf = $conf['db'];
    $con =  mysqli_connect($db_conf['host'], $db_conf['user'], $db_conf['password'], $db_conf['database']);
    if (!$con) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, "utf8");
    return($con);
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param mysqli $link Ресурс соединения
 * @param string $sql  SQL запрос с плейсхолдерами вместо значений
 * @param array  $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt(mysqli $link, string $sql, array $data = []) : mysqli_stmt
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else {
                if (is_string($value)) {
                    $type = 's';
                } else {
                    if (is_double($value)) {
                        $type = 'd';
                    }
                }
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Запись тегов поста в БД
 *
 * @param mysqli $con        Объект-соединение с БД
 * @param string $input_tags значение поля Теги формы
 * @param int    $post_id    id нового поста
 *
 * @return void
 */
function dbWriteTags(mysqli $con, string $input_tags, ?int $post_id ) : void
{
    $field_tags = trim($input_tags);

    if (!empty($field_tags)) {
        $tags = explode(' ', $field_tags);

        foreach ($tags as $key => $tag) {
            //поиск тага
            $sql_tag = "SELECT id FROM hashtag WHERE name = ?";
            $stmt_tag = mysqli_prepare($con, $sql_tag);
            mysqli_stmt_bind_param($stmt_tag, 's', $tag);
            mysqli_stmt_execute($stmt_tag);
            $res_tag = mysqli_stmt_get_result($stmt_tag);
            $rows_tag = mysqli_fetch_all($res_tag, MYSQLI_ASSOC);

            //такого тага ещё нет в базе
            if (empty($rows_tag)) {
                $sql_tag_new = 'INSERT hashtag (name) VALUES (?)';
                $stmt_tag_new = mysqli_prepare($con, $sql_tag_new);
                mysqli_stmt_bind_param($stmt_tag_new, 's', $tag);
                mysqli_stmt_execute($stmt_tag_new);
                $hashtag_id = mysqli_stmt_insert_id($stmt_tag_new);
                mysqli_stmt_close($stmt_tag_new);
            //такой таг уже есть в базе
            } else {
                $hashtag_id = $rows_tag[0]['id'];
            }

            $sql_post_tag = 'INSERT post_hashtag (post_id, hashtag_id) VALUES (?,?)';
            $stmt_post_tag = mysqli_prepare($con, $sql_post_tag);
            mysqli_stmt_bind_param($stmt_post_tag, 'ii', $post_id, $hashtag_id);
            mysqli_stmt_execute($stmt_post_tag);
            mysqli_stmt_close($stmt_post_tag);
        }
    }
}

/**
 * Удаление подписки в БД
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id_creator id пользователя, на которого подписались
 * @param int    $user_id_subscriber id подписчика
 *
 * @return bool
 */
function dbDelSubscribe(mysqli $con, int $user_id_creator, int $user_id_subscriber) : bool
{
    $sql1 = 'DELETE FROM subscription
             WHERE creator_user_id = ? AND subscriber_user_id = ?';

    $stmt1 = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt1, 'ii', $user_id_creator, $user_id_subscriber);

    $sql2 = 'UPDATE user
             SET    subscribers = subscribers - 1
             WHERE  id = ?';

    $stmt2 = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($stmt2, 'i', $user_id_creator);

    mysqli_begin_transaction($con);

    $result1 = mysqli_stmt_execute($stmt1);
    $result2 = mysqli_stmt_execute($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($con);
        return true;
      }
    mysqli_rollback($con);
    return false;
}
