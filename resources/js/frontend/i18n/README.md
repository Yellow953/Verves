# Internationalization (i18n) Setup

This project uses `react-i18next` for internationalization support with English (en) and French (fr) languages.

## Structure

```
i18n/
├── config.js                 # i18n configuration
├── locales/
│   ├── en/
│   │   └── translation.json  # English translations
│   └── fr/
│       └── translation.json  # French translations
└── README.md                 # This file
```

## Usage

### Basic Usage in Components

```jsx
import { useTranslation } from 'react-i18next';

function MyComponent() {
  const { t } = useTranslation();
  
  return (
    <div>
      <h1>{t('common.welcome')}</h1>
      <p>{t('common.subtitle')}</p>
    </div>
  );
}
```

### Using the Custom Hook

```jsx
import { useTranslation } from '../hooks/useTranslation';

function MyComponent() {
  const { t, currentLanguage, changeLanguage, isEnglish } = useTranslation();
  
  return (
    <div>
      <p>Current language: {currentLanguage}</p>
      {isEnglish && <p>You're viewing in English</p>}
    </div>
  );
}
```

### Language Switcher Component

```jsx
import LanguageSwitcher from './components/LanguageSwitcher';

function Header() {
  return (
    <header>
      <LanguageSwitcher />
    </header>
  );
}
```

## Adding New Translations

1. Add the key-value pair to both `en/translation.json` and `fr/translation.json`
2. Use nested objects for organization (e.g., `auth.login`, `forum.title`)
3. Use the translation key in your component: `t('your.namespace.key')`

## Translation Keys Structure

- `common.*` - Common UI elements (buttons, labels, etc.)
- `auth.*` - Authentication related translations
- `navigation.*` - Navigation menu items
- `forum.*` - Forum/community features
- `coach.*` - Coach-related features
- `program.*` - Program management
- `booking.*` - Booking system
- `profile.*` - User profile

## Language Detection

The system automatically detects the user's language preference from:
1. LocalStorage (if previously set)
2. Browser navigator settings
3. HTML lang attribute

The selected language is saved to localStorage for persistence across sessions.

