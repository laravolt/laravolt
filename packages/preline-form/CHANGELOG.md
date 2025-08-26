# Changelog

All notable changes to `preline-form` will be documented in this file.

## [Unreleased]

### Added

- **🔄 SemanticForm API Compatibility**: Full compatibility with SemanticForm API for smooth migration
  - Added `attributes()` method to all form elements for setting multiple attributes at once
  - Added `horizontal()` method to FormOpen for horizontal form layouts
  - Added `make()` method for dynamic field generation with FieldCollection support
  - Added `hasError()` and `getError()` methods for validation error handling
  - Added compatibility methods for checkboxes and radio buttons (`setChecked()`, `defaultCheckedState()`, `check()`, `uncheck()`)
- **🆕 New Form Elements**:
  - `color()` for color picker inputs
  - `html()` for custom HTML content insertion
  - `link()` for link elements with styling support
  - Enhanced `date()` and `time()` input types with proper styling
- **📚 Enhanced Documentation**:
  - Comprehensive README with visual examples and advanced features
  - Migration guide (MIGRATION.md) for smooth transition from SemanticForm
  - Contributing guidelines (CONTRIBUTING.md) with development setup
  - Complete API reference with collapsible sections
  - Troubleshooting section with common issues and solutions
  - Multiple real-world examples (user registration, product creation)
- **🔧 New Utility Features**:
  - Dynamic field generation via `make()` method
  - Enhanced error handling and validation display
  - Better form layout options (horizontal, grid, custom classes)
  - File upload with multiple file support and validation

### Improved

- **🎨 Better Styling System**:
  - Enhanced dark mode support across all elements
  - Improved accessibility features with proper ARIA attributes
  - More consistent Tailwind CSS class usage
  - Better responsive design patterns
- **🔧 Developer Experience**:
  - More comprehensive test coverage
  - Better code organization and documentation
  - Enhanced IDE support with proper type hints
  - Improved error messages and debugging

### Fixed

- Form element rendering consistency issues
- CSS class conflicts between Tailwind and Preline UI
- JavaScript integration problems with Preline UI components
- Error state styling and display issues

## [1.0.0] - 2024-01-01

### Added

- Initial release of Preline Form package
- Support for all basic form elements (text, email, password, textarea, select, etc.)
- Checkbox and radio button support with grouping
- File upload support
- Button elements with multiple styling variants
- Form validation error display
- Model binding support
- Input wrapper with icon and label support
- Preline UI and Tailwind CSS styling
- Dark mode support
- Comprehensive API compatible with Semantic Form

### Features

- **Form Elements**: Text, Email, Password, Number, Date, Time, Color, Textarea, Select, Multiple Select
- **Interactive Elements**: Checkbox, Radio Button, Checkbox Group, Radio Group
- **File Handling**: File input with multiple file support
- **Buttons**: Submit, Button with Primary, Secondary, Danger, Success variants
- **Layout**: Field wrappers, proper spacing, form containers
- **Validation**: Automatic error handling and display
- **Styling**: Preline UI design system with Tailwind CSS
- **Accessibility**: Proper labeling and ARIA support
- **Dark Mode**: Full dark mode compatibility
