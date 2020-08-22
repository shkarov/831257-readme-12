<?php

/**
 * Функция проверяет доступно ли видео по ссылке на youtube
 * @param string $url ссылка на видео
 *
 * @return bool
 */
function check_youtube_link(string $url) : bool
{
    $result = true;
    $id = extract_youtube_id($url);
    $headers = get_headers('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $id);
    if (!is_array($headers)) {
        $result = false;
    }
    $err_flag = strpos($headers[0], '200') ? 200 : 404;
    if ($err_flag !== 200) {
        $result = false;
    }
    return $result;
}

/**
 * Валидация полей формы, перенаправление к валидации формы конкретного типа контента
 *
 * @param array $post глобальный массив $_POST
 * @param array $files глобальный массив $_FILES
 *
 * @return array вызов функции для проверки формы данного типа контента, возвращающей массив ошибок
 */
function checkForm(array $post, array $files) : array
{
    if ($post === []) {
        return $post;
    }
    $type_id = $post['type_id'];

    switch ($type_id) {
        case 1:
            return checkPhotoForm($post, $files);
        case 2:
            return checkVideoForm($post);
        case 3:
            return checkTextForm($post);
        case 4:
            return checkQuoteForm($post);
        case 5:
            return checkLinkForm($post);
        default:
            return checkPhotoForm($post);
    }
}

/**
 * Валидация полей формы, контент Фото
 *
 * @param array $post глобальный массив $_POST
 * @param array $files глобальный массив $_FILES
 *
 * @return array массив ошибок
 */
function checkPhotoForm(array $post, array $files = []) : array
{
    $errors = [];
    foreach ($post as $key => $value) {
        switch ($key) {
            case 'photo-heading':
                $errors[$key] = validateHeading($value);
                break;
            case 'photo-url':
                $errors[$key] = validateFile($value, $files);
                break;
            case 'photo-tags':
                $errors[$key] = validateTag($value);
                break;
        }
    }
    return array_filter($errors);
}

/**
 * Валидация полей формы, контент Видео
 *
 * @param array $arr Ассоциативный массив, переданный из формы методом post
 *
 * @return array массив ошибок
 */
function checkVideoForm(array $arr) : array
{
    $errors = [];
    foreach ($arr as $key => $value) {
        switch ($key) {
            case 'video-heading':
                $errors[$key] = validateHeading($value);
                break;
            case 'video-url':
                $errors[$key] = validateYoutubeLink($value);
                break;
            case 'video-tags':
                $errors[$key] = validateTag($value);
                break;
        }
    }
    return array_filter($errors);
}

/**
 * Валидация полей формы, контент Текст
 *
 * @param array $arr Ассоциативный массив, переданный из формы методом post
 *
 * @return array массив ошибок
 */
function checkTextForm(array $arr) : array
{
    $errors = [];
    foreach ($arr as $key => $value) {
        switch ($key) {
            case 'text-heading':
                $errors[$key] = validateHeading($value);
                break;
            case 'text-text':
                $errors[$key] = validateText($value);
                break;
            case 'text-tags':
                $errors[$key] = validateTag($value);
                break;
        }
    }
    return array_filter($errors);
}

/**
 * Валидация полей формы, контент Цитата
 *
 * @param array $arr Ассоциативный массив, переданный из формы методом post
 *
 * @return array массив ошибок
 */
function checkQuoteForm(array $arr) : array
{
    $errors = [];
    foreach ($arr as $key => $value) {
        switch ($key) {
            case 'quote-heading':
                $errors[$key] = validateHeading($value);
                break;
            case 'quote-text':
                $errors[$key] = validateText($value);
                break;
            case 'quote-author':
                $errors[$key] = validateAuthor($value);
                break;
            case 'quote-tags':
                $errors[$key] = validateTag($value);
                break;
        }
    }
    return array_filter($errors);
}

/**
 * Валидация полей формы, контент Ссылка
 *
 * @param array $arr Ассоциативный массив, переданный из формы методом post
 *
 * @return array массив ошибок
 */
function checkLinkForm(array $arr) : array
{
    $errors = [];
    foreach ($arr as $key => $value) {
        switch ($key) {
            case 'link-heading':
                $errors[$key] = validateHeading($value);
                break;
            case 'link-url':
                $errors[$key] = validateUrl($value);
                break;
            case 'link-tags':
                $errors[$key] = validateTag($value);
                break;
        }
    }
    return array_filter($errors);
}

/**
 * Проверка строки на заполненность
 * @param string $name
 *
 * @return bool
 */
function validateFilled(?string $name) : bool
{
    return empty(trim($name));
}

/**
 * Проверка поля формы "Заголовок"
 *
 * @param string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateHeading(?string $name) : array
{
    $error = [];
    $type = "Заголовок.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите наименование вашей публикации.";
    }
    return $error;
}

/**
 * Проверка поля формы "Теги"
 *
 * @param string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateTag(?string $name) : array
{
    if (empty(trim($name))) {
        return [];
    }
    $error = [];
    $type = "Теги.";

    $arr = explode(' ', trim($name));

    foreach ($arr as $key => $value) {
        if (!preg_match("/^[a-zа-я]+$/iu", $value)) {
            $error['report'] = $type."В этом поле допустимы только слова.";
            $error['header'] = "Ошибка ввода данных.";
            $error['description'] = "В качестве тегов допустимы отдельные слова из русских и латинских букв.";
        }
    }
    return $error;
}

/**
 * Проверка поля формы "Файл и Ссылка из интернета"
 *
 * @param string $url ссылка на файл в сети
 * @param array $files глобальный массив $_FILES
 *
 * @return array массив сообшений об ошибках
 */
function validateFile(?string $url, array $files = []) : array
{
    $error = [];

    $file = $files['userpic-file-photo'];

    if (!empty($file['name'])) {
        $type = 'Выбор файла.';
        if (!($file['type'] === 'image/jpeg' || $file['type'] === 'image/png' || $file['type'] === 'image/gif')) {
            $error['report'] = $type."Допустимый тип файла JPEG, PNG, GIF.";
            $error['header'] = "Выбран недопустимый тип файла.";
            $error['description'] = "Выберите файл формата JPEG, PNG или GIF.";
        }
        return $error;
    }

    $type = 'Ссылка из интернета.';
    if (validateFilled($url)) {
        $error['report'] = $type."Необходимо выбрать файл на локальном ПК или указать ссылку на файл в интернете.";
        $error['header'] = "Это поле должно быть заполнено.";
        $error['description'] = "Введите ссылку на файл в интернете или выберите файл на локальном ПК.";
        return $error;
    }
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error['report'] = $type."Ошибка URL.";
        $error['header'] = "Ошибочная ссылка.";
        $error['description'] = "Введите корректный адрес ссылки.";
        return $error;
    }

    $file_headers = get_headers($url);
    if (!strpos($file_headers[0], '200')) {
        $error['report'] = $type."Файл не найден.";
        $error['header'] = "Ошибка загрузки.";
        $error['description'] = "Не удалось найти файл по указанному адресу.";
        return $error;
    }

    $content_type = '';
    foreach ($file_headers as $value) {
        if (mb_strpos($value, 'Content-Type:') !== false) {
            $content_type = ltrim(mb_substr($value, -10));
        }
    }

    if (!($content_type === 'image/jpeg' || $content_type === 'image/png' || $content_type === 'image/gif')) {
        $error['report'] = $type. "Допустимый тип файла JPEG, PNG, GIF.";
        $error['header'] = "Выбран недопустимый тип файла.";
        $error['description'] = "Выберите файл формата JPEG, PNG или GIF.";
        return $error;
    }
    if (!file_get_contents($url)) {
        $error['report'] = $type."Ошибка загрузки файла.";
        $error['header'] = "Ошибка загрузки.";
        $error['description'] = "Не удалось загрузить файл с указанного адреса.";
    }

    return $error;
}

