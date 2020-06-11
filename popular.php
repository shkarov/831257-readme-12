<?php

require_once 'bootstrap.php';

$is_auth = rand(0, 1);

$user_name = 'Boris';

$types = dbGetTypes($connect);

$typeId = getTypeFromRequest($_GET);

$sort = getSortFromRequest($_GET);

$posts = dbGetPosts($connect, $typeId, $sort);

$page_content = include_template("main.php", ['types' => $types, 'posts' => $posts, 'type_id' => $typeId, 'sort' => $sort]);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name, 'is_auth' => $is_auth]);

print($layout_content);
