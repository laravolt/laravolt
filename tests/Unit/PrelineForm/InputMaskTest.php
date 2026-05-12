<?php

declare(strict_types=1);

namespace Laravolt\Tests\Unit\PrelineForm;

use Laravolt\PrelineForm\Elements\Text;
use Laravolt\Tests\UnitTest;

class InputMaskTest extends UnitTest
{
    public function test_text_input_can_use_named_mask_preset(): void
    {
        $html = (string) (new Text('phone'))->mask('phone');

        $this->assertStringContainsString('name="phone"', $html);
        $this->assertStringContainsString('data-mask="phone"', $html);
        $this->assertSame('(+99) 9999-9999[9]', $this->inputmaskOptions($html)['mask']);
        $this->assertStringContainsString('inputmode="tel"', $html);
    }

    public function test_currency_mask_renders_inputmask_options(): void
    {
        $html = (string) (new Text('amount'))->mask('currency');
        $options = $this->inputmaskOptions($html);

        $this->assertSame('currency', $options['alias']);
        $this->assertSame('Rp ', $options['prefix']);
        $this->assertSame('.', $options['groupSeparator']);
        $this->assertSame(',', $options['radixPoint']);
        $this->assertSame(0, $options['digits']);
        $this->assertFalse($options['rightAlign']);
        $this->assertTrue($options['removeMaskOnSubmit']);
        $this->assertStringContainsString('inputmode="decimal"', $html);
    }

    public function test_date_mask_can_be_overridden_with_custom_options(): void
    {
        $html = (string) (new Text('birthday'))->mask('date', [
            'inputFormat' => 'dd/mm/yyyy',
            'placeholder' => 'dd/mm/yyyy',
        ]);
        $options = $this->inputmaskOptions($html);

        $this->assertSame('datetime', $options['alias']);
        $this->assertSame('dd/mm/yyyy', $options['inputFormat']);
        $this->assertSame('dd/mm/yyyy', $options['placeholder']);
    }

    public function test_custom_mask_string_is_supported(): void
    {
        $html = (string) (new Text('sku'))->mask('AAA-9999');
        $options = $this->inputmaskOptions($html);

        $this->assertStringContainsString('data-mask="AAA-9999"', $html);
        $this->assertSame('AAA-9999', $options['mask']);
    }

    public function test_raw_inputmask_options_are_supported(): void
    {
        $html = (string) (new Text('code'))->inputmask([
            'mask' => '999-AAA',
            'casing' => 'upper',
        ]);
        $options = $this->inputmaskOptions($html);

        $this->assertStringContainsString('data-mask="custom"', $html);
        $this->assertSame('999-AAA', $options['mask']);
        $this->assertSame('upper', $options['casing']);
    }

    public function test_mask_can_be_removed(): void
    {
        $html = (string) (new Text('phone'))->mask('phone')->unmask();

        $this->assertStringNotContainsString('data-mask=', $html);
        $this->assertStringNotContainsString('data-inputmask=', $html);
        $this->assertStringNotContainsString('inputmode=', $html);
    }

    private function inputmaskOptions(string $html): array
    {
        preg_match('/data-inputmask="([^"]+)"/', $html, $matches);

        $this->assertNotEmpty($matches[1] ?? null, 'Missing data-inputmask attribute.');

        return json_decode(html_entity_decode($matches[1], ENT_QUOTES), true);
    }
}
