<?php

declare(strict_types=1);

namespace Laravolt\PrelineForm\Concerns;

trait HasInputMask
{
    /**
     * Apply an Inputmask-compatible mask definition to the form control.
     *
     * Named presets are available for common cases: phone, currency, date,
     * datetime, time, and email. Any other string is treated as a custom
     * Inputmask mask pattern.
     */
    public function mask(string|array $mask, array $options = []): static
    {
        $definition = is_array($mask)
            ? $mask
            : $this->maskDefinition($mask);

        $definition = array_merge($definition, $options);

        $this->setAttribute('data-mask', is_string($mask) ? $mask : 'custom');
        $this->setAttribute('data-inputmask', $definition);

        if ($mask === 'phone') {
            $this->setAttribute('inputmode', 'tel');
        } elseif (($definition['alias'] ?? null) === 'currency') {
            $this->setAttribute('inputmode', 'decimal');
        } elseif (isset($definition['mask']) && $this->looksNumericMask((string) $definition['mask'])) {
            $this->setAttribute('inputmode', 'numeric');
        }

        return $this;
    }

    /**
     * Apply a raw Inputmask options array.
     */
    public function inputmask(array $options): static
    {
        return $this->mask($options);
    }

    /**
     * Remove previously configured mask attributes.
     */
    public function unmask(): static
    {
        $this->removeAttribute('data-mask');
        $this->removeAttribute('data-inputmask');
        $this->removeAttribute('inputmode');

        return $this;
    }

    protected function maskDefinition(string $mask): array
    {
        return match ($mask) {
            'phone' => [
                'mask' => '(+99) 9999-9999[9]',
                'placeholder' => '_',
            ],
            'currency' => [
                'alias' => 'currency',
                'prefix' => 'Rp ',
                'groupSeparator' => '.',
                'radixPoint' => ',',
                'digits' => 0,
                'rightAlign' => false,
                'autoUnmask' => true,
                'removeMaskOnSubmit' => true,
            ],
            'date' => [
                'alias' => 'datetime',
                'inputFormat' => 'yyyy-mm-dd',
                'placeholder' => 'yyyy-mm-dd',
            ],
            'datetime' => [
                'alias' => 'datetime',
                'inputFormat' => 'yyyy-mm-dd HH:MM',
                'placeholder' => 'yyyy-mm-dd HH:MM',
            ],
            'time' => [
                'alias' => 'datetime',
                'inputFormat' => 'HH:MM',
                'placeholder' => 'HH:MM',
            ],
            'email' => ['alias' => 'email'],
            default => ['mask' => $mask],
        };
    }

    protected function looksNumericMask(string $mask): bool
    {
        return preg_match('/^[0-9+()\- ._{}\[\]]+$/', $mask) === 1;
    }
}
