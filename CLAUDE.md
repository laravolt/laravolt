# Laravolt Package AI Guidelines

## UI Framework & Styling

### Icon Set
- **Font Awesome v5** - All icons use the Font Awesome 5 icon set
- Icon prefix is configured in `config/laravolt.ui.iconset`
- Icons are rendered using the `svg()` helper from blade-ui-kit/blade-icons
- Example: `{!! svg('fas-shield') !!}` or `<x-volt-icon name="shield"/>`

### CSS Framework
- **Preline UI** - Modern UI component library built on Tailwind CSS
- All components in `resources/views/components/` use Preline UI patterns
- Use Tailwind CSS utility classes, NOT Fomantic UI classes
- Dark mode is supported via `dark:` prefixes

### Deprecated Patterns (Do NOT Use)
These legacy Fomantic UI patterns should NOT be used in new code:
- `ui grid`, `ui column`
- `ui card`, `ui cards`
- `ui button`, `ui button basic`
- `ui table`, `ui table padded`
- `ui checkbox`, `ui toggle`
- `ui message`, `ui icon message`
- `ui divider`, `ui divider hidden`

### Preferred Patterns (Use These)
Use these modern Preline UI + Tailwind patterns instead:
| Old (Fomantic) | New (Preline/Tailwind) |
|----------------|------------------------|
| `ui grid` | Tailwind grid: `grid grid-cols-3 gap-6` |
| `ui card` | `<x-volt-card>` component |
| `ui button` | `<x-volt-button>` component |
| `ui table` | `<x-volt-table>` or Tailwind styled table |
| `ui checkbox` | Tailwind checkbox or toggle switch |
| `ui message` | Tailwind alert with flex layout |

## Component Reference

Key Blade components available:
- `<x-volt-app>` - Main app layout with sidebar
- `<x-volt-panel>` - Card panel with header/footer
- `<x-volt-card>` - Clickable card component
- `<x-volt-button>` - Button with variants (primary, secondary, danger, success)
- `<x-volt-link-button>` - Anchor styled as button
- `<x-volt-badge>` - Badge/tag component
- `<x-volt-switch>` - Toggle switch component
- `<x-volt-table>` - Styled table component
- `<x-volt-input>` - Form input component
- `<x-volt-backlink>` - Back navigation link
