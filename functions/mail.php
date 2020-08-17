<?php

/**
 * Подготовка заголовка и тела email-уведомления и последующая его отправка
 *
 * @param array  $smtp_conf массив c данными для подключения к smtp серверу
 * @param string $type_message тип передаваемого сообщения
 * @param array  $to   массив c данными адресата
 * @param array  $from массив c данными отправителя
 * @param string $post_header заголовок поста, добавленного пользователем
 *
 * @return void
 */
function sendEmail(array $smtp_conf, string $type_message, array $to, array $from, string $post_header = "") : void
{
    $link_profile = $smtp_conf['base_url']."profile.php?user_id=".$from['id'];

    switch ($type_message) {
        case 'subscribe':
            $message_title = "У вас новый подписчик";
            $message_body = "На вас подписался новый пользователь ".$from['login'].". Вот ссылка на его профиль: $link_profile";
            break;
        case 'post':
            $message_title = "Новая публикация от пользователя ".$from['login'];
            $message_body = "Пользователь ".$from['login']." только что опубликовал новую запись ".htmlspecialchars($post_header).". Посмотрите её на странице пользователя: $link_profile";
            break;
        default:
            $message_title = '';
            $message_body = '';
    }

    transferEmail($smtp_conf, $to, $message_title, $message_body);
}

/**
 * Отправка email-уведомления
 *
 * @param array  $smtp_conf массив c данными для подключения к smtp серверу
 * @param array  $to   массив c данными адресата
 * @param string $title заголовок передаваемого сообщения
 * @param string $body тело сообщения
 *
 * @return void
 */
function transferEmail(array $smtp_conf, array $to, string $title, string $body) : void
{
    // Конфигурация траспорта
    $transport = new Swift_SmtpTransport($smtp_conf['host'], $smtp_conf['port']);
    $transport->setUsername($smtp_conf['user']);
    $transport->setPassword($smtp_conf['password']);

    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

     // Формирование сообщения
    $message = new Swift_Message($title);
    $message->setFrom([$smtp_conf['user'] => 'Readme']);

    // Отправка сообщения
    foreach ($to as $address) {

        $message->setTo([$address['email'] => $address['login']]);

        $message_body = "Здравствуйте, ".$address['login'].". ".$body;

        $message->setBody($message_body);
        $mailer->send($message);
    }
}