/**
 * Проверка поля формы "Youtube ссылка"
 * @param string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateYoutubeLink(?string $name) : array
{
    $error = [];
    $type = "Ссылка youtube.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите адрес ссылки на видеоролик.";
        return $error;
    }
    if (!filter_var($name, FILTER_VALIDATE_URL)) {
        $error['report'] = $type."Ошибка URL.";
        $error['header'] = "Ошибочная ссылка.";
        $error['description'] = "Введите корректный адрес ссылки на видеоролик.";
        return $error;
    }
    if (!check_youtube_link($name)) {
        $error['report'] = $type."Видео не доступно.";
        $error['header'] = "Ошибка доступа.";
        $error['description'] = "Указанный видеоролик недоступен. Укажите ссылку на видеоролик с публичным доступом";
    }
    return $error;
}

/**
 * Проверка поля формы "Текст"
 * @param string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateText(?string $name) : array
{
    $error = [];
    $type = "Текст.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите текст сообщения.";
    }
    return $error;
}

/**
 * Проверка поля формы "Автор цитаты"
 *
 * @param string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateAuthor(?string $name) : array
{
    $error = [];
    $type = "Автор.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите имя автора цитаты.";
    }
    return $error;
}

/**
 * Проверка поля формы "URL ссылка"
 *
 * @param string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateUrl(?string $name) : array
{
    $error = [];
    $type = "Ссылка.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите адрес ссылки.";
        return $error;
    }
    if (!filter_var($name, FILTER_VALIDATE_URL)) {
        $error['report'] = $type."Ошибка URL.";
        $error['header'] = "Ошибочная ссылка.";
        $error['description'] = "Введите корректный адрес ссылки.";
    }
    return $error;
}

/**
 * Валидация полей формы, перенаправление к валидации формы конкретного типа контента
 *
 * @param mysqli $con Объект-соединение с БД
 * @param array  $post глобальный массив $_POST
 * @param array  $files глобальный массив $_FILES
 *
 * @return array вызов функции для проверки формы данного типа контента, возвращающей массив ошибок
 */
