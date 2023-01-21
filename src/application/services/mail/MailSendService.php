<?php

declare(strict_types=1);

namespace omarinina\application\services\mail;

use omarinina\infrastructure\constants\MailConstants;
use Yii;

class MailSendService
{
    public static function sendChatMessageMail($messageDto) : void
    {
        Yii::$app->mailer->compose('chatmessage', [
            'currentAd' => $messageDto->currentAd,
            'sender' => $messageDto->currentUser,
            'time' => $messageDto->currentTime,
            'message' => $messageDto->message,
            'html' => 'layouts/html'
        ])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($messageDto->receiverEmail)
            ->setSubject(MailConstants::CHAT_HEADER)
            ->send();
    }
}