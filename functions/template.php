<?php
/**
 * Обрезает строку
 *
 * @param string $text - Строка текста
 *
 * @param int $lengthMax - Максимально допустимое количество символов в строке
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
 * @param string $date - Дата в текстовом представлении
 *
 * @return string - Строка вида “% месяцев назад”
 *
 */
function dateDifferent(string $date) : string
{
    $markPost = strtotime($date);
    $markNow = time();
    $markDiff = $markNow - $markPost;

    $datePost = date_create($date);
    $dateNow = date_create("now");
    $dateDiff = date_diff($datePost, $dateNow);

    if ($markDiff/60 < 60) {
        //если до текущего времени прошло меньше 60 минут, то формат будет вида “% минут назад”;
        $numDiff = ceil($markDiff/60);
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "минута", "минуты", "минут")." назад";
    } elseif ($markDiff/3600 < 24) {
        //если до текущего времени прошло больше 60 минут, но меньше 24 часов, то формат будет вида “% часов назад”;
        $numDiff = ceil($markDiff/3600);
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "час", "часа", "часов")." назад";
    } elseif ($markDiff/86400 < 7) {
        //если до текущего времени прошло больше 24 часов, но меньше 7 дней, то формат будет вида “% дней назад”;
        $numDiff = ceil($markDiff/86400);
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "день", "дня", "дней")." назад";
    } elseif ($markDiff/(86400*7) < 5) {
        //если до текущего времени прошло больше 7 дней, но меньше 5 недель, то формат будет вида “% недель назад”;
        $numDiff = ceil($markDiff/(86400*7));
        $interval = "$numDiff ".get_noun_plural_form($numDiff, "неделя", "недели", "недель")." назад";
    } else {
        //если до текущего времени прошло больше 5 недель, то формат будет вида “% месяцев назад”.
        $interval = $dateDiff->format('%m')." ".get_noun_plural_form((int) $dateDiff->format('%m'), "месяц", "месяца", "месяцев")." назад";
    }
    return $interval;
}
