<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\Thunderclap;

use Laravolt\Tests\UnitTest;

/**
 * Regression for v7 P1-5: Thunderclap must generate FormRequest-backed
 * Store/Update requests by default, with the controller typehinting them
 * and the _form blade using PrelineForm helpers.
 */
class FormRequestStubsTest extends UnitTest
{
    private string $storeRequestStub;

    private string $updateRequestStub;

    private string $controllerStub;

    private string $formBladeStub;

    private string $createBladeStub;

    private string $editBladeStub;

    protected function setUp(): void
    {
        parent::setUp();

        $base = __DIR__.'/../../../packages/thunderclap/stubs/laravolt';

        $this->storeRequestStub = (string) file_get_contents($base.'/Requests/Store.php.stub');
        $this->updateRequestStub = (string) file_get_contents($base.'/Requests/Update.php.stub');
        $this->controllerStub = (string) file_get_contents($base.'/Controllers/Controller.php.stub');
        $this->formBladeStub = (string) file_get_contents($base.'/resources/views/_form.blade.php.stub');
        $this->createBladeStub = (string) file_get_contents($base.'/resources/views/create.blade.php.stub');
        $this->editBladeStub = (string) file_get_contents($base.'/resources/views/edit.blade.php.stub');
    }

    public function test_store_request_extends_form_request(): void
    {
        $this->assertStringContainsString('use Illuminate\\Foundation\\Http\\FormRequest;', $this->storeRequestStub);
        $this->assertStringContainsString('extends FormRequest', $this->storeRequestStub);
    }

    public function test_store_request_defines_rules_and_authorize(): void
    {
        $this->assertStringContainsString('public function rules(): array', $this->storeRequestStub);
        $this->assertStringContainsString('public function authorize(): bool', $this->storeRequestStub);
        $this->assertStringContainsString(':VALIDATION_RULES:', $this->storeRequestStub);
    }

    public function test_update_request_extends_form_request(): void
    {
        $this->assertStringContainsString('use Illuminate\\Foundation\\Http\\FormRequest;', $this->updateRequestStub);
        $this->assertStringContainsString('extends FormRequest', $this->updateRequestStub);
    }

    public function test_update_request_defines_rules_and_authorize(): void
    {
        $this->assertStringContainsString('public function rules(): array', $this->updateRequestStub);
        $this->assertStringContainsString('public function authorize(): bool', $this->updateRequestStub);
        $this->assertStringContainsString(':VALIDATION_RULES:', $this->updateRequestStub);
    }

    public function test_controller_imports_and_typehints_store_and_update_requests(): void
    {
        $this->assertStringContainsString(
            'use :Namespace:\\:ModuleName:\\Requests\\Store;',
            $this->controllerStub,
            'Generated controller must import the Store FormRequest.'
        );
        $this->assertStringContainsString(
            'use :Namespace:\\:ModuleName:\\Requests\\Update;',
            $this->controllerStub,
            'Generated controller must import the Update FormRequest.'
        );

        $this->assertMatchesRegularExpression(
            '/function\s+store\s*\(\s*Store\s+\$request\s*\)/',
            $this->controllerStub,
            'Generated controller store() must typehint Store FormRequest.'
        );
        $this->assertMatchesRegularExpression(
            '/function\s+update\s*\(\s*Update\s+\$request\s*,/',
            $this->controllerStub,
            'Generated controller update() must typehint Update FormRequest.'
        );
    }

    public function test_controller_uses_validated_payload_from_form_requests(): void
    {
        $this->assertStringContainsString(
            '$request->validated()',
            $this->controllerStub,
            'Generated controller must call ->validated() to consume FormRequest output.'
        );
    }

    public function test_form_blade_uses_preline_form_helpers(): void
    {
        $this->assertStringContainsString('form()->action(', $this->formBladeStub);
        $this->assertStringContainsString('form()->submit(', $this->formBladeStub);
        $this->assertStringContainsString('form()->linkButton(', $this->formBladeStub);
        $this->assertStringContainsString(':FORM_CREATE_FIELDS:', $this->formBladeStub);
    }

    public function test_create_and_edit_blades_open_form_against_module_routes(): void
    {
        $this->assertStringContainsString("form()->post(route('modules:::module-name:.store'))", $this->createBladeStub);
        $this->assertStringContainsString('form()->close()', $this->createBladeStub);

        $this->assertStringContainsString(
            "->put(route('modules:::module-name:.update",
            $this->editBladeStub,
            'Generated edit blade should submit PUT to the module update route.'
        );
        $this->assertStringContainsString(
            'form()->bind($:moduleName:)',
            $this->editBladeStub,
            'Generated edit blade should bind the model to populate form fields.'
        );
        $this->assertStringContainsString('form()->close()', $this->editBladeStub);

        $this->assertStringContainsString('@include(\':module-name:::_form\')', $this->createBladeStub);
        $this->assertStringContainsString('@include(\':module-name:::_form\')', $this->editBladeStub);
    }
}
