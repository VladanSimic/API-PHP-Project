# Media Buyers API Automation

This repository contains a PHP + Codeception API automation design for the Media Buyers resource contract.
The assignment states that no live API exists, so the suite is written as production-ready contract tests with `API_BASE_URL` supplied from environment configuration.

## Technology Choice

- PHP 8.2+
- Codeception 5
- REST module for API calls
- PhpBrowser as the HTTP transport
- Asserts module for readable business assertions
- justinrainbow/json-schema for JSON Schema validation
- dotenv/environment configuration for `API_BASE_URL`

The tests are intentionally not tied to a hard-coded URL. In a real environment, CI would inject `API_BASE_URL`, for example `https://qa.example.com/api`.

## Repository Structure

| Path | Purpose |
|---|---|
| `composer.json` | Codeception and schema-validation dependencies. |
| `codeception.yml` | Global Codeception configuration and `.env` parameter loading. |
| `tests/Api.suite.yml` | API suite using REST, PhpBrowser, and Asserts. |
| `tests/api/MediaBuyerCest.php` | GET and POST acceptance tests mapped to the contract criteria. |
| `tests/_support/Api/Client/MediaBuyerApi.php` | Thin HTTP boundary wrapper for `/mediabuyers`. |
| `tests/_support/Api/Factory/MediaBuyerPayloadFactory.php` | Request payload builder; keeps JSON out of test methods. |
| `tests/_support/Api/Assertion/MediaBuyerAssertions.php` | Shared assertions for headers, schemas, validation errors, and response data. |
| `tests/schemas/*.json` | JSON Schemas supplied by the API contract. |
| `tests/_data/media-buyers/*.json` | Sample fixture payloads for documentation and future mock setups. |
| `.github/workflows/api-tests.yml` | Example CI workflow ready for a real API environment. |

## Scenario Selection

The suite focuses on the highest-risk contract behavior:

1. `GET /api/mediabuyers` returns JSON, status 200, and matches the list schema.
2. The list response always exposes `data` as an array, including empty-state environments.
3. Returned records include required fields.
4. Email syntax, `active` type/value, and `id` uniqueness are validated for every listed item.
5. `POST /api/mediabuyers` creates a valid buyer and returns a schema-compliant response.
6. Boolean `active` input is transformed to integer `0` or `1` in the response.
7. Required-field validation covers `mbId`, `name`, `email`, and `active`.
8. Invalid email, initials, name length, `mbId`, active type, and duplicate `mbId` paths are covered.

Lower-priority cases intentionally left out for the first version:

- Authentication and authorization, because the contract does not mention them.
- Pagination, filtering, sorting, and query parameters, because the GET endpoint has none.
- Rate limiting and performance, because no SLA or throttling behavior is specified.
- Full RFC email fuzzing, because one strict invalid email acceptance criterion is provided; deeper fuzzing belongs in a later validation suite.

## Abstractions and Why They Scale

`MediaBuyerApi` centralizes endpoint paths and required headers. If the route or common headers change, tests do not need to be edited one by one.

`MediaBuyerPayloadFactory` creates valid payloads and applies explicit overrides for negative tests. This prevents copy-pasted JSON and makes boundary cases easy to parameterize.

`MediaBuyerAssertions` keeps protocol assertions, schema checks, validation-error checks, and response mapping in one place. When the suite grows from 8 tests to 80, this reduces assertion drift.

JSON Schemas live under `tests/schemas` as contract artifacts. Successful responses are validated against those files instead of informal hand assertions only.

## How to Run When an API Exists

```bash
composer install
cp .env.example .env
# edit API_BASE_URL to point at the environment, including /api
vendor/bin/codecept run Api --steps
```

For CI, set `API_BASE_URL` as a secret or environment variable and run:

```bash
vendor/bin/codecept run Api --xml --html
```

## Assumptions

- `API_BASE_URL` includes the `/api` prefix, so the test client calls `/mediabuyers`.
- `Content-Type` may include a charset, for example `application/json; charset=utf-8`; the tests assert that it contains `application/json`.
- Duplicate `mbId` should return `409 Conflict`. The contract allows either 400 or 409 as an open question, so the test accepts both while documenting 409 as the preferred API design.
- The empty-list GET test requires a controlled seed state or a mock environment with zero media buyers.
- `initials` and `slackUserId` are optional in request payloads, but present in successful responses according to the schemas.

## Contract-Drift Strategy

In a real team, the JSON Schemas in this repository should be generated from the canonical OpenAPI contract or checked against it. A CI job should fail when schemas, examples, or endpoint definitions change without corresponding test updates. Pull requests that modify the contract should require QA review and should include updated tests before merge.

