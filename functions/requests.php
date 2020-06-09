<?php

/**
 * Возвращает id типа контента из массива параметров запроса, если такой тип существует, иначе возвращает null
 *
 * @param $arr array массив параметров запроса
 *
 * @return int or null
 */
function getTypeFromRequest(array $get, array $post = []) : ?int
{
    if (isset($get['type_id'])) {
        return (int) $get['type_id'];
    } elseif (isset($post['type_id'])) {
        return (int) $post['type_id'];
    }
    return null;
}

/**
 * Возвращает id поста из массива параметров запроса, если id найден, иначе возвращает null
 *
 * @arr array массив параметров запроса
 *
 * @return int or null
 */
function getPostIdFromRequest(array $arr) : ?int
{
    if (!isset($arr['post_id'])) {
        return null;
    }
    if (!is_numeric($arr['post_id'])) {
        exit('Некорректный параметр post_id');
    }
    return (int) $arr['post_id'];
}

/**
 * Возвращает признак сортировки из массива параметров запроса, если параметр найден, иначе возвращает null
 *
 * @arr array массив параметров запроса
 *
 * @return int or null
 */
function getSortFromRequest(array $arr) : ?string
{
    if (!isset($arr['sort'])) {
        return null;
    }
    if (!is_string($arr['sort'])) {
        exit('Некорректный параметр sort');
    }
    return $arr['sort'];
}

/**
 * Возвращает знавение массива по ключу $key, если такое существует, иначе - пустую строку
 *
 * @param  @arr array ассоциативный массив
 * @param  @key string ключ массива
 *
 * @return string
 */
function getPostVal(array $arr, string $key) : string
{
    return $arr[$key] ?? "";
}
