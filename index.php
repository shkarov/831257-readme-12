<?php

require_once 'bootstrap.php';

$is_auth = rand(0, 1);

$user_name = 'Boris';

//unset($_REQUEST[session_name()]);


$errors = checkFormLogin($connect, $_POST);

if ($errors === []) {
    $userId = dbAddUser($connect, $_POST, $_FILES);
    if (!is_null($userId)) {
        header('Location: index.php');
        exit();
    }
}

$page_content = include_template("adding-user.php", ['errors' => $errors]);


if (!isset($_REQUEST['login'])) {
    $layout_content = include_template("start.php");
    print($layout_content);





} else {

    //$_REQUEST['user'] = $user_name;

    //session_start();
}





//$types = dbGetTypes($connect);

//$typeId = getTypeFromRequest($_GET);

//$sort = getSortFromRequest($_GET);

//$posts = dbGetPosts($connect, $typeId, $sort);

//$page_content = include_template("main.php", ['types' => $types, 'posts' => $posts, 'type_id' => $typeId, 'sort' => $sort]);

//var_dump($_SESSION);


/**
 * Валидация полей формы, перенаправление к валидации конкретного типа контента
 *
 * @param  array $post глобальный массив $_POST
 *
 * @return array вызов функции для проверки формы данного типа контента, возвращающей массив ошибок
 */
function checkFormLogin(array $post) : array
{
    if ($post === []) {
        return $post;
    }
    $errors = [];
    foreach ($post as $key => $value) {
        switch ($key) {
            case 'login':
                $errors[$key] = validateLoginEnter($value);
                break;
            case 'password':
                $errors[$key] = validatePassword($value);
                break;
        }
    }

    return array_filter($errors);
}

/**
 * Проверка поля формы "Логин"
 *
 * @param  string $name
 *
 * @return array массив сообшений об ошибках
 */
function validateLoginEnter(?string $name) : array
{
    $error = [];
    $type = "Логин.";
    if (validateFilled($name)) {
        $error['report'] = $type."Это поле должно быть заполнено.";
        $error['header'] = "Это обязательно поле.";
        $error['description'] = "Введите ваш логин.";
    }
    return $error;
}
