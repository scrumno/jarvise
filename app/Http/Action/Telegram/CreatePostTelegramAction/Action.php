<?php

namespace App\Http\Action\Telegram\CreatePostTelegramAction;

use App\Telegram\Query\CreatePostTelegram\Handler;

class Action
{
    public function __construct(
        private readonly Handler $handler
    ) {
    }

    public function __invoke()
    {
        try {
            $this->handler->handle();
        } catch (\Throwable $th) {
            throw $th;
        }

        throw new \Exception('Not implemented');
    }
}
