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
function check_youtube_url($youtube_url)
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
function embed_youtube_video($youtube_url)
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

/**
 * Проверка строки на заполненность
 * @param  string $name
 *
 * @return bool
 */
function validateFilled($name) {
    return empty($name);
}

/**
 * Проверка поля формы "Заголовок"
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
 */
function validateHeading($name) {
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
 * Проверка поля формы "Youtube ссылка"
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
 */
function validateYoutubeLink($name) {
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
 * @param  string $name
 *
 * @return array массив сообшений об ошибоках
 */
function validateText($name) {
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
function validateAuthor($name) {
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
function validateUrl($name) {
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
