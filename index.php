<?php

require_once 'bootstrap.php';

$is_auth = rand(0, 1);

$user_name = 'Boris';

$connect =  dbConnect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$sqlTypeContent = "SELECT * FROM content_type";

$sqlPostUserType = "SELECT p.*, u.login, u.avatar, c.class
                    FROM   post AS p
                           JOIN user AS u
                           ON u.id = p.user_id
                           JOIN content_type AS c
                           ON c.id = p.content_type_id
                    ORDER BY p.views DESC";

$types = dbQuery($connect, $sqlTypeContent);
$cards = dbQuery($connect, $sqlPostUserType);

$page_content = include_template("main.php", ['types' => $types, 'cards' => $cards]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name, 'is_auth' => $is_auth]);

print($layout_content);
