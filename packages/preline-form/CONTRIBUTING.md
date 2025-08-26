# Contributing to PrelineForm

Thank you for considering contributing to PrelineForm! This document outlines the process and guidelines for contributing.

## 🤝 How to Contribute

### 1. Fork the Repository

```bash
git clone https://github.com/laravolt/laravolt
cd packages/preline-form
```

### 2. Set Up Development Environment

```bash
# Install PHP dependencies
composer install

# Run tests to ensure everything works
composer test
```

### 3. Create a Feature Branch

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b fix/bug-description
```

## 📋 Development Guidelines

### Code Standards

- **PSR-12**: Follow PSR-12 coding standards
- **PHPDoc**: Add comprehensive documentation for all public methods
- **Type Hints**: Use PHP type hints where applicable
- **Testing**: Write tests for new functionality

### File Structure

```
src/
├── Elements/           # Form element classes
├── Contracts/         # Interfaces
├── ErrorStore/        # Error handling
├── OldInput/          # Old input handling
├── PrelineForm.php    # Main form class
├── FieldCollection.php # Dynamic field generation
├── ServiceProvider.php # Laravel service provider
└── helpers.php        # Helper functions
```

### Naming Conventions

- **Classes**: PascalCase (`TextInput`, `FormOpen`)
- **Methods**: camelCase (`setText`, `addClass`)
- **Variables**: camelCase (`$fieldName`, `$errorMessage`)
- **Constants**: UPPER_SNAKE_CASE (`DEFAULT_CLASS`)

## 🧪 Testing

### Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage

# Run specific test
vendor/bin/phpunit tests/Elements/TextTest.php
```

### Writing Tests

Create tests for new elements or functionality:

```php
<?php

namespace Laravolt\PrelineForm\Test\Elements;

use Laravolt\PrelineForm\Elements\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function testCanCreateTextInput()
    {
        $text = new Text('username');
        $this->assertInstanceOf(Text::class, $text);
    }

    public function testCanSetAttributes()
    {
        $text = new Text('username');
        $text->attributes(['class' => 'custom', 'data-test' => 'value']);

        $html = $text->render();
        $this->assertStringContainsString('class="custom"', $html);
        $this->assertStringContainsString('data-test="value"', $html);
    }
}
```

## 🎨 Styling Guidelines

### Tailwind CSS Classes

Use consistent Tailwind CSS classes:

```php
// Default input styling
'py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500'

// Error state
'border-red-500 focus:border-red-500 focus:ring-red-500'

// Success state
'border-green-500 focus:border-green-500 focus:ring-green-500'
```

### Dark Mode Support

Always include dark mode classes:

```php
'dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400'
```

### Responsive Design

Use responsive prefixes when needed:

```php
'w-full md:w-auto lg:max-w-md'
```

## 📝 Documentation

### PHPDoc Comments

```php
/**
 * Create a text input element.
 *
 * @param string $name The input name attribute
 * @param mixed $defaultValue Default value for the input
 * @return Text
 */
public function text(string $name, mixed $defaultValue = null): Text
{
    // Implementation
}
```

### README Updates

When adding new features:

1. Add to the API reference section
2. Include usage examples
3. Update the table of contents
4. Add to appropriate collapsible sections

## 🐛 Bug Reports

### Before Submitting

1. Check if the issue already exists
2. Test with the latest version
3. Provide minimal reproduction steps

### Bug Report Template

````markdown
## Bug Description

Brief description of the issue

## Steps to Reproduce

1. Step one
2. Step two
3. Expected vs actual result

## Environment

- PHP version:
- Laravel version:
- PrelineForm version:
- Browser (if relevant):

## Code Example

```php
// Minimal code that reproduces the issue
```
````

## Expected Behavior

What should happen

## Actual Behavior

What actually happens

````

## ✨ Feature Requests

### Feature Request Template

```markdown
## Feature Description
Brief description of the proposed feature

## Use Case
Why is this feature needed? What problem does it solve?

## Proposed API
```php
// Example of how the feature would be used
PrelineForm::newFeature('param')->option('value')
````

## Implementation Ideas

Any thoughts on how this could be implemented

## Alternatives Considered

Other solutions you've considered

````

## 🔄 Pull Request Process

### Before Submitting

1. **Tests Pass**: Ensure all tests pass
2. **Code Style**: Run code style checks
3. **Documentation**: Update relevant documentation
4. **Backwards Compatibility**: Maintain API compatibility

### Pull Request Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that causes existing functionality to change)
- [ ] Documentation update

## Testing
- [ ] Tests added/updated for new functionality
- [ ] All tests pass
- [ ] Manual testing completed

## Checklist
- [ ] Code follows PSR-12 standards
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] Backwards compatibility maintained
````

## 🔍 Code Review Process

### What We Look For

1. **Code Quality**: Clean, readable, well-documented code
2. **Testing**: Adequate test coverage for new functionality
3. **Compatibility**: Maintains API compatibility with SemanticForm
4. **Performance**: No significant performance regressions
5. **Security**: No security vulnerabilities introduced

### Review Criteria

- ✅ Code follows established patterns
- ✅ Tests are comprehensive and pass
- ✅ Documentation is clear and complete
- ✅ No breaking changes without discussion
- ✅ Styling follows Preline UI patterns

## 🚀 Release Process

### Versioning

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR**: Breaking changes
- **MINOR**: New features (backwards compatible)
- **PATCH**: Bug fixes (backwards compatible)

### Changelog

Update `CHANGELOG.md` with:

- **Added**: New features
- **Changed**: Changes in existing functionality
- **Deprecated**: Soon-to-be removed features
- **Removed**: Removed features
- **Fixed**: Bug fixes
- **Security**: Security improvements

## 💬 Communication

### Channels

- **GitHub Issues**: Bug reports and feature requests
- **GitHub Discussions**: General questions and ideas
- **Pull Requests**: Code contributions

### Guidelines

- Be respectful and constructive
- Provide clear and detailed information
- Use appropriate labels and templates
- Follow up on your contributions

## 🏆 Recognition

Contributors will be:

- Listed in the repository contributors
- Mentioned in release notes for significant contributions
- Added to the credits section of documentation

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Preline UI Documentation](https://preline.co/docs)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

Thank you for contributing to PrelineForm! 🎉
