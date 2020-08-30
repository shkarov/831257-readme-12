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
