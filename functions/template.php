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
