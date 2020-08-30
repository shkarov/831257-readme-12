<?php

/**
 * Возвращает id типа контента из массива параметров запроса, если такой тип существует, иначе возвращает null
 * ('type_id' == 0  равноценно null)
 * @param array $get массив параметров запроса
 * @param array $post массив параметров запроса
 *
 * @return int or null
 */
function getTypeFromRequest(array $get, array $post = []) : ?int
{
    if (isset($get['type_id'])) {
        return empty($get['type_id']) ? null : (int) $get['type_id'];
    }
    if (isset($post['type_id'])) {
        return (int) $post['type_id'];
    }
    return null;
}

/**
 * Возвращает id поста из массива параметров запроса, если id найден, иначе возвращает null
 *
 * @param array $get массив параметров запроса
 * @param array $post массив параметров запроса
 *
 * @return int or null
 */
function getPostIdFromRequest(array $get, array $post = []) : ?int
{
    if (isset($get['post_id'])) {
        return (int) $get['post_id'];
    }
    if (isset($post['post_id'])) {
        return (int) $post['post_id'];
    }
    return null;
}

/**
 * Возвращает имя поля в БД, по которому будет производится сортировка данных в запросе
 *
 * @param array $arr массив параметров запроса
 *
 * @return string
 */
function getSortFromRequest(array $arr) : string
{
    if (!isSortValid($arr)) {
        exit('Некорректный параметр sort');
    }

    return empty($arr['sort']) ? 'views' : $arr['sort'];
}

/**
 * Возвращает имя вкладки в профиле пользователя, по которому будет производится выборка данных в запросе
 *
 * @param array $arr массив параметров запроса
 *
 * @return string
 */
function getTabFromRequest(array $arr) : string
{
    if (!isTabValid($arr)) {
        exit('Некорректный параметр tab');
    }

    return empty($arr['tab']) ? 'posts' : $arr['tab'];
}

/**
 * Возвращает id пользователя из массива параметров запроса, если id найден, иначе возвращает null
 *
 * @param array $get массив параметров запроса
 * @param array $post массив параметров запроса
 *
 * @return int or null
 */
function getUserIdFromRequest(array $get, array $post = []) : ?int
{
    if (isset($get['user_id'])) {
        return (int) $get['user_id'];
    }
    if (isset($post['user_id'])) {
        return (int) $post['user_id'];
    }
    return null;
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

/**
 * Выборка постов по строке поиска
 *
 * @param mysqli $con Объект-соединение с БД
 * @param string $search строка поиска
 *
 * @return array Ассоциативный массив
 */
function getPostsSearch(mysqli $con, string $search) : array
{
    $search_string = trim($search);

    if (empty($search_string)) {
        return [];
    }

    if (mb_substr($search_string, 0, 1) === '#') {
        return dbGetPostsSearchHashtag($con, mb_substr($search_string, 1));
    }

    return dbGetPostsSearchFulltext($con, $search_string);
}

/**
 * Получение списка пользователей, имеющих сообщения с текущим пользователем
 * Список отсортирован по дате создания сообщения
 *
 * @param mysqli $con Объект-соединение с БД
 * @param int    $user_id id пользователя
 *
 * @return array Ассоциативный массив Результат запроса
 */
function getContactsMessages(mysqli $con, int $user_id) : array
{
    $contacts = dbGetContactsMessages($con, $user_id);

    // отфильтровываются уникальные значения $user_id
    $contacts_uniq = [];
    $user_id = 0;
    foreach ($contacts as $value) {
        if ($user_id != $value['user_id']) {
            $contacts_uniq[] = $value;
        }
        $user_id = $value['user_id'];
    }

    //сортировка масива по убыванию даты создания поста
    usort($contacts_uniq, function ($a, $b)
                            { return $a['creation_time'] === $b['creation_time'] ? 0 : ($a['creation_time'] < $b['creation_time'] ? 1 : -1);} );

    return $contacts_uniq;
}

/**
 * Возвращает номер страницы для пагинации
 *
 * @param array $get Ассоциативный массив, переданный методом get
 * @param int   $count_pages количество страниц для полного вывода результата запроса
 *
 * @return int номер страницы
 */
function getPageNumber(array $get, int $count_pages) : int
{
    $page = isset($get['page']) ? (int) $get['page'] : 1;
    if ($page > $count_pages) {
        $page = $count_pages;
    }
    return $page;
}
