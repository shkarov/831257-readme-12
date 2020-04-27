<?php

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
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
 * Устанавливает соединение с базой данных(БД) и возвращает объект соединения
 *
 * @param $host string Хост
 * @param $username string Имя пользователя БД
 * @param $psaaword string Пароль пользователя БД
 * @param $database string Имя БД
 *
 * @return $con object Объект-соединение с БД
 */
function dbConnect(string $host, string $username, string $password, string $database_name) : object
{
    $con =  mysqli_connect($host, $username, $password, $database_name);
    if (!$con) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, "utf8");
    return($con);
}

/**
 * Отправляет запрос на чтение к текущей БД и возвращает Ассоциативный массив
 *
 * @param $con object Объект-соединение с БД
 * @param $sql string Строка запроса
 *
 * @return  Ассоциативный массив
 */
function dbQuery(object $con, string $sql) : array
{
    $result = mysqli_query($con, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($con));
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
