<?php

declare(strict_types=1);

namespace App\Notify;

use App\Entity\Version;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TelegramNotificator
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly MessageFormatter $messageFormatter,
        private readonly string $token,
        private readonly string $chatId,
    ) {
    }

    public function notify(Version $version): void
    {
        $response = $this->client->request(
            'POST',
            "https://api.telegram.org/bot$this->token/sendMessage",
            [
                'json' => [
                    'chat_id' => $this->chatId,
                    'text' => $this->messageFormatter->format($version),
                    'parse_mode' => 'HTML',
                ],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            if ($response->getStatusCode() === 429) {
                sleep(60);

                $this->notify($version);
            } else {
                throw new \RuntimeException('sendMessage failure ' . $response->getContent(false));
            }
        }
    }
}
