<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\PrelineForm;

use Illuminate\Foundation\Http\FormRequest;
use Laravolt\PrelineForm\Elements\FormOpen;
use Laravolt\PrelineForm\Elements\Number;
use Laravolt\PrelineForm\Elements\Text;
use Laravolt\PrelineForm\Validation\ClientValidation;
use Laravolt\Tests\UnitTest;

class ClientValidationTest extends UnitTest
{
    protected function tearDown(): void
    {
        ClientValidation::clear();

        parent::tearDown();
    }

    public function test_form_open_accepts_form_request_for_client_validation(): void
    {
        $html = (string) (new FormOpen('/users'))->validate(ClientValidationFormRequest::class);

        $this->assertStringContainsString('data-client-validation="true"', $html);
    }

    public function test_validation_rules_are_rendered_as_native_html_attributes(): void
    {
        ClientValidation::use(ClientValidationFormRequest::class);

        $html = (string) (new Text('email'))->label('Email');

        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('type="email"', $html);
        $this->assertStringContainsString('required="required"', $html);
        $this->assertStringContainsString('maxlength="50"', $html);
        $this->assertStringContainsString('data-validation-rules="', $html);
        $this->assertStringContainsString('data-validation-message="Please enter your email."', $html);
    }

    public function test_numeric_rules_are_rendered_as_numeric_attributes(): void
    {
        ClientValidation::use(['age' => 'required|integer|min:18|max:65']);

        $html = (string) (new Number('age'))->label('Age');

        $this->assertStringContainsString('required="required"', $html);
        $this->assertStringContainsString('min="18"', $html);
        $this->assertStringContainsString('max="65"', $html);
        $this->assertStringContainsString('inputmode="numeric"', $html);
    }

    public function test_array_field_names_are_normalized_when_matching_rules(): void
    {
        ClientValidation::use(['profile.name' => 'required|min:3']);

        $html = (string) new Text('profile[name]');

        $this->assertStringContainsString('required="required"', $html);
        $this->assertStringContainsString('minlength="3"', $html);
    }

    public function test_numeric_min_and_max_are_detected_regardless_of_rule_order(): void
    {
        ClientValidation::use(['age' => 'required|min:18|max:65|integer']);

        $html = (string) new Number('age');

        $this->assertStringContainsString('min="18"', $html);
        $this->assertStringContainsString('max="65"', $html);
        $this->assertStringContainsString('inputmode="numeric"', $html);
        $this->assertStringNotContainsString('minlength="18"', $html);
    }

    public function test_wildcard_array_rules_match_indexed_field_names(): void
    {
        ClientValidation::use(new WildcardClientValidationFormRequest);

        $html = (string) new Text('items[0][name]');

        $this->assertStringContainsString('required="required"', $html);
        $this->assertStringContainsString('minlength="3"', $html);
        $this->assertStringContainsString('data-validation-message="Each item needs a name."', $html);
    }

    public function test_regex_rules_keep_commas_and_strip_laravel_delimiters_and_modifiers(): void
    {
        ClientValidation::use(['code' => ['required', 'regex:/^[A-Z]{2,4},[0-9]+$/i']]);

        $html = (string) new Text('code');

        $this->assertStringContainsString('pattern="^[A-Z]{2,4},[0-9]+$"', $html);
        $this->assertStringContainsString('&quot;regex&quot;', $html);
        $this->assertStringContainsString('^[A-Z]{2,4},[0-9]+$', $html);
    }
}

class WildcardClientValidationFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items.*.name' => 'required|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.name.required' => 'Each item needs a name.',
        ];
    }
}

class ClientValidationFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email.',
        ];
    }
}