function checkFormRegistration(mysqli $con, array $post, array $files) : array
{
    if ($post === []) {
        return $post;
    }
    $errors = [];
    foreach ($post as $key => $value) {
        switch ($key) {
            case 'email':
                $errors[$key] = validateEmail($con, $value);
                break;
            case 'login':
                $errors[$key] = validateLogin($con, $value);
                break;
            case 'password':
                $errors[$key] = validatePassword($value);
                break;
            case 'password-repeat':
                $errors[$key] = validatePasswordRepeat($value, $post['password']);
                break;
        }
    }

    $errors[key($files)] = validateFileAvatar($files);

    return array_filter($errors);
}

/**
 * Проверка поля формы "Электронная почта"
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateEmail(mysqli $con, ?string $name) : array
{
    $error = [];
    $type = "Электронная почта.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите адрес электронной почты.";
        return $error;
    }
    if (!filter_var($name, FILTER_VALIDATE_EMAIL)) {
        $error['report'] = $type."Ошибка адреса электронной почты.";
        $error['header'] = "Ошибочный email.";
        $error['description'] = "Введите корректный адрес электронной почты.";
        return $error;
    }
    if (dbFindEmail($con, $name)) {
        $error['report'] = $type."Этот адрес электронной почты уже присутствует в нашей базе.";
        $error['header'] = "Этот email уже используется.";
        $error['description'] = "Введите другой адрес электронной почты.";
    }
    return $error;
}

/**
 * Проверка поля формы "Логин"
 *
 * @param mysqli $con  Объект-соединение с БД
 * @param string $name Содержание поля формы
 *
 * @return array массив сообшений об ошибках
 */
