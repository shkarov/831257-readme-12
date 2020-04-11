<?php
$is_auth = rand(0, 1);

$user_name = 'Boris';

$cards = [
    [
        'header' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'username' => 'Лариса',
        'userpic' => 'userpic-larisa-small.jpg'
    ],
    [
        'header' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала! Не могу дождаться начала финального сезона своего любимого сериала!Не могу дождаться начала финального сезона своего любимого сериала!',
        'username' => 'Владик',
        'userpic' => 'userpic.jpg'
    ],
    [
        'header' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'username' => 'Виктор',
        'userpic' => 'userpic-mark.jpg'
    ],
    [
        'header' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'username' => 'Лариса',
        'userpic' => 'userpic-larisa-small.jpg'
    ],
    [
        'header' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'username' => 'Владик',
        'userpic' => 'userpic.jpg'
    ]
];

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

require_once 'helpers.php';

$page_content = include_template("main.php", $cards);

$layout_content = include_template("layout.php", ['content' => $page_content, 'title' => 'readme: популярное', 'user' => $user_name]);

print($layout_content);
