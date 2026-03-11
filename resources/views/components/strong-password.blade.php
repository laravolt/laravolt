@php
    $id = $id ?? 'strong-password-' . uniqid();
    $name = $name ?? 'password';
    $placeholder = $placeholder ?? 'Enter password';
    $minLength = $minLength ?? 8;
    $specialChars = $specialChars ?? true;
    $numbers = $numbers ?? true;
    $uppercase = $uppercase ?? true;
    $lowercase = $lowercase ?? true;
    $hsStrongPasswordConfig = json_encode(array_filter([
        'target' => '#' . $id,
        'hints' => '#' . $id . '-hints',
        'stripClasses' => 'hs-strong-password:opacity-100 hs-strong-password-accepted:bg-teal-500 h-2 flex-auto rounded-full bg-blue-500 opacity-50 mx-1',
        'minLength' => $minLength,
    ]));
@endphp

<div {{ $attributes->except(['min-length', 'special-chars', 'numbers', 'uppercase', 'lowercase', 'placeholder']) }}>
    <div class="relative">
        <input
            type="password"
            id="{{ $id }}"
            name="{{ $name }}"
            class="py-2 px-3 pe-10 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
            placeholder="{{ $placeholder }}"
            oninput="checkPasswordStrength_{{ str_replace('-','_',$id) }}(this.value)"
        >
    </div>

    {{-- Strength bars --}}
    <div id="{{ $id }}-bars" class="flex mt-2 -mx-1">
        <div class="w-1/4"><div class="h-2 rounded-full bg-gray-200 dark:bg-neutral-700 mx-1" id="{{ $id }}-bar-1"></div></div>
        <div class="w-1/4"><div class="h-2 rounded-full bg-gray-200 dark:bg-neutral-700 mx-1" id="{{ $id }}-bar-2"></div></div>
        <div class="w-1/4"><div class="h-2 rounded-full bg-gray-200 dark:bg-neutral-700 mx-1" id="{{ $id }}-bar-3"></div></div>
        <div class="w-1/4"><div class="h-2 rounded-full bg-gray-200 dark:bg-neutral-700 mx-1" id="{{ $id }}-bar-4"></div></div>
    </div>

    {{-- Hints --}}
    <div id="{{ $id }}-hints" class="mt-2">
        <div class="flex items-center gap-x-1.5">
            <span class="text-xs text-gray-500 dark:text-neutral-400">Requirements:</span>
        </div>
        <ul class="mt-1 space-y-1 text-xs text-gray-500 dark:text-neutral-500">
            @if($minLength)
                <li id="{{ $id }}-hint-length" class="flex items-center gap-x-1.5">
                    <svg class="shrink-0 size-3 text-gray-400" id="{{ $id }}-check-length" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Min {{ $minLength }} characters
                </li>
            @endif
            @if($numbers)
                <li id="{{ $id }}-hint-number" class="flex items-center gap-x-1.5">
                    <svg class="shrink-0 size-3 text-gray-400" id="{{ $id }}-check-number" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Contains number
                </li>
            @endif
            @if($lowercase)
                <li id="{{ $id }}-hint-lower" class="flex items-center gap-x-1.5">
                    <svg class="shrink-0 size-3 text-gray-400" id="{{ $id }}-check-lower" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Lowercase letter
                </li>
            @endif
            @if($uppercase)
                <li id="{{ $id }}-hint-upper" class="flex items-center gap-x-1.5">
                    <svg class="shrink-0 size-3 text-gray-400" id="{{ $id }}-check-upper" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Uppercase letter
                </li>
            @endif
            @if($specialChars)
                <li id="{{ $id }}-hint-special" class="flex items-center gap-x-1.5">
                    <svg class="shrink-0 size-3 text-gray-400" id="{{ $id }}-check-special" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Special character (!@#$...)
                </li>
            @endif
        </ul>
    </div>
</div>

@pushOnce('strong-password-scripts')
<script>
function checkPasswordStrength_{{ str_replace('-','_',$id) }}(val) {
    var score = 0;
    var checks = {
        @if($minLength) length: val.length >= {{ $minLength }}, @endif
        @if($numbers) number: /\d/.test(val), @endif
        @if($lowercase) lower: /[a-z]/.test(val), @endif
        @if($uppercase) upper: /[A-Z]/.test(val), @endif
        @if($specialChars) special: /[!@#$%^&*(),.?":{}|<>]/.test(val), @endif
    };
    var colors = ['bg-red-500','bg-orange-400','bg-yellow-400','bg-teal-500'];
    Object.keys(checks).forEach(function(k) {
        var el = document.getElementById('{{ $id }}-check-' + k);
        if (el) el.style.color = checks[k] ? '#0d9488' : '#9ca3af';
        if (checks[k]) score++;
    });
    var total = Object.keys(checks).length;
    var level = total > 0 ? Math.ceil((score / total) * 4) : 0;
    for (var i = 1; i <= 4; i++) {
        var bar = document.getElementById('{{ $id }}-bar-' + i);
        if (bar) bar.className = 'h-2 rounded-full mx-1 transition-all ' + (i <= level ? colors[level-1] : 'bg-gray-200 dark:bg-neutral-700');
    }
}
</script>
@endPushOnce
