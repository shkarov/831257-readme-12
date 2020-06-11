<?php

/**
 * Возвращает id типа контента из массива параметров запроса, если такой тип существует, иначе возвращает null
 *
 * @param array $get массив параметров запроса
 * @param array $post массив параметров запроса
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
 * @param array $arr массив параметров запроса
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
 * @param array $arr массив параметров запроса
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
 * Возвращает значение массива по ключу $key, если такое существует, иначе - пустую строку
 *
 * @param  array $arr ассоциативный массив
 * @param  string $key ключ массива
 *
 * @return string
 */
function getPostVal(array $arr, string $key) : string
{
    return $arr[$key] ?? "";
}
