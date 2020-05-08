<?php

require_once 'bootstrap.php';

$is_auth = rand(0, 1);

$user_name = 'Boris';

$connect =  dbConnect($config);

$types = dbGetTypes($connect);
$posts = dbGetPosts($connect);

$page_content = include_template("main.php", ['types' => $types, 'posts' => $posts]);

//if (isset($_GET['post_id'])) {
//    $post = dbGetSinglePost($connect);
 //   $page_content = include_template("post.php", $post);
//}

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name, 'is_auth' => $is_auth]);

print($layout_content);
