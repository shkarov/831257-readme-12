<?php

/**
 * Отправка email-уведомления
 *
 * @param array  $conf массив c данными для подключения к smtp серверу
 * @param string $type_message тип передаваемого сообщения
 * @param array  $to   массив c данными адресата
 * @param array  $from массив c данными отправителя
 * @param string $post_header заголовок поста, добавленного пользователем
 *
 * @return void
 */
function sendEmail(array $conf, string $type_message, array $to, array $from, string $post_header = "") : void
{
    $smtp_conf = $conf['smtp'];

    // Конфигурация траспорта
    $transport = new Swift_SmtpTransport($smtp_conf['host'], $smtp_conf['port']);
    $transport->setUsername($smtp_conf['user']);
    $transport->setPassword($smtp_conf['password']);

    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    $message_title = ($type_message === 'subscribe') ? "У вас новый подписчик" : "Новая публикация от пользователя ".$from['login'];

    // Формирование сообщения
    $message = new Swift_Message($message_title);
    $message->setFrom([$smtp_conf['user'] => 'Readme']);

    $link_profile = "http://readme.local/profile.php?user_id=".$from['id'];

    // Отправка сообщения
    foreach ($to as $address) {

        $message->setTo([$address['email'] => $address['login']]);

        $message_body = "Здравствуйте, ".$address['login'].".";
        if ($type_message === 'subscribe') {
            $message_body = $message_body." На вас подписался новый пользователь ".$from['login'].". Вот ссылка на его профиль: $link_profile";
        }
        if ($type_message === 'post') {
            $message_body = $message_body." Пользователь ".$from['login']." только что опубликовал новую запись $post_header. Посмотрите её на странице пользователя: $link_profile";
        }
        $message->setBody($message_body);
        $mailer->send($message);
    }
}
