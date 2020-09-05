<?php

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form(int $number, string $one, string $two, string $many): string
{
    $number = (int)$number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 *
 * @return string Итоговый HTML
 */
function include_template(string $name, array $data = []) : string
{
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();

    extract($data);

    require $name;

    $result = ob_get_clean();

    return $result;
}

/**
 * Возвращает код iframe для вставки youtube видео на страницу
 *
 * @param string $youtube_url Ссылка на youtube видео
 *
 * @return string
 */
function embed_youtube_video(?string $youtube_url) : string
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
 * Возвращает img-тег с обложкой видео для вставки на страницу
 *
 * @param string $youtube_url Ссылка на youtube видео
 *
 * @return string
 */
function embed_youtube_cover(string $youtube_url) : string
{
    $res = "";
    $id = extract_youtube_id($youtube_url);

    if ($id) {
        $src = sprintf("https://img.youtube.com/vi/%s/mqdefault.jpg", $id);
        $res = '<img alt="youtube cover" width="320" height="120" src="' . $src . '" />';
    }

    return $res;
}

/**
 * Извлекает из ссылки на youtube видео его уникальный ID
 *
 * @param string $youtube_url Ссылка на youtube видео
 *
 * @return string Строка id или null
 */
function extract_youtube_id(string $youtube_url) : ?string
{
    $id = null;

    $parts = parse_url($youtube_url);

    if ($parts) {
        if ($parts['path'] === '/watch') {
            parse_str($parts['query'], $vars);
            $id = $vars['v'] ?? null;
        } else {
            if ($parts['host'] === 'youtu.be') {
                $id = substr($parts['path'], 1);
            }
        }
    }

    return $id;
}

/**
 * Обрезает строку
 *
 * @param string $text - Строка текста
 * @param int    $lengthMax - Максимально допустимое количество символов в строке
 *
 * @return string - Строка, длиной $lengthMax, дополненая "...";
 *                  либо
 *                  Строка без изменений, если длина входящей строки не превышает максимально допустимую
 */
function textTrim(string $text, int $lengthMax = 300) : string
{
    if (mb_strlen($text) <= $lengthMax) {
        return $text;
    }
    $arrText = explode(" ", $text);
    $arrTextNew = [];
    $len = 0;
    foreach ($arrText as $word) {
        if (($len + mb_strlen($word)) >= $lengthMax) {
            break;
        }
        $arrTextNew[] = $word;
        $len += mb_strlen($word) + 1;
    }
    return implode(" ", $arrTextNew)."...";
}

/**
 * Возращает строку, соответствующую временному промежутку между параметром и текущей датой
 *
 * @param string $date Дата в текстовом представлении
 * @param string $phraseEnding Окончание фразы
 *
 * @return string - Строка вида “% месяцев назад”
 *
 */
function dateDifferent(string $date, string $phraseEnding = "назад") : string
{
    $markPost = strtotime($date);
    $markNow = time();
    $markDiff =  $markNow - $markPost;
    if ($markDiff < 0) {
        exit('Переданная дата не может быть больше текущей');
    }
    $datePost = date_create($date);
    $dateNow = date_create("now");
    $dateDiff = date_diff($datePost, $dateNow);

    if ($markDiff/60 < 60) {
        //если до текущего времени прошло меньше 60 минут, то формат будет вида “% минут назад”;
        $numDiff = round($markDiff/60);
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "минута", "минуты", "минут");
    } elseif ($markDiff/3600 < 24) {
        //если до текущего времени прошло больше 60 минут, но меньше 24 часов, то формат будет вида “% часов назад”;
        $numDiff = round($markDiff/3600);
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "час", "часа", "часов");
    } elseif ($markDiff/86400 < 7) {
        //если до текущего времени прошло больше 24 часов, но меньше 7 дней, то формат будет вида “% дней назад”;
        $numDiff = round($markDiff/86400);
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "день", "дня", "дней");
    } elseif ($markDiff/(86400*7) < 5) {
        //если до текущего времени прошло больше 7 дней, но меньше 5 недель, то формат будет вида “% недель назад”;
        $numDiff = round($markDiff/(86400*7));
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "неделя", "недели", "недель");
    } elseif ($markDiff/(86400*7*4) < 12) {
        //если до текущего времени прошло больше 4 недель, то формат будет вида “% месяцев назад”.
        $interval = $dateDiff->format('%m')." ".get_noun_plural_form((int) $dateDiff->format('%m'), "месяц", "месяца", "месяцев");
    } else {
        //если до текущего времени прошло больше 12 месяцев, то формат будет вида “% лет назад”.
        $interval = $dateDiff->format('%y')." ".get_noun_plural_form((int) $dateDiff->format('%y'), "год", "года", "лет");
    }
    return $interval." ".$phraseEnding;
}

/**
 * Возращает строку - тип заголовка нового поста
 *
 * @param int $type_id - тип контента
 *
 * @return string
 *
 */
function getHeadingAddPost(?int $type_id) : string
{
    switch ($type_id) {
        case 1:
            return 'photo-heading';
        case 2:
            return 'video-heading';
        case 3:
            return 'text-heading';
        case 4:
            return 'quote-heading';
        case 5:
            return 'link-heading';
        default:
            return 'photo-heading';
    }
}

/**
 * Возращает строку - тип тегов нового поста
 *
 * @param int $type_id - тип контента
 *
 * @return string
 *
 */
function getTagsAddPost(?int $type_id) : string
{
    switch ($type_id) {
        case 2:
            return 'video-tags';
        case 3:
            return 'text-tags';
        case 4:
            return 'quote-tags';
        case 5:
            return 'link-tags';
        default:
            return 'photo-tags';
    }
}