function validateLogin(mysqli $con, ?string $name) : array
{
    $error = [];
    $type = "Логин.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите ваш логин.";
        return $error;
    }
    if (dbFindLogin($con, $name)) {
        $error['report'] = $type."Этот логин уже присутствует в нашей базе.";
        $error['header'] = "Этот логин уже используется.";
        $error['description'] = "Введите другой логин.";
    }
    return $error;
}

/**
 * Проверка поля формы "Пароль"
 *
 * @param string $name Содержание поля формы
 *
 * @return array массив сообшений об ошибках
 */
function validatePassword(?string $name) : array
{
    $error = [];
    $type = "Пароль.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите ваш пароль.";
    }
    return $error;
}

/**
 * Проверка поля формы "Повтор пароля"
 *
 * @param string $name строка для сравнения
 * @param string $nameOrigin строка оригинал
 *
 * @return array массив сообшений об ошибках
 */
function validatePasswordRepeat(?string $name, ?string $nameOrigin) : array
{
    $error = [];
    $type = "Повтор пароля.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите ваш пароль.";
        return $error;
    }
    if ($name !== $nameOrigin) {
        $error['report'] = $type."Значение в этом поле не совпадает со значением в поле 'Пароль'.";
        $error['header'] = "Ошибка ввода данных.";
        $error['description'] = "Введите значение, идентичное значению в поле 'Пароль'.";
    }
    return $error;
}

/**
 * Проверка поля формы "Выбор аватара"
 * @param  array $files глобальный массив $_FILES
 *
 * @return array массив сообшений об ошибках
 */
function validateFileAvatar(array $files) : array
{
    $error = [];

    $file = $files[key($files)];
    if (!empty($file['name'])) {
        $file_type = mime_content_type($file['tmp_name']);
        $type = 'Выбор файла.';
        if (!($file_type === 'image/jpeg' || $file_type === 'image/png' || $file_type === 'image/gif')) {
            $error['report'] = $type."Допустимый тип файла JPEG, PNG, GIF.";
            $error['header'] = "Выбран недопустимый тип файла.";
            $error['description'] = "Выберите файл формата JPEG, PNG или GIF.";
        }
    }
    return $error;
}

/**
 * Аутентификация пользователя
 *
 * @param mysqli $con  Объект-соединение с БД*
 * @param array  $post массив данных формы
 *
 * @return array  массив сообшений об ошибках
 */
function login(mysqli $con, array $post) : array
{
    if ($post === []) {
        return $post;
    }

    $errors = validateEmailLogin($con, $post['email']);

    //поле email заполнено без ошибок и такой  email есть в БД
    if ($errors === []) {

        $user = dbGetUserByEmail($con, $post['email']);
        if (password_verify($post['password'], $user['password'])) {

            $_SESSION['id'] = $user['id'];
            $_SESSION['login'] = $user['login'];
            $_SESSION['avatar'] = $user['avatar'];
            $_SESSION['creation_time_user'] = $user['creation_time'];
            $_SESSION['posts'] = $user['posts'];
            $_SESSION['subscribers'] = $user['subscribers'];

        } else {
            $errors['password']['header'] = "Неверный пароль.";
            $errors['password']['description'] = "Вы ввели неверный пароль.";
        }
    }
    return $errors;
}

/**
 * Проверка поля формы "Электронная почта"
 *
 * @param mysqli $con  Объект-соединение с БД
 * @param string $name поле формы
 *
 * @return array  массив сообшений об ошибках
 */
