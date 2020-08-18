<?php

/**
 * Сохранение аватара нового пользователя локально
 *
 * @param array $files массив с данными о загружаемом аватаре пользователя
 *
 * @return string возвращает относительный путь к загруженному файлу либо null
 */
function savePicture(array $files) : ?string
{
    $picture = '';
    $file = $files[key($files)];

    if (!empty($file['name'])) {
        $file_path = 'uploads/';

        //формируем новое имя файла
        $file_name = md5(uniqid());
        $file_ext = mb_substr($file['type'], mb_strpos($file['type'], '/') + 1);
        $file_ext = $file_ext === 'jpeg' ? 'jpg' : $file_ext;
        $picture = $file_path.$file_name.'.'.$file_ext;

        if (!move_uploaded_file($file['tmp_name'], $picture)) {
            echo "Ошибка перемещения файла";
            return null;
        };
    }
    return $picture;
}

/**
 * Сортирует массив первого уровня на основании значения $field из второго уровня вложенности
 *
 * Пример:
 * $arr = [
 *          0 => [id => 13, text => 'qsd', time => '2020-06-12 14:45:33'],
 *          1 => [id => 20, text => 'aqd', time => '2020-06-02 14:45:33'],
 *          2 => [id => 10, text => 'xsd', time => '2020-06-22 14:45:33']
 *        ]
 * $field = 'time'
 *
 * Отсортированный массив:
 *        [
 *          0 => [id => 10, text => 'xsd', time => '2020-06-22 14:45:33'],
 *          1 => [id => 13, text => 'qsd', time => '2020-06-12 14:45:33'],
 *          2 => [id => 20, text => 'aqd', time => '2020-06-02 14:45:33']
 *        ]
 *
 * @param array  $arr Двумерный массив
 * @param string $field поле, по которому сортируется массив
 *
 * @return array Отсортированный массив, по убыванию (пузырьковый метод)
 */
function sortBubbleDescArray(array $arr, string $field) : array
{
    $size = count($arr);

    for( $i=0; $i < $size; $i++) {
        for( $j = $size-1; $j > $i; $j-- ) {
            if ( $arr[$j-1][$field] < $arr[$j][$field] ) {
                $temp = $arr[$j-1];
                $arr[$j-1]=$arr[$j];
                $arr[$j]= $temp;
            }
        }
    }
    return $arr;
}
