<?php

declare(strict_types=1);

namespace omarinina\infrastructure\jobs;

use omarinina\infrastructure\services\mail\dto\ChatMessageMailDto;
use omarinina\infrastructure\services\mail\MailSendService;
use yii\queue\JobInterface;

class ChatMessageMailJob implements JobInterface
{
    /** @var ChatMessageMailDto  */
    private ChatMessageMailDto $chatMessageMailDto;

    /**
     * @param ChatMessageMailDto $chatMessageMailDto
     */
    public function __construct(
        ChatMessageMailDto $chatMessageMailDto,
    ) {
        $this->chatMessageMailDto = $chatMessageMailDto;
    }

    public function execute($queue)
    {
        MailSendService::sendChatMessageMail($this->chatMessageMailDto);
    }
}