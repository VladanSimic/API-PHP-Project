<?php

declare(strict_types=1);

namespace Tests\Api;

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\Support\Api\Assertion\MediaBuyerAssertions;
use Tests\Support\Api\Client\MediaBuyerApi;
use Tests\Support\Api\Factory\MediaBuyerPayloadFactory;
use Tests\Support\ApiTester;

final class MediaBuyerCest
{
    private MediaBuyerApi $mediaBuyers;
    private MediaBuyerPayloadFactory $payloadFactory;
    private MediaBuyerAssertions $assertions;

    public function _before(ApiTester $I): void
    {
        $this->mediaBuyers = new MediaBuyerApi($I);
        $this->payloadFactory = new MediaBuyerPayloadFactory();
        $this->assertions = new MediaBuyerAssertions();
    }

    public function listMediaBuyersReturnsJsonArrayMatchingSchema(ApiTester $I): void
    {
        $this->mediaBuyers->list();

        $I->seeResponseCodeIs(HttpCode::OK);
        $this->assertions->seeJsonContentType($I);
        $this->assertions->seeResponseMatchesSchema($I, 'get-media-buyers-schema.json');
        $this->assertions->seeDataIsArray($I);
        $this->assertions->seeEveryListedBuyerHasRequiredFields($I);
    }

    public function listedMediaBuyersRespectFieldLevelContract(ApiTester $I): void
    {
        $this->mediaBuyers->list();

        $I->seeResponseCodeIs(HttpCode::OK);
        $this->assertions->seeEveryListedBuyerHasValidEmail($I);
        $this->assertions->seeEveryListedBuyerHasIntegerActiveFlag($I);
    }

    public function listedMediaBuyerIdsAreUnique(ApiTester $I): void
    {
        $this->mediaBuyers->list();

        $I->seeResponseCodeIs(HttpCode::OK);
        $this->assertions->seeListedBuyerIdsAreUnique($I);
    }

    /**
     * This test requires an environment seeded with zero media buyers.
     * It documents G3 explicitly and should run in a controlled contract or mock environment.
     *
     * @group state-dependent
     */
    public function emptyMediaBuyerListStillReturnsDataArray(ApiTester $I): void
    {
        $this->mediaBuyers->list();

        $I->seeResponseCodeIs(HttpCode::OK);
        $this->assertions->seeJsonContentType($I);
        $this->assertions->seeResponseMatchesSchema($I, 'get-media-buyers-schema.json');
        $I->seeResponseContainsJson(['data' => []]);
    }

    /**
     * @example {"active": true, "expectedActive": 1}
     * @example {"active": false, "expectedActive": 0}
     */
    public function createMediaBuyerMapsBooleanActiveToInteger(ApiTester $I, Example $example): void
    {
        $payload = $this->payloadFactory->valid([
            'active' => $example['active'],
        ]);

        $this->mediaBuyers->create($payload);

        $I->seeResponseCodeIs(HttpCode::OK);
        $this->assertions->seeJsonContentType($I);
        $this->assertions->seeResponseMatchesSchema($I, 'post-media-buyer-schema.json');
        $this->assertions->seeServerGeneratedPositiveId($I);
        $this->assertions->seeCreatedBuyerMatchesPayload($I, $payload, $example['expectedActive']);
    }

    /**
     * @example {"field": "mbId"}
     * @example {"field": "name"}
     * @example {"field": "email"}
     * @example {"field": "active"}
     */
    public function requiredFieldsAreValidated(ApiTester $I, Example $example): void
    {
        $payload = $this->payloadFactory->without((string) $example['field']);

        $this->mediaBuyers->create($payload);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $this->assertions->seeJsonContentType($I);
        $this->assertions->seeValidationErrorsMention($I, sprintf('This field is missing: [%s]', $example['field']));
    }

    public function invalidEmailIsRejected(ApiTester $I): void
    {
        $this->mediaBuyers->create($this->payloadFactory->valid([
            'email' => 'not-an-email',
        ]));

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $this->assertions->seeValidationErrorsMention($I, 'not-an-email', 'not a valid email');
    }

    public function invalidInitialsLengthIsRejected(ApiTester $I): void
    {
        $this->mediaBuyers->create($this->payloadFactory->valid([
            'initials' => 'TOO LONG',
        ]));

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $this->assertions->seeValidationErrorsMention($I, 'The initials must be exactly 2 characters long');
    }

    /**
     * @example {"name": "A"}
     * @example {"name": "This name is definitely longer than thirty characters"}
     */
    public function invalidNameLengthIsRejected(ApiTester $I, Example $example): void
    {
        $this->mediaBuyers->create($this->payloadFactory->valid([
            'name' => (string) $example['name'],
        ]));

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $this->assertions->seeValidationErrorsMention($I, 'name');
        $this->assertions->seeAnyValidationErrorContainsOneOf($I, ['length', 'between 2 and 30', '2 and 30']);
    }

    public function nonNumericMbIdIsRejected(ApiTester $I): void
    {
        $this->mediaBuyers->create($this->payloadFactory->valid([
            'mbId' => 'abc',
        ]));

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $this->assertions->seeValidationErrorsMention($I, 'mbId');
    }

    public function nonBooleanActiveIsRejected(ApiTester $I): void
    {
        $this->mediaBuyers->create($this->payloadFactory->valid([
            'active' => 'yes',
        ]));

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $this->assertions->seeValidationErrorsMention($I, 'active');
    }

    public function duplicateMbIdIsRejectedOnSecondCreate(ApiTester $I): void
    {
        $payload = $this->payloadFactory->valid();

        $this->mediaBuyers->create($payload);
        $I->seeResponseCodeIs(HttpCode::OK);

        $this->mediaBuyers->create($payload);

        $this->assertions->seeResponseCodeIsOneOf($I, [HttpCode::BAD_REQUEST, HttpCode::CONFLICT]);
        $this->assertions->seeValidationErrorsMention($I, 'mbId');
    }
}
