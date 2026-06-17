# QA Assessment - Manual Testing and API Automation

## Author

**Vladan Simic**

---

## Project Overview

This repository contains my QA assessment work for the assigned testing task.

The work is divided into two main parts:

1. **Task 1 - Manual QA Testing Report**

   * Manual testing of a lead generation funnel.
   * Validation of multi-step form behavior.
   * Requirement traceability.
   * Boundary value testing.
   * Bug reporting with severity, priority, reproducibility, expected result, actual result, business impact, and evidence.

2. **Task 2 - API Automation Project**

   * PHP + Codeception API automation framework.
   * Automated contract-style tests for the Media Buyers API.
   * Clean test architecture using API client classes, payload factories, reusable assertion helpers, JSON schemas, test fixtures, and CI configuration.

The purpose of this repository is to demonstrate both practical manual QA skills and structured API automation knowledge.

---

# Table of Contents

* [Project Overview](#project-overview)
* [Technologies Used](#technologies-used)
* [Repository Structure](#repository-structure)
* [Task 1 - Manual QA Testing](#task-1---manual-qa-testing)
* [Manual QA Scope](#manual-qa-scope)
* [Manual QA Key Findings](#manual-qa-key-findings)
* [Manual QA Bug Summary](#manual-qa-bug-summary)
* [Boundary Test Matrix](#boundary-test-matrix)
* [Manual QA Recommendations](#manual-qa-recommendations)
* [Task 2 - API Automation](#task-2---api-automation)
* [API Automation Scope](#api-automation-scope)
* [Tested API Endpoints](#tested-api-endpoints)
* [API Test Scenarios](#api-test-scenarios)
* [Automation Project Structure](#automation-project-structure)
* [Automation Design Explanation](#automation-design-explanation)
* [Installation Requirements](#installation-requirements)
* [How to Install the Project](#how-to-install-the-project)
* [Environment Configuration](#environment-configuration)
* [How to Run Tests Locally](#how-to-run-tests-locally)
* [How to Run Tests in CI](#how-to-run-tests-in-ci)
* [GitHub Actions](#github-actions)
* [Reports and Output](#reports-and-output)
* [Assumptions](#assumptions)
* [Known Limitations](#known-limitations)
* [Recommended Next Steps](#recommended-next-steps)
* [Final QA Summary](#final-qa-summary)

---

# Technologies Used

## Manual QA

The manual QA part was performed using:

* Google Chrome on Windows
* Browser DevTools
* Network tab observation
* Dynamic UI checks
* Client-side JavaScript review
* Screenshot-based evidence
* Boundary value analysis
* Requirement traceability
* Risk-based testing approach

## API Automation

The API automation project uses:

* PHP 8.2+
* Composer
* Codeception 5
* Codeception REST module
* Codeception PhpBrowser module
* Codeception Asserts module
* JSON Schema validation
* justinrainbow/json-schema
* vlucas/phpdotenv
* GitHub Actions
* Markdown documentation

---

# Repository Structure

Expected repository structure:

```text
.
├── .github/
│   └── workflows/
│       └── api-tests.yml
│
├── tests/
│   ├── api/
│   │   └── MediaBuyerCest.php
│   │
│   ├── schemas/
│   │   ├── get-media-buyers-schema.json
│   │   └── post-media-buyer-schema.json
│   │
│   ├── _data/
│   │   └── media-buyers/
│   │       ├── list-response-example.json
│   │       └── valid-create-payload.json
│   │
│   ├── _support/
│   │   └── Api/
│   │       ├── Assertion/
│   │       │   └── MediaBuyerAssertions.php
│   │       ├── Client/
│   │       │   └── MediaBuyerApi.php
│   │       └── Factory/
│   │           └── MediaBuyerPayloadFactory.php
│   │
│   ├── _bootstrap.php
│   └── Api.suite.yml
│
├── codeception.yml
├── composer.json
├── .env.example
├── .gitignore
├── README.md
│
├── Vladan_Simic_Task_1_Test_Report.docx
├── Vladan_Simic_Test_Task_2_API_Automation_Report.docx
└── Vladan_Simic_Test_Task_2_API_Automation_Report.pdf
```

---

# Task 1 - Manual QA Testing

## Tested Website

```text
https://test-qa.capslock.global/
```

## Manual QA Objective

The purpose of the manual testing task was to verify the lead generation funnel and identify defects that could negatively affect:

* lead quality
* conversion tracking
* sales team efficiency
* user experience
* form validation
* business qualification logic
* data integrity

The testing focused on whether the form behaves according to the written requirements and whether the submitted data appears complete, valid, and useful for the business.

---

# Manual QA Scope

The following areas were covered:

* Multi-step form behavior
* Required field validation
* ZIP code validation
* Email validation
* Phone validation
* Interest selection validation
* Property type qualification logic
* Successful redirect to the Thank You page
* Network behavior during form submission
* Top form and bottom form behavior
* Duplicate form flow
* UI validation messages
* Boundary value testing
* Client-side JavaScript validation behavior

## Out of Scope

The following areas were not tested because they require backend or internal system access:

* Backend database verification
* CRM ingestion
* Email delivery
* SMS delivery
* Internal analytics dashboards
* Production monitoring
* Server logs
* Real downstream lead processing after submission

---

# Manual QA Key Findings

The lead funnel contains several high-impact defects.

The highest-risk issue is that the user can complete the funnel and reach the Thank You page after a successful-looking submission, while the observed network traffic does not show the complete lead data being submitted.

Other important risks include:

* ZIP values outside the written requirement are accepted.
* Interest selection is not required.
* Ineligible property types are allowed to continue.
* Phone validation does not fully match the written requirement.
* Email validation UX is not clear enough.
* Bottom form interaction moves the user to the top form area.
* The current funnel can create incomplete, invalid, or unqualified leads while still showing a successful conversion to the user.

Overall risk:

```text
The funnel can create incomplete, invalid, or unqualified leads while still reporting a successful conversion to the user.
```

---

# Manual QA Bug Summary

## BUG-01 - ZIP+4 value is accepted although ZIP must contain exactly 5 digits

**Severity:** Critical
**Priority:** P1
**Area:** ZIP validation
**Reproducibility:** 100%

### Test Data

```text
12345-6789
```

### Steps to Reproduce

1. Open the tested website.
2. Enter `12345-6789` in the ZIP Code field.
3. Click `Next`.

### Expected Result

The value should be rejected because the ZIP code must contain exactly 5 digits.

### Actual Result

The form accepts the ZIP+4 value and advances the user to the next step.

### Business Impact

Invalid location data can enter the funnel. This can affect lead routing, sales follow-up, service area checks, analytics, and reporting accuracy.

---

## BUG-02 - Canadian postal code is accepted in a US ZIP-only field

**Severity:** Critical
**Priority:** P1
**Area:** ZIP validation
**Reproducibility:** 100%

### Test Data

```text
K1A 0B1
```

### Steps to Reproduce

1. Open the tested website.
2. Enter `K1A 0B1` in the ZIP Code field.
3. Click `Next`.

### Expected Result

The value should be rejected because the ZIP field should accept only exactly 5 numeric digits.

### Actual Result

The form accepts the Canadian postal code format and advances to the next step.

### Business Impact

Non-US or malformed leads can enter the system, creating wasted sales effort and unreliable location data.

---

## BUG-03 - Interest selection is not required

**Severity:** Critical
**Priority:** P1
**Area:** Required fields
**Reproducibility:** 100%

### Scenario

The user can continue without selecting any option under:

```text
Why are you interested in a walk-in tub?
```

### Steps to Reproduce

1. Enter a valid ZIP code, for example `90210`.
2. Leave all interest options unchecked.
3. Click `Next`.

### Expected Result

The user should remain on the interest step and see a required-field validation message.

### Actual Result

The form advances to the property step without any selected interest.

### Business Impact

Sales and marketing lose important intent data needed for lead qualification and personalization.

---

## BUG-04 - Rental Property is accepted even though it should be blocked

**Severity:** Critical
**Priority:** P1
**Area:** Lead qualification
**Reproducibility:** 100%

### Scenario

The user selects:

```text
Rental Property
```

### Steps to Reproduce

1. Enter ZIP `90210`.
2. Select an interest option, for example `Safety`.
3. Select `Rental Property`.
4. Click `Next`.

### Expected Result

The user should remain on the property step and see an ineligible-property message.

### Actual Result

The form advances to the name/email step.

### Business Impact

Unqualified leads can enter the funnel, increasing cost per valid lead and wasting sales team time.

---

## BUG-05 - Mobile Home is accepted even though it should be blocked

**Severity:** Critical
**Priority:** P1
**Area:** Lead qualification
**Reproducibility:** 100%

### Scenario

The user selects:

```text
Mobile Home
```

### Steps to Reproduce

1. Enter ZIP `90210`.
2. Select an interest option, for example `Safety`.
3. Select `Mobile Home`.
4. Click `Next`.

### Expected Result

The user should be blocked from continuing and should see the ineligible-property message.

### Actual Result

The form advances to the contact details step.

### Business Impact

The system captures leads that the business explicitly says it cannot service.

---

## BUG-06 - Successful submit redirects to Thank You page without observed complete lead data

**Severity:** Critical
**Priority:** P1
**Area:** Submission
**Reproducibility:** 100%

### Scenario

The user completes the full funnel with valid-looking data.

Example data:

```text
ZIP: 90210
Interest: Safety
Property: Owned House / Condo
Name: Test User
Email: user@example.com
Phone: 2345678901
```

### Steps to Reproduce

1. Complete the form using valid-looking data.
2. Click `Submit Your Request`.
3. Observe network traffic and final URL.

### Expected Result

The complete lead payload should be submitted before redirecting to the Thank You page.

Expected submitted data should include:

```text
ZIP
Interest
Property type
Name
Email
Phone number
```

### Actual Result

The user is redirected to the Thank You page, but the observed network traffic only shows the ZIP step payload.

### Business Impact

This is the highest business risk because the user sees a successful conversion, while the business may not receive complete actionable contact details.

---

## BUG-07 - Phone validation does not match the exactly 10 digits requirement

**Severity:** High
**Priority:** P2
**Area:** Phone validation
**Reproducibility:** 100%

### Expected Result

Phone validation should accept only 10 numeric digits after normalization.

### Actual Result

The implementation allows broader phone formats, such as optional country code, separators, alphanumeric groups, and optional extensions.

### Business Impact

Lead phone values may become inconsistent across CRM, dialer, analytics, and deduplication systems.

---

## BUG-08 - Phone mask blocks some 10-digit numeric values without explaining the rule

**Severity:** High
**Priority:** P2
**Area:** Phone input
**Reproducibility:** 100%

### Test Data

```text
1234567890
```

### Steps to Reproduce

1. Reach the phone step.
2. Try to type `1234567890`.
3. Observe the masked input behavior.

### Expected Result

The value should be accepted according to the written requirement because it contains exactly 10 digits.

### Actual Result

The UI silently imposes an additional first-digit restriction.

### Business Impact

Users may be blocked or confused even when entering a valid-looking 10-digit phone number.

---

## BUG-09 - Invalid email shows no clear inline validation message

**Severity:** High
**Priority:** P2
**Area:** Email validation UX
**Reproducibility:** 100%

### Test Data

```text
bad-email
```

### Steps to Reproduce

1. Enter ZIP `90210`.
2. Select an interest option.
3. Select `Owned House / Condo`.
4. Enter a valid name.
5. Enter `bad-email`.
6. Click `Go To Estimate`.

### Expected Result

A clear inline email validation message should be shown next to the email field.

### Actual Result

The user remains on the same step without a clear custom inline validation message.

### Business Impact

Users may not understand why they cannot continue, which can increase form abandonment.

---

## BUG-10 - Bottom form submission scrolls the user to the top form area

**Severity:** High
**Priority:** P2
**Area:** Duplicate form flow
**Reproducibility:** 100%

### Scenario

The user interacts with the bottom form on the landing page.

### Steps to Reproduce

1. Scroll to the bottom form.
2. Enter ZIP `90210`.
3. Click `Next`.

### Expected Result

The user should remain in the bottom form and continue the quiz there.

### Actual Result

After submitting the bottom form ZIP step, the page scrolls the user to the top form area.

### Business Impact

This can cause confusion, duplicate form state risk, and conversion loss on a long landing page.

---

# Boundary Test Matrix

| Field | Test Value                                  | Expected Result                     | Observed Result                                  |
| ----- | ------------------------------------------- | ----------------------------------- | ------------------------------------------------ |
| ZIP   | empty                                       | Reject                              | Rejected with required message                   |
| ZIP   | 1234                                        | Reject                              | Rejected as wrong ZIP                            |
| ZIP   | 12345                                       | Accept                              | Accepted                                         |
| ZIP   | 123456                                      | Reject                              | Rejected as wrong ZIP                            |
| ZIP   | 12345-6789                                  | Reject                              | Accepted - BUG-01                                |
| ZIP   | K1A 0B1                                     | Reject                              | Accepted - BUG-02                                |
| ZIP   | abcde                                       | Reject                              | Rejected as wrong ZIP                            |
| Email | bad-email                                   | Reject with clear message           | Blocked, but no clear custom inline form message |
| Email | [user@example.com](mailto:user@example.com) | Accept                              | Accepted                                         |
| Phone | 12345                                       | Reject                              | Rejected                                         |
| Phone | 2345678901                                  | Accept                              | Accepted and redirected                          |
| Phone | 1234567890                                  | Accept based on written requirement | Blocked by undocumented mask rule - BUG-08       |

---

# Manual QA Recommendations

Recommended fixes:

1. Enforce ZIP validation on both client side and server side.
2. Accept only exactly 5 digits for ZIP code if that is the written requirement.
3. Reject ZIP+4 and non-US postal formats if the field is ZIP-only.
4. Make interest selection required.
5. Block ineligible property types before collecting contact details.
6. Submit the complete lead payload before redirecting to the Thank You page.
7. Align phone validation with the written requirement.
8. Normalize phone input before validation and storage.
9. Add clear inline validation messages for blocked fields.
10. Improve email validation UX.
11. Keep users in the same form instance when they interact with the bottom form.
12. Reduce duplicate form state risk by using one shared form component or shared validation logic.
13. Add automated regression tests for all fixed defects.
14. Add API or integration tests that verify the final submitted payload contains all required lead data.

---

# Task 2 - API Automation

## API Automation Objective

The purpose of the API automation project is to validate the Media Buyers API contract using PHP and Codeception.

The test suite is designed as a production-ready contract test framework.

The assignment states that no live API was provided, so the framework is prepared to run once a valid API environment is available through the `API_BASE_URL` environment variable.

---

# API Automation Scope

The automation suite validates:

* HTTP status codes
* JSON response content type
* JSON response structure
* JSON schema compliance
* Required fields
* Data types
* Valid email format
* Active flag mapping
* Unique IDs
* Required-field validation
* Negative validation scenarios
* Duplicate `mbId` behavior

---

# Tested API Endpoints

The project focuses on the following endpoints:

```text
GET /api/mediabuyers
POST /api/mediabuyers
```

The configured base URL should include the `/api` prefix.

Example:

```env
API_BASE_URL=https://qa.example.com/api
```

The test client then calls:

```text
/mediabuyers
```

Final endpoint:

```text
https://qa.example.com/api/mediabuyers
```

---

# API Test Scenarios

## GET /api/mediabuyers

Covered validations:

* Response status code is `200 OK`.
* Response content type contains `application/json`.
* Response matches `get-media-buyers-schema.json`.
* Response contains `data`.
* `data` is an array.
* Every listed media buyer contains required fields.
* Every listed media buyer has a valid email.
* Every listed media buyer has an integer `active` flag.
* `active` is either `0` or `1`.
* Media buyer IDs are unique.
* Empty list still returns:

```json
{
  "data": []
}
```

The empty list test is marked as state-dependent because it requires a controlled environment with zero media buyers.

---

## POST /api/mediabuyers

Covered validations:

* Valid media buyer can be created.
* Response status code is `200 OK`.
* Response content type contains `application/json`.
* Response matches `post-media-buyer-schema.json`.
* Response contains a server-generated positive `id`.
* Request payload values are returned correctly.
* Boolean `active` input is mapped to integer response value:

  * `true` becomes `1`
  * `false` becomes `0`

---

## Required Field Validation

The following fields are tested as required:

```text
mbId
name
email
active
```

For each missing field, the expected response is:

```text
400 Bad Request
```

The validation response should mention the missing field.

---

## Negative Validation Scenarios

The suite covers these negative cases:

* Invalid email is rejected.
* Invalid initials length is rejected.
* Invalid name length is rejected.
* Non-numeric `mbId` is rejected.
* Non-boolean `active` is rejected.
* Duplicate `mbId` is rejected on second create.

For duplicate `mbId`, the preferred API behavior is:

```text
409 Conflict
```

The test accepts both:

```text
400 Bad Request
409 Conflict
```

because the exact duplicate behavior was not fully strict in the assignment contract.

---

# Automation Project Structure

## `composer.json`

Defines project dependencies and test scripts.

Main dependencies:

```json
{
  "codeception/codeception": "^5.1",
  "codeception/module-asserts": "^3.0",
  "codeception/module-phpbrowser": "^3.0",
  "codeception/module-rest": "^3.4",
  "justinrainbow/json-schema": "^6.0",
  "vlucas/phpdotenv": "^5.6"
}
```

Available scripts:

```json
{
  "test:api": "codecept run Api --steps",
  "test:api:ci": "codecept run Api --xml --html"
}
```

---

## `codeception.yml`

Global Codeception configuration.

It defines:

* test paths
* output path
* support path
* data path
* bootstrap file
* `.env` parameter loading
* memory limit
* RunFailed extension

---

## `tests/Api.suite.yml`

API suite configuration.

Enabled modules:

```yaml
actor: ApiTester

modules:
  enabled:
    - REST:
        depends: PhpBrowser
        part: Json
        url: '%API_BASE_URL%'
    - PhpBrowser:
        url: '%API_BASE_URL%'
    - Asserts

step_decorators: ~
```

---

## `tests/api/MediaBuyerCest.php`

Main API test class.

Contains tests for:

* listing media buyers
* validating listed buyer fields
* checking unique IDs
* empty list state
* creating media buyer
* validating boolean active mapping
* required field validation
* invalid email
* invalid initials length
* invalid name length
* invalid `mbId`
* invalid `active`
* duplicate `mbId`

---

## `tests/_support/Api/Client/MediaBuyerApi.php`

API client wrapper.

Purpose:

* centralizes endpoint paths
* sends GET and POST requests
* sets common headers
* keeps test methods cleaner and more readable

Endpoint used by the client:

```php
private const RESOURCE = '/mediabuyers';
```

---

## `tests/_support/Api/Factory/MediaBuyerPayloadFactory.php`

Payload factory.

Purpose:

* creates valid request payloads
* generates unique `mbId`
* generates unique email values
* supports overrides for negative tests
* supports removing required fields for required-field validation tests

Example valid payload structure:

```php
[
    'mbId' => '123456',
    'initials' => 'TM',
    'name' => 'Test Media Buyer',
    'email' => 'test.media.buyer.123456@example.com',
    'slackUserId' => 'U05AZ3DQBBKK',
    'active' => true,
]
```

---

## `tests/_support/Api/Assertion/MediaBuyerAssertions.php`

Reusable assertion helper.

Purpose:

* validates JSON content type
* validates JSON schema
* checks that `data` is an array
* checks required fields
* validates email format
* validates integer `active` flag
* checks unique IDs
* checks server-generated positive ID
* checks that created buyer matches request payload
* validates expected error messages
* supports checking one of multiple allowed status codes

This keeps repeated assertion logic outside of test methods.

---

## `tests/schemas/`

Contains JSON schema files:

```text
get-media-buyers-schema.json
post-media-buyer-schema.json
```

Purpose:

* validates API contract
* ensures expected response structure
* ensures required properties exist
* ensures field data types are correct
* prevents contract drift

---

## `tests/_data/media-buyers/`

Contains sample fixture files:

```text
list-response-example.json
valid-create-payload.json
```

Purpose:

* documents expected example data
* supports future mock setup
* makes the expected contract easier to understand

---

## `.github/workflows/api-tests.yml`

GitHub Actions workflow.

Purpose:

* runs API contract tests in CI
* installs PHP
* installs Composer dependencies
* executes the Codeception API suite
* uploads test reports as artifacts

---

# Automation Design Explanation

The framework is intentionally separated into layers.

## Test Layer

```text
tests/api/MediaBuyerCest.php
```

Contains readable test scenarios.

The goal is for each test to clearly describe business behavior and contract expectations.

---

## API Client Layer

```text
tests/_support/Api/Client/MediaBuyerApi.php
```

Handles HTTP requests.

This prevents repeated endpoint paths and repeated headers inside test methods.

---

## Payload Factory Layer

```text
tests/_support/Api/Factory/MediaBuyerPayloadFactory.php
```

Creates valid and invalid test payloads.

This avoids copy-pasted JSON in tests and makes negative scenarios easier to maintain.

---

## Assertion Layer

```text
tests/_support/Api/Assertion/MediaBuyerAssertions.php
```

Contains reusable assertions.

This makes the suite easier to scale because validation logic is written once and reused across multiple tests.

---

## Schema Layer

```text
tests/schemas/
```

Contains JSON contract files.

Successful responses are validated against JSON schemas, which gives stronger contract coverage than simple field-by-field checks only.

---

# Installation Requirements

Before running the project, install:

## PHP

Required version:

```text
PHP 8.2 or newer
```

Check PHP version:

```bash
php -v
```

---

## Composer

Composer is required for installing PHP dependencies.

Check Composer version:

```bash
composer -V
```

---

## Git

Git is required for cloning and version control.

Check Git version:

```bash
git --version
```

---

# How to Install the Project

Clone the repository:

```bash
git clone https://github.com/VladanSimic/API-PHP-Project.git
```

Enter the project folder:

```bash
cd API-PHP-Project
```

Install dependencies:

```bash
composer install
```

---

# Environment Configuration

Create a `.env` file from `.env.example`.

On Linux/macOS:

```bash
cp .env.example .env
```

On Windows CMD:

```cmd
copy .env.example .env
```

Open `.env` and set the API base URL:

```env
API_BASE_URL=http://localhost:8080/api
```

Example for a QA environment:

```env
API_BASE_URL=https://qa.example.com/api
```

Important:

```text
API_BASE_URL should include the /api prefix.
```

---

# How to Run Tests Locally

Run the API suite with detailed steps:

```bash
vendor/bin/codecept run Api --steps
```

On Windows CMD:

```cmd
vendor\bin\codecept run Api --steps
```

Or run through Composer:

```bash
composer test:api
```

---

# How to Run Tests in CI

Run tests with XML and HTML reports:

```bash
vendor/bin/codecept run Api --xml --html
```

Or through Composer:

```bash
composer test:api:ci
```

---

# GitHub Actions

The CI workflow is located here:

```text
.github/workflows/api-tests.yml
```

It runs on:

```yaml
pull_request:
workflow_dispatch:
```

The workflow:

1. Checks out the repository.
2. Sets up PHP 8.2.
3. Installs Composer dependencies.
4. Runs Codeception API tests.
5. Uploads Codeception report artifacts.

Before running the workflow, configure this GitHub secret:

```text
API_BASE_URL
```

Example value:

```text
https://qa.example.com/api
```

---

# Reports and Output

Codeception output is generated in:

```text
tests/_output/
```

Possible generated files:

```text
report.html
report.xml
logs
failed test details
```

The `tests/_output/` directory should remain ignored by Git because reports are generated files.

---

# Assumptions

The API automation suite is based on the following assumptions:

1. `API_BASE_URL` includes the `/api` prefix.
2. The Media Buyers endpoint path is `/mediabuyers`.
3. `GET /api/mediabuyers` returns status `200 OK`.
4. `POST /api/mediabuyers` returns status `200 OK` for valid creation.
5. Response `Content-Type` may include charset, for example:

```text
application/json; charset=utf-8
```

Because of that, tests check that Content-Type contains:

```text
application/json
```

6. `active` is sent as boolean in the request but returned as integer in the response:

   * `true` becomes `1`
   * `false` becomes `0`

7. `id` is generated by the server and must be a positive integer.

8. `mbId` is provided in the request and should remain consistent in the response.

9. Duplicate `mbId` should ideally return `409 Conflict`, but the test accepts both `400` and `409`.

10. The empty list scenario requires a controlled environment with zero media buyers.

11. `initials` and `slackUserId` are optional in request payloads, but present in successful responses according to the schemas.

12. Authentication and authorization are not included because they were not part of the provided contract.

13. Pagination, filtering, sorting, and rate limiting are not tested because they were not specified.

---

# Known Limitations

Because no live API was provided with the assignment, the automation project is prepared as a contract-ready framework.

Current limitations:

* No real backend service is included in the repository.
* Tests require a valid `API_BASE_URL`.
* State-dependent tests require controlled test data.
* Duplicate `mbId` testing requires persistence in the backend.
* Empty list testing requires a seeded or mock environment with zero media buyers.
* Authentication is not covered.
* Authorization is not covered.
* Pagination is not covered.
* Filtering is not covered.
* Sorting is not covered.
* Performance testing is not covered.
* Rate limit testing is not covered.
* Database verification is not covered.
* Downstream CRM validation is not covered.

---

# Recommended Next Steps

## API Automation Improvements

Recommended next improvements:

1. Connect the suite to a real QA API environment.
2. Add test data setup and cleanup.
3. Add database verification if database access is available.
4. Add authentication tests if authentication is introduced.
5. Add authorization tests for different user roles.
6. Add pagination tests if pagination is introduced.
7. Add filtering and sorting tests if query parameters are added.
8. Add performance smoke checks.
9. Add contract drift detection against OpenAPI documentation.
10. Add more detailed negative validation cases.
11. Add tags for smoke, regression, contract, and state-dependent tests.
12. Add Docker support for easier local execution.

---

## Manual QA Follow-Up

Recommended follow-up for manual QA defects:

1. Fix ZIP validation.
2. Fix interest selection required validation.
3. Block ineligible property types.
4. Ensure complete lead data is submitted before Thank You redirect.
5. Align phone validation with the written requirement.
6. Add better inline validation messages.
7. Fix bottom form behavior.
8. Add automated regression tests for all confirmed defects.
9. Add API or integration validation for complete lead submission.
10. Add server-side validation for all critical fields.

---

# Example Test Command Summary

## Install dependencies

```bash
composer install
```

## Create environment file

Linux/macOS:

```bash
cp .env.example .env
```

Windows CMD:

```cmd
copy .env.example .env
```

## Run API tests

Linux/macOS:

```bash
vendor/bin/codecept run Api --steps
```

Windows CMD:

```cmd
vendor\bin\codecept run Api --steps
```

## Run API tests through Composer

```bash
composer test:api
```

## Run API tests with reports

```bash
composer test:api:ci
```

---

# Contract-Drift Strategy

In a real team environment, the JSON schemas in this repository should be generated from the canonical OpenAPI contract or checked against it.

A CI job should fail when schemas, examples, or endpoint definitions change without corresponding test updates.

Pull requests that modify the API contract should require QA review and should include updated tests before merge.

---

# What This Project Demonstrates

This project demonstrates:

* Manual QA testing
* Requirement analysis
* Boundary value testing
* Bug reporting
* Severity and priority assessment
* Business impact analysis
* Evidence-based reporting
* Network observation
* Client-side validation review
* API contract testing
* PHP automation with Codeception
* JSON schema validation
* Clean test architecture
* Environment-based configuration
* CI-ready test execution
* Risk-based QA thinking

---

# Final QA Summary

The manual testing identified several high-risk defects in the lead funnel, especially around invalid ZIP data, missing required interest selection, unqualified property types, incomplete lead submission, and unclear validation behavior.

The API automation part provides a maintainable and scalable foundation for validating the Media Buyers API contract once a live or mock API environment is available.

Overall, this repository shows both practical manual QA thinking and structured API automation implementation.
