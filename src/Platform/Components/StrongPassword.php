<?php
declare(strict_types=1);
namespace Laravolt\Platform\Components;
use Illuminate\View\Component;

class StrongPassword extends Component
{
    public string $id;
    public string $name;
    public ?string $placeholder;
    public int $minLength;
    public bool $specialChars;
    public bool $numbers;
    public bool $uppercase;
    public bool $lowercase;

    public function __construct(?string $id = null, ?string $name = null, ?string $placeholder = null, ?int $minLength = null, ?bool $specialChars = null, ?bool $numbers = null, ?bool $uppercase = null, ?bool $lowercase = null)
    {
        $this->id = $id ?? 'strong-password-' . uniqid();
        $this->name = $name ?? 'password';
        $this->placeholder = $placeholder ?? 'Enter password';
        $this->minLength = $minLength ?? 8;
        $this->specialChars = $specialChars ?? true;
        $this->numbers = $numbers ?? true;
        $this->uppercase = $uppercase ?? true;
        $this->lowercase = $lowercase ?? true;
    }

    public function render() { return view('laravolt::components.strong-password'); }
}
