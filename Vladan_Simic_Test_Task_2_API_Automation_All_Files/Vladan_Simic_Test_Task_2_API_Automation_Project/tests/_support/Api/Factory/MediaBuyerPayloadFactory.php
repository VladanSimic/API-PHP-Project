<?php

declare(strict_types=1);

namespace Tests\Support\Api\Factory;

final class MediaBuyerPayloadFactory
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public function valid(array $overrides = []): array
    {
        $unique = (string) random_int(100000, 999999);

        return array_replace([
            'mbId' => $unique,
            'initials' => 'TM',
            'name' => 'Test Media Buyer',
            'email' => sprintf('test.media.buyer.%s@example.com', $unique),
            'slackUserId' => 'U05AZ3DQBBKK',
            'active' => true,
        ], $overrides);
    }

    /**
     * @return array<string, mixed>
     */
    public function without(string $field): array
    {
        $payload = $this->valid();
        unset($payload[$field]);

        return $payload;
    }
}

