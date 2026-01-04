<?php

declare(strict_types=1);

namespace Laravolt\Platform\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ComponentShowcaseController extends Controller
{
    public function index()
    {
        return view('laravolt::showcase.index');
    }

    public function component(Request $request, string $component)
    {
        $components = $this->getAvailableComponents();

        if (! isset($components[$component])) {
            abort(404, 'Component not found');
        }

        $componentData = $components[$component];
        $sampleData = $this->getSampleData($component);

        return view('laravolt::showcase.component', compact('component', 'componentData', 'sampleData'));
    }

    public function getAvailableComponents(): array
    {
        return [
            // Form Components
            'alert' => [
                'name' => 'Alert',
                'category' => 'Feedback',
                'description' => 'Display important messages and notifications to users',
                'variants' => ['success', 'error', 'warning', 'info', 'gray'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Dismissible', 'Icons', 'Custom styling'],
            ],
            'button' => [
                'name' => 'Button',
                'category' => 'Form',
                'description' => 'Interactive buttons with multiple variants and states',
                'variants' => ['primary', 'secondary', 'soft', 'outline', 'ghost', 'link', 'danger', 'success'],
                'sizes' => ['2xs', 'xs', 'sm', 'md', 'lg', 'xl'],
                'features' => ['Loading states', 'Icons', 'Pill shape', 'Disabled state'],
            ],
            'input' => [
                'name' => 'Input',
                'category' => 'Form',
                'description' => 'Text input fields with validation states',
                'variants' => ['default', 'error', 'success'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Icons', 'Helper text', 'Error states'],
            ],
            'switch' => [
                'name' => 'Switch',
                'category' => 'Form',
                'description' => 'Toggle switches for boolean inputs',
                'variants' => ['primary', 'success', 'warning', 'danger', 'gray'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Labels', 'Descriptions', 'Disabled state'],
            ],
            'checkbox' => [
                'name' => 'Checkbox',
                'category' => 'Form',
                'description' => 'Checkbox inputs for multiple selections',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Indeterminate state', 'Disabled state'],
            ],
            'radio' => [
                'name' => 'Radio',
                'category' => 'Form',
                'description' => 'Radio buttons for single selection from options',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Disabled state', 'Grouped options'],
            ],
            'select' => [
                'name' => 'Select',
                'category' => 'Form',
                'description' => 'Dropdown select for choosing from options',
                'variants' => ['default', 'error', 'success'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Multiple selection', 'Search', 'Disabled state'],
            ],
            'textarea' => [
                'name' => 'Textarea',
                'category' => 'Form',
                'description' => 'Multi-line text input areas',
                'variants' => ['default', 'error', 'success'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Auto-resize', 'Character count', 'Disabled state'],
            ],
            'file-input' => [
                'name' => 'File Input',
                'category' => 'Form',
                'description' => 'File upload inputs with drag and drop',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Drag & drop', 'Multiple files', 'File type validation'],
            ],
            'pin-code' => [
                'name' => 'PIN Code',
                'category' => 'Form',
                'description' => 'Secure PIN/OTP input fields',
                'variants' => ['default', 'success', 'error'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Auto-focus', 'Masking', 'Paste support'],
            ],
            'rating' => [
                'name' => 'Rating',
                'category' => 'Form',
                'description' => 'Star rating input and display',
                'variants' => ['yellow', 'orange', 'red', 'blue'],
                'sizes' => ['xs', 'sm', 'md', 'lg', 'xl'],
                'features' => ['Interactive', 'Half stars', 'Read-only mode'],
            ],
            'time-picker' => [
                'name' => 'Time Picker',
                'category' => 'Form',
                'description' => 'Time selection input with picker',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['12h/24h format', 'Clearable', 'Custom format'],
            ],
            'advanced-datepicker' => [
                'name' => 'Advanced Datepicker',
                'category' => 'Form',
                'description' => 'Advanced date picker with range support',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Date ranges', 'Min/max dates', 'Custom format'],
            ],

            // Layout Components
            'card' => [
                'name' => 'Card',
                'category' => 'Layout',
                'description' => 'Container for grouping related content',
                'variants' => ['default', 'shadow', 'bordered'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Header', 'Footer', 'Actions'],
            ],
            'panel' => [
                'name' => 'Panel',
                'category' => 'Layout',
                'description' => 'Flexible content containers',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Header', 'Footer', 'Collapsible'],
            ],
            'modal' => [
                'name' => 'Modal',
                'category' => 'Layout',
                'description' => 'Overlay dialogs for focused content',
                'variants' => ['default'],
                'sizes' => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl', 'full'],
                'features' => ['Header', 'Footer', 'Scrollable', 'Centered'],
            ],
            'offcanvas' => [
                'name' => 'Offcanvas',
                'category' => 'Layout',
                'description' => 'Slide-out panels for navigation or content',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Multiple positions', 'Backdrop', 'Auto-close'],
            ],
            'sidebar' => [
                'name' => 'Sidebar',
                'category' => 'Layout',
                'description' => 'Navigation sidebar components',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Collapsible', 'Icons', 'Multi-level'],
            ],
            'accordion' => [
                'name' => 'Accordion',
                'category' => 'Layout',
                'description' => 'Expandable content sections',
                'variants' => ['default', 'light', 'shadow'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Multi-panel', 'Icons', 'Flush design'],
            ],

            // Navigation Components
            'breadcrumb' => [
                'name' => 'Breadcrumb',
                'category' => 'Navigation',
                'description' => 'Hierarchical navigation trail',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Custom separators', 'Icons', 'Dropdown'],
            ],
            'pagination' => [
                'name' => 'Pagination',
                'category' => 'Navigation',
                'description' => 'Navigate through pages of content',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Previous/Next', 'Page numbers', 'Jump to page'],
            ],
            'tab' => [
                'name' => 'Tabs',
                'category' => 'Navigation',
                'description' => 'Tabbed navigation interface',
                'variants' => ['default', 'pills', 'underline'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Icons', 'Badges', 'Disabled tabs'],
            ],
            'stepper' => [
                'name' => 'Stepper',
                'category' => 'Navigation',
                'description' => 'Step-by-step navigation',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Linear', 'Non-linear', 'Custom icons'],
            ],
            'steps' => [
                'name' => 'Steps',
                'category' => 'Navigation',
                'description' => 'Visual progress through steps',
                'variants' => ['default', 'success'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Clickable', 'Icons', 'Descriptions'],
            ],

            // Data Display
            'table' => [
                'name' => 'Table',
                'category' => 'Data Display',
                'description' => 'Structured data in rows and columns',
                'variants' => ['default', 'striped', 'bordered'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Sortable', 'Responsive', 'Actions'],
            ],
            'badge' => [
                'name' => 'Badge',
                'category' => 'Data Display',
                'description' => 'Small status indicators and labels',
                'variants' => ['primary', 'secondary', 'success', 'danger', 'warning', 'info'],
                'sizes' => ['xs', 'sm', 'md', 'lg'],
                'features' => ['Dot variant', 'Removable', 'Icons'],
            ],
            'avatar' => [
                'name' => 'Avatar',
                'category' => 'Data Display',
                'description' => 'User profile images and initials',
                'variants' => ['circle', 'square'],
                'sizes' => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'],
                'features' => ['Initials fallback', 'Status indicator', 'Groups'],
            ],
            'timeline' => [
                'name' => 'Timeline',
                'category' => 'Data Display',
                'description' => 'Chronological display of events',
                'variants' => ['default', 'primary', 'success'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Icons', 'Status indicators', 'Connectors'],
            ],
            'list-group' => [
                'name' => 'List Group',
                'category' => 'Data Display',
                'description' => 'Flexible component for displaying lists',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Actions', 'Badges', 'Custom content'],
            ],

            // Feedback Components
            'toast' => [
                'name' => 'Toast',
                'category' => 'Feedback',
                'description' => 'Brief notification messages',
                'variants' => ['success', 'error', 'warning', 'info'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Auto-dismiss', 'Actions', 'Positioning'],
            ],
            'notification' => [
                'name' => 'Notification',
                'category' => 'Feedback',
                'description' => 'Rich notification with actions',
                'variants' => ['success', 'error', 'warning', 'info'],
                'sizes' => [],
                'features' => ['Actions', 'Auto-hide', 'Positioning'],
            ],
            'progress' => [
                'name' => 'Progress',
                'category' => 'Feedback',
                'description' => 'Progress indicators and bars',
                'variants' => ['default', 'striped', 'animated'],
                'sizes' => ['xs', 'sm', 'md', 'lg'],
                'features' => ['Indeterminate', 'Labels', 'Colors'],
            ],
            'skeleton' => [
                'name' => 'Skeleton',
                'category' => 'Feedback',
                'description' => 'Loading placeholders',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Text', 'Avatar', 'Custom shapes'],
            ],
            'scroll-indicator' => [
                'name' => 'Scroll Indicator',
                'category' => 'Feedback',
                'description' => 'Page scroll progress indicator',
                'variants' => ['top', 'bottom'],
                'sizes' => ['xs', 'sm', 'md', 'lg', 'xl'],
                'features' => ['Colors', 'Positioning', 'Custom targets'],
            ],

            // Utility Components
            'dropdown' => [
                'name' => 'Dropdown',
                'category' => 'Utility',
                'description' => 'Contextual overlays with actions',
                'variants' => ['default'],
                'sizes' => [],
                'features' => ['Positioning', 'Auto-close', 'Floating UI'],
            ],
            'popover' => [
                'name' => 'Popover',
                'category' => 'Utility',
                'description' => 'Rich content overlays',
                'variants' => ['default'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Positioning', 'Triggers', 'Rich content'],
            ],
            'tooltip' => [
                'name' => 'Tooltip',
                'category' => 'Utility',
                'description' => 'Helpful text on hover',
                'variants' => ['default', 'dark', 'light'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Positioning', 'Delays', 'Rich content'],
            ],
            'copy-markup' => [
                'name' => 'Copy Markup',
                'category' => 'Utility',
                'description' => 'Code display with copy functionality',
                'variants' => ['light', 'dark'],
                'sizes' => ['sm', 'md', 'lg'],
                'features' => ['Syntax highlighting', 'Line numbers', 'Copy button'],
            ],
        ];
    }

    protected function getSampleData(string $component): array
    {
        $sampleData = [
            'alert' => [
                'examples' => [
                    ['variant' => 'success', 'title' => 'Success!', 'message' => 'Your changes have been saved successfully.', 'dismissible' => true],
                    ['variant' => 'error', 'title' => 'Error!', 'message' => 'There was a problem processing your request.', 'dismissible' => true],
                    ['variant' => 'warning', 'title' => 'Warning!', 'message' => 'Please review your information before proceeding.', 'dismissible' => false],
                    ['variant' => 'info', 'title' => 'Information', 'message' => 'New features are now available in your dashboard.', 'dismissible' => true],
                ],
            ],
            'button' => [
                'examples' => [
                    ['variant' => 'primary', 'label' => 'Primary Button'],
                    ['variant' => 'secondary', 'label' => 'Secondary Button'],
                    ['variant' => 'outline', 'label' => 'Outline Button'],
                    ['variant' => 'ghost', 'label' => 'Ghost Button'],
                    ['variant' => 'danger', 'label' => 'Danger Button'],
                    ['variant' => 'success', 'label' => 'Success Button', 'loading' => false],
                ],
            ],
            'accordion' => [
                'items' => [
                    [
                        'title' => 'Getting Started',
                        'content' => 'Learn how to integrate and use our components in your Laravel application. This section covers installation, configuration, and basic usage patterns.',
                        'open' => true,
                    ],
                    [
                        'title' => 'Advanced Features',
                        'content' => 'Explore advanced component features including custom styling, event handling, and integration with other libraries.',
                    ],
                    [
                        'title' => 'Best Practices',
                        'content' => 'Follow our recommended best practices for performance, accessibility, and maintainability when using Preline UI components.',
                    ],
                ],
            ],
            'timeline' => [
                'items' => [
                    [
                        'title' => 'Project Planning',
                        'description' => 'Initial project setup and requirements gathering',
                        'timestamp' => '2024-01-15',
                        'status' => 'completed',
                    ],
                    [
                        'title' => 'Development Phase',
                        'description' => 'Core functionality implementation and testing',
                        'timestamp' => '2024-02-01',
                        'status' => 'completed',
                    ],
                    [
                        'title' => 'Testing & QA',
                        'description' => 'Comprehensive testing and quality assurance',
                        'timestamp' => '2024-02-15',
                        'status' => 'current',
                    ],
                    [
                        'title' => 'Deployment',
                        'description' => 'Production deployment and monitoring',
                        'timestamp' => '2024-03-01',
                        'status' => 'pending',
                    ],
                ],
            ],
            'steps' => [
                'steps' => [
                    ['title' => 'Account Setup', 'description' => 'Create your account'],
                    ['title' => 'Profile Information', 'description' => 'Add your details'],
                    ['title' => 'Preferences', 'description' => 'Configure settings'],
                    ['title' => 'Completion', 'description' => 'Review and finish'],
                ],
                'currentStep' => 2,
            ],
            'rating' => [
                'examples' => [
                    ['value' => 4.5, 'readonly' => true, 'showCount' => true, 'count' => 128],
                    ['value' => 3, 'readonly' => false, 'max' => 5],
                    ['value' => 5, 'readonly' => true, 'variant' => 'yellow'],
                ],
            ],
        ];

        return $sampleData[$component] ?? [];
    }
}