function validateEmailLogin(mysqli $con, ?string $name) : array
{
    $error = [];
    if (validateFilled($name)) {
        $error['email']['header'] = "Это обязательно поле.";
        $error['email']['description'] = "Введите адрес электронной почты.";
        return $error;
    }
    if (!filter_var($name, FILTER_VALIDATE_EMAIL)) {
        $error['email']['header'] = "Ошибочный email.";
        $error['email']['description'] = "Введите корректный адрес электронной почты.";
        return $error;
    }
    if (!dbFindEmail($con, $name)) {
        $error['email']['header'] = "Такой email не зарегистрирован.";
        $error['email']['description'] = "Вы ввели неверный email.";
    }
    return $error;
}

/**
 * Проверка длины поля
 *
 * @param string $name Проверяемая строка
 * @param int $min Минимальная длина
 * @param int $max Максимальная длина
 *
 * @return bool
 */
function isCorrectLength(string $name, int $min, int $max) : bool
{
    $len = strlen(trim($name));
    return ($len >= $min && $len <= $max);
}

/**
 * Проверка соответствия типа сортировки допустимому значению
 *
 * @param array $arr Ассоциативный массив, переданный из формы методом get
 *
 * @return bool
 */
function isSortValid(array $arr) : bool
{
    $sort_types = [null, '', 'views', 'likes', 'creation_time'];

    return (isset($arr['sort']) && !in_array($arr['sort'], $sort_types)) ? false : true;
}

/**
 * Проверка соответствия значения параметра запроса допустимому значению наименования вкладки для вывода информации в профиле пользователя (Посты, Лайки, Подписки)
 *
 * @param array  $arr Ассоциативный массив, переданный из формы методом get
 *
 * @return bool
 */
function isTabValid(array $arr) : bool
{
    $types = [null, '', 'posts', 'likes', 'subscribes'];

    return (isset($arr['tab']) && !in_array($arr['tab'], $types)) ? false : true;
}

/**
 * Валидация поля формы сообщения
 * @param array  $arr Ассоциативный массив, переданный из формы методом post
 *
 * @return array массив ошибок
 */
function checkFormComment(array $arr) : array
{
    $error = [];
    // min и max длина текста
    $minlength = 4;
    $maxlength = 70;

    $name_field = 'comment';

    if (isset($arr[$name_field])) {
        if (validateFilled($arr[$name_field])) {
            $error[$name_field]['header'] = "Это обязательно поле.";
            $error[$name_field]['description'] = "Введите текст сообщения.";
            return $error;
        }
        if (!isCorrectLength($arr[$name_field], $minlength, $maxlength)) {
            $error[$name_field]['header'] = "Длина текста не соответствует требованиям.";
            $error[$name_field]['description'] = "Допустимая длина текста от 4 до 70 знаков.";
        }
    }
    return $error;
}

/**
 * Валидация поля формы сообщения
 * @param array  $arr Ассоциативный массив, переданный из формы методом post
 *
 * @return array массив ошибок
 */
function checkFormMessage(array $arr) : array
{
    $error = [];
    $name_field = 'message';

    if (isset($arr[$name_field])) {
        if (validateFilled($arr[$name_field])) {
            $error[$name_field]['header'] = "Это обязательно поле.";
            $error[$name_field]['description'] = "Введите текст сообщения.";
        }
    }
    return $error;
}

/**
 * Проверка существования пользователя и его отличия от залогиненого пользователя
 *
 * @param mysqli $con   Объект-соединение с БД
 * @param int    $user_id id пользователя адресата
 * @param int    $user_id_login id залогиненого пользователя
 *
 * @return bool
 */
function isValidUser(mysqli $con, ?int $user_id, int $user_id_login) : bool
{
    return !empty(dbGetUserById($con, $user_id)) && ($user_id != $user_id_login);
}

/**
 * Проверка наличия пользователя в списке
 *
 * @param array $contacts список пользователей
 * @param int   $user_id id пользователя
 *
 * @return bool
 */
function isUserPresentInList(array $contacts, int $user_id) : bool
{
    foreach ($contacts as $contact) {
        if ($contact['user_id'] === $user_id) {
            return true;
        }
    }
    return false;
}
