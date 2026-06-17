<?php

declare(strict_types=1);

namespace Tests\Support\Api\Client;

use Tests\Support\ApiTester;

final class MediaBuyerApi
{
    private const RESOURCE = '/mediabuyers';

    public function __construct(private readonly ApiTester $I)
    {
    }

    public function list(): void
    {
        $this->I->haveHttpHeader('Accept', 'application/json');
        $this->I->sendGet(self::RESOURCE);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): void
    {
        $this->I->haveHttpHeader('Content-Type', 'application/json');
        $this->I->haveHttpHeader('Accept', 'application/json');
        $this->I->sendPost(self::RESOURCE, $payload);
    }
}

