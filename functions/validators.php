<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Проверяет, что переданная ссылка ведет на публично доступное видео с youtube
 * @param string $youtube_url Ссылка на youtube видео
 * @return bool
 */
function check_youtube_url(?string $youtube_url) : bool
{
    $res = false;
    $id = extract_youtube_id($youtube_url);

    if ($id) {
        $api_data = ['id' => $id, 'part' => 'id,status', 'key' => 'AIzaSyBN-AXBnCPxO3HJfZZdZEHMybVfIgt16PQ'];
        $url = "https://www.googleapis.com/youtube/v3/videos?" . http_build_query($api_data);

        $resp = file_get_contents($url);

        if ($resp && $json = json_decode($resp, true)) {
            $res = $json['pageInfo']['totalResults'] > 0 && $json['items'][0]['status']['privacyStatus'] == 'public';
        }
    }

    return $res;
}

/**
 * Возвращает код iframe для вставки youtube видео на страницу
 * @param string $youtube_url Ссылка на youtube видео
 * @return string
 */
function embed_youtube_video(?string $youtube_url) : bool
{
    $res = "";
    $id = extract_youtube_id($youtube_url);

    if ($id) {
        $src = "https://www.youtube.com/embed/" . $id;
        $res = '<iframe width="760" height="400" src="' . $src . '" frameborder="0"></iframe>';
    }

    return $res;
}

/**
 * Валидация полей формы
 * @param  array $arr Ассоциативный массив, переданный из формы методом post
 *
 * @return array массив сообшений об ошибоках
 */
/*
function checkForm(array $arr) : array
{
    if ($arr === []) {
        return $arr;
    }

    $errors = [];
    $rules = [
        'photo-heading' => function($arr) {
            return validateHeading($arr['photo-heading']);
        },
        'video-heading' => function($arr) {
            return validateHeading($arr['video-heading']);
        },
        'video-url' => function($arr) {
            return validateYoutubeLink($arr['video-url']);
        },
        'text-heading' => function($arr) {
            return validateHeading($arr['text-heading']);
        },
        'text-text' => function($arr) {
            return validateText($arr['text-text']);
        },
        'quote-heading' => function($arr) {
            return validateHeading($arr['quote-heading']);
        },
        'quote-text' => function($arr) {
            return validateText($arr['quote-text']);
        },
        'quote-author' => function($arr) {
            return validateAuthor($arr['quote-author']);
        },
        'link-heading' => function($arr) {
            return validateHeading($arr['link-heading']);
        },
        'link-url' => function($arr) {
            return validateUrl($arr['link-url']);
        }
    ];

    foreach ($arr as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($arr);
        }
    }

    $errors = array_filter($errors);

    return $errors;
}
*/

/**
 * Валидация полей формы, перенаправление к валидации формы конкретного типа контента
 *
 * @param  array $post глобальный массив $_POST
 * @param  array $files глобальный массив $_FILES
 *
 * @return array вызов функции для проверки формы данного типа контента, возвращающей массив ошибок
 */
//function checkForm(?int $type_id, array $post, array $files) : array
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
 * @param  array $post глобальный массив $_POST
 * @param  array $files глобальный массив $_FILES
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
 * @param  array $arr Ассоциативный массив, переданный из формы методом post
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
 * @param  array $arr Ассоциативный массив, переданный из формы методом post
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
 * @param  array $arr Ассоциативный массив, переданный из формы методом post
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
 * @param  array $arr Ассоциативный массив, переданный из формы методом post
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
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
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
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
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
 * @param  string $url ссылка на файл в сети
 * @param  array $files глобальный массив $_FILES
 *
 * @return array массив сообшений об ошибоках
 */
function validateFile(?string $url, array $files = []) : array
{
    $error = [];

    $file = $files['userpic-file-photo'];
    if (!empty($file['name'])) {
        if (!($file['type'] === 'image/jpeg' || $file['type'] === 'image/png' || $file['type'] === 'image/gif')) {
            $error['report'] = "Файл. Допустимый тип файла JPEG, PNG, GIF.";
            $error['header'] = "Выбран недопустимый тип файла.";
            $error['description'] = "Выберите файл формата JPEG, PNG или GIF.";
        }
    } else {
        $type = 'Ссылка из интернета.';
        if (validateFilled($url)) {
            $error['report'] = $type."Необходимо выбрать файл на локальном ПК или указать ссылку на файл в интернете.";
            $error['header'] = "Это поле должно быть заполнено.";
            $error['description'] = "Введите ссылку на файл в интернете или выберите файл на локальном ПК.";
        } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
            $error['report'] = $type."Ошибка URL.";
            $error['header'] = "Ошибочная ссылка.";
            $error['description'] = "Введите корректный адрес ссылки.";
        } else {
            $file_headers = get_headers($url);
            if (!strpos($file_headers[0], '200')) {
                $error['report'] = $type."Файл не найден.";
                $error['header'] = "Ошибка загрузки.";
                $error['description'] = "Не удалось найти файл по указанному адресу.";
            } else {
                $content_type = '';
                foreach ($file_headers as $key => $value) {
                    if (mb_strpos($value, 'Content-Type:') !== false) {
                        $content_type = ltrim(mb_substr($value, -10));
                    }
                }
                if (!($content_type === 'image/jpeg' || $content_type === 'image/png' || $content_type === 'image/gif')) {
                    $error['report'] = $type. "Допустимый тип файла JPEG, PNG, GIF.";
                    $error['header'] = "Выбран недопустимый тип файла.";
                    $error['description'] = "Выберите файл формата JPEG, PNG или GIF.";
                } elseif (!file_get_contents($url)) {
                    $error['report'] = $type."Ошибка загрузки файла.";
                    $error['header'] = "Ошибка загрузки.";
                    $error['description'] = "Не удалось загрузить файл с указанного адреса.";
                }
            }
        }
    }
    return $error;
}

/**
 * Проверка поля формы "Youtube ссылка"
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
 */
function validateYoutubeLink(?string $name) : array
{
    $error = [];
    $type = "Ссылка youtube.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите адрес ссылки на видеоролик.";
    } elseif (!filter_var($name, FILTER_VALIDATE_URL)) {
        $error['report'] = $type."Ошибка URL.";
        $error['header'] = "Ошибочная ссылка.";
        $error['description'] = "Введите корректный адрес ссылки на видеоролик.";
    } elseif (!check_youtube_url($name)) {
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
 * @return array массив сообшений об ошибоках
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
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
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
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
 */
function validateUrl(?string $name) : array
{
    $error = [];
    $type = "Ссылка.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите адрес ссылки.";
    } elseif (!filter_var($name, FILTER_VALIDATE_URL)) {
        $error['report'] = $type."Ошибка URL.";
        $error['header'] = "Ошибочная ссылка.";
        $error['description'] = "Введите корректный адрес ссылки.";
    }
    return $error;
}

/*
//Проверка длины
function isCorrectLength($name, $min, $max) {
    $len = strlen($_POST[$name]);

    if ($len < $min or $len > $max) {
        return "Значение должно быть от $min до $max символов";
    }
}

function validateEmail($name) {
    if (!filter_input(INPUT_POST, $name, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный email";
    }
}
*/
