<?php

declare(strict_types=1);

namespace Tests\Support\Api\Assertion;

use JsonSchema\Validator;
use Tests\Support\ApiTester;

final class MediaBuyerAssertions
{
    private const REQUIRED_FIELDS = [
        'id',
        'mbId',
        'initials',
        'name',
        'email',
        'slackUserId',
        'active',
    ];

    public function seeJsonContentType(ApiTester $I): void
    {
        $contentType = (string) $I->grabHttpHeader('Content-Type');
        $I->assertStringContainsString('application/json', strtolower($contentType));
    }

    public function seeResponseMatchesSchema(ApiTester $I, string $schemaFile): void
    {
        $schemaPath = codecept_root_dir() . 'tests/schemas/' . $schemaFile;
        $schema = json_decode((string) file_get_contents($schemaPath));
        $response = json_decode($I->grabResponse());

        $I->assertNotNull($schema, sprintf('Schema %s must be valid JSON.', $schemaFile));
        $I->assertNotNull($response, 'Response must be valid JSON before schema validation.');

        $validator = new Validator();
        $validator->validate($response, $schema);

        $I->assertTrue(
            $validator->isValid(),
            sprintf("Response does not match %s:\n%s", $schemaFile, $this->formatSchemaErrors($validator->getErrors()))
        );
    }

    public function seeDataIsArray(ApiTester $I): void
    {
        $body = $this->responseArray($I);

        $I->assertArrayHasKey('data', $body);
        $I->assertIsArray($body['data']);
    }

    public function seeEveryListedBuyerHasRequiredFields(ApiTester $I): void
    {
        foreach ($this->listedBuyers($I) as $index => $buyer) {
            foreach (self::REQUIRED_FIELDS as $field) {
                $I->assertArrayHasKey($field, $buyer, sprintf('Buyer at index %d is missing %s.', $index, $field));
            }
        }
    }

    public function seeEveryListedBuyerHasValidEmail(ApiTester $I): void
    {
        foreach ($this->listedBuyers($I) as $index => $buyer) {
            $I->assertArrayHasKey('email', $buyer);
            $I->assertNotFalse(
                filter_var($buyer['email'], FILTER_VALIDATE_EMAIL),
                sprintf('Buyer at index %d has invalid email %s.', $index, (string) $buyer['email'])
            );
        }
    }

    public function seeEveryListedBuyerHasIntegerActiveFlag(ApiTester $I): void
    {
        foreach ($this->listedBuyers($I) as $index => $buyer) {
            $I->assertContains($buyer['active'] ?? null, [0, 1], sprintf('Buyer at index %d has invalid active flag.', $index));
            $I->assertIsInt($buyer['active'], sprintf('Buyer at index %d active flag must be integer.', $index));
        }
    }

    public function seeListedBuyerIdsAreUnique(ApiTester $I): void
    {
        $ids = array_map(static fn (array $buyer): mixed => $buyer['id'] ?? null, $this->listedBuyers($I));

        $I->assertSame($ids, array_values(array_unique($ids)), 'Media buyer ids must be unique in the GET response.');
    }

    public function seeServerGeneratedPositiveId(ApiTester $I): void
    {
        $data = $this->responseArray($I)['data'] ?? null;

        $I->assertIsArray($data);
        $I->assertArrayHasKey('id', $data);
        $I->assertIsInt($data['id']);
        $I->assertGreaterThan(0, $data['id']);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function seeCreatedBuyerMatchesPayload(ApiTester $I, array $payload, int $expectedActive): void
    {
        $data = $this->responseArray($I)['data'] ?? [];

        foreach (['mbId', 'initials', 'name', 'email', 'slackUserId'] as $field) {
            $I->assertSame($payload[$field], $data[$field] ?? null, sprintf('%s should round-trip from request to response.', $field));
        }

        $I->assertSame($expectedActive, $data['active'] ?? null, 'Boolean active request value should map to integer response value.');
    }

    /**
     * @param list<int> $expectedCodes
     */
    public function seeResponseCodeIsOneOf(ApiTester $I, array $expectedCodes): void
    {
        $I->assertContains($I->grabResponseCode(), $expectedCodes, 'Response code should be one of the documented duplicate mbId outcomes.');
    }

    public function seeValidationErrorsMention(ApiTester $I, string ...$needles): void
    {
        $details = $this->validationErrorDetails($I);
        $joined = implode("\n", $details);

        foreach ($needles as $needle) {
            $I->assertStringContainsString($needle, $joined);
        }
    }

    /**
     * @param list<string> $needles
     */
    public function seeAnyValidationErrorContainsOneOf(ApiTester $I, array $needles): void
    {
        $joined = strtolower(implode("\n", $this->validationErrorDetails($I)));

        foreach ($needles as $needle) {
            if (str_contains($joined, strtolower($needle))) {
                $I->assertTrue(true);
                return;
            }
        }

        $I->assertTrue(false, sprintf('Expected any validation error to contain one of: %s', implode(', ', $needles)));
    }

    /**
     * @return array<string, mixed>
     */
    private function responseArray(ApiTester $I): array
    {
        $body = json_decode($I->grabResponse(), true);
        $I->assertIsArray($body, 'Response must be a JSON object.');

        return $body;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function listedBuyers(ApiTester $I): array
    {
        $body = $this->responseArray($I);
        $I->assertArrayHasKey('data', $body);
        $I->assertIsArray($body['data']);

        return $body['data'];
    }

    /**
     * @return list<string>
     */
    private function validationErrorDetails(ApiTester $I): array
    {
        $body = $this->responseArray($I);

        $I->assertArrayHasKey('errors', $body);
        $I->assertIsArray($body['errors']);

        return array_map(static fn (array $error): string => (string) ($error['detail'] ?? ''), $body['errors']);
    }

    /**
     * @param list<array{property?: string, message?: string}> $errors
     */
    private function formatSchemaErrors(array $errors): string
    {
        return implode("\n", array_map(
            static fn (array $error): string => sprintf('[%s] %s', $error['property'] ?? 'root', $error['message'] ?? 'unknown error'),
            $errors
        ));
    }
}
