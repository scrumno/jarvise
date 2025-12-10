<?php

namespace App\Http\Action\Telegram\CreatePostTelegramAction;

use App\Telegram\Query\CreatePostTelegram\Handler;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Action
{
    public function __construct(
        private readonly Handler $handler
    ) {
    }

    public function __invoke(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!isset($data['message'])) {
            return $response->withStatus(200);
        }

        $message = $data['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'];

        try {
            $this->handler->handle($message, $chatId, $text);
        } catch (\Throwable $th) {
            throw $th;
        }

        throw new \Exception('Not implemented');
    }
}
