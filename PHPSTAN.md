# PHPStan Level 9 Configuration

This project is configured with **PHPStan Level 9** for maximum static analysis coverage and developer experience enhancement.

## 🚀 Quick Start

### Installation & Setup

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Run PHPStan analysis:**
   ```bash
   composer phpstan
   ```

3. **Generate baseline (if needed):**
   ```bash
   composer phpstan:baseline
   ```

## 📋 Available Commands

| Command | Description |
|---------|-------------|
| `composer phpstan` | Run full PHPStan analysis |
| `composer phpstan:baseline` | Generate baseline for existing issues |
| `composer phpstan:clear` | Clear PHPStan result cache |
| `composer phpstan:pro` | Run PHPStan Pro with advanced features |
| `composer code:analyse` | Run both PHPStan and Laravel Pint |
| `composer code:fix` | Fix code style and clear PHPStan cache |

## 🎯 Configuration Levels

### Root Project (`phpstan.neon`)
- **Level 9** - Maximum strictness
- Analyzes: `src/`, `packages/`, `config/`, `database/migrations/`, `routes/`, `tests/`
- Excludes: vendor directories, views, cache directories

### Individual Packages
- **Semantic Form**: `packages/semantic-form/phpstan.neon`
- **Preline Form**: `packages/preline-form/phpstan.neon`
- **All Packages**: `packages/phpstan.neon`

## 🔧 IDE Integration

### VS Code
The project includes comprehensive VS Code configuration:

- **Extensions**: PHPStan, Intelephense, Laravel support
- **Tasks**: Run PHPStan directly from VS Code
- **Settings**: Automatic formatting and validation

**Recommended Extensions:**
- `swordev.phpstan` - PHPStan extension
- `bmewburn.vscode-intelephense-client` - PHP IntelliSense
- `ryannaddy.laravel-artisan` - Laravel Artisan commands

### Other IDEs
For PHPStorm, Vim, or other editors, use the command line tools:
```bash
# Run analysis
./vendor/bin/phpstan analyse --memory-limit=2G

# With specific configuration
./vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=2G
```

## 🤖 CI/CD Integration

### GitHub Actions
Automated PHPStan checks run on:
- Push to `main` or `develop` branches
- Pull requests
- PHP versions 8.2 and 8.3
- Includes PHPStan Pro for PR analysis

### Local Pre-commit
Set up pre-commit hooks:
```bash
# Add to .git/hooks/pre-commit
#!/bin/sh
composer code:analyse
```

## ⚙️ Configuration Details

### Strict Type Checking
- `treatPhpDocTypesAsCertain: true`
- `reportMaybes: true`
- `checkExplicitMixed: true`
- `checkUninitializedProperties: true`
- `checkDynamicProperties: true`

### Laravel-Specific Rules
- Model relationship type checking
- Query builder method validation
- Service container resolution
- Configuration array access patterns

### Disallowed Functions
The following functions are prohibited:
- `dd()`, `dump()`, `var_dump()` - Use proper logging
- `exit()`, `die()` - Use exception handling
- `env()` - Use `config()` instead
- `eval()`, `exec()`, `shell_exec()` - Security risks

## 🎨 Code Quality Rules

### Type Declarations
```php
// ✅ Good - Explicit types
public function processUser(User $user): UserData
{
    return new UserData($user->name, $user->email);
}

// ❌ Bad - Missing types
public function processUser($user)
{
    return new UserData($user->name, $user->email);
}
```

### Array Type Hints
```php
// ✅ Good - Specific array types
/** @param array<string, mixed> $config */
public function configure(array $config): void

// ❌ Bad - Generic array
public function configure(array $config): void
```

### Null Safety
```php
// ✅ Good - Null-safe operations
public function getName(): ?string
{
    return $this->user?->name;
}

// ❌ Bad - Potential null pointer
public function getName(): string
{
    return $this->user->name; // May be null
}
```

## 🐛 Common Issues & Solutions

### Issue: "Cannot access offset on mixed"
```php
// ❌ Problem
$value = $array['key'];

// ✅ Solution
$value = $array['key'] ?? null;
// or
assert(is_array($array));
$value = $array['key'];
```

### Issue: "Method not found on Builder"
```php
// ❌ Problem
$users = User::whereActive()->get();

// ✅ Solution - Add proper scope
class User extends Model
{
    public function scopeWhereActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
```

### Issue: "Property access on mixed"
```php
// ❌ Problem
$user = auth()->user();
echo $user->name;

// ✅ Solution
/** @var User $user */
$user = auth()->user();
echo $user->name;
```

## 📊 Performance Tips

1. **Use result cache**: PHPStan caches results for faster subsequent runs
2. **Memory limit**: Set `--memory-limit=2G` for large codebases
3. **Parallel processing**: PHPStan automatically uses multiple cores
4. **Baseline**: Use baseline for gradual adoption in existing projects

## 🔍 Debugging PHPStan

### Enable Debug Mode
```bash
./vendor/bin/phpstan analyse --debug
```

### Check Configuration
```bash
./vendor/bin/phpstan dump-config
```

### Memory Usage
```bash
./vendor/bin/phpstan analyse --memory-limit=4G --debug
```

## 📈 Gradual Adoption

If Level 9 is too strict initially:

1. **Start with Level 6-8**
2. **Generate baseline**: `composer phpstan:baseline`
3. **Fix issues gradually**
4. **Remove baseline entries as you fix them**
5. **Increase level when ready**

## 🤝 Contributing

When contributing code:

1. **Run PHPStan**: `composer phpstan`
2. **Fix all issues** before submitting PR
3. **Add type hints** to new code
4. **Update tests** if needed
5. **Check CI passes**

## 📚 Resources

- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [Larastan Documentation](https://github.com/larastan/larastan)
- [Laravel Type Hints](https://laravel.com/docs/11.x/helpers#method-array-add)
- [PHP Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)

---

**Level 9 Achievement Unlocked! 🎉**

Your codebase now has maximum static analysis coverage for the best possible developer experience.