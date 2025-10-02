# Copilot Instructions for ToolPress


## Project Overview
- **ToolPress** is a WordPress plugin for enabling/disabling various site tools (Google Tag Manager, HubSpot, jQuery, Bootstrap, Font Awesome, Tawk.to chat, etc.) via the admin backend.
- The main entry point is `toolpress.php`.
- The project now includes a more modern structure with build tools, source folders, and configuration files.

## Architecture & Key Files
- Main logic is in `toolpress.php` (root plugin file).
- Additional PHP logic in `includes/` (e.g., `class-run-tags.php`).
- JS/React source code in `src/` (admin dashboard, components, icons).
- Compiled assets in `build/` (admin dashboard JS, asset manifests).
- Language files in `languages/` (translation `.pot` file).
- Composer dependencies in `vendor/`.

## Configuration & Build
- **Configuration files:**
	- `package.json`: NPM scripts, JS dependencies, build config.
	- `Gruntfile.js`: Grunt tasks for cleaning, copying, zipping.
	- `webpack-config.js`: Webpack bundling for JS assets.
	- `.eslintrc`: ESLint rules for JS/React code.
	- `.vscode/settings.json`: VS Code workspace settings.
	- `composer.json`: PHP dependencies via Composer.
- **Build workflow:**
	- Use `npm run` scripts for JS build and asset management.
	- Grunt for packaging and zipping plugin files.
	- Composer for PHP dependency management.

## Patterns & Conventions
- Main plugin logic in `toolpress.php`.
- Additional PHP classes in `includes/`.
- JS/React code in `src/`, output to `build/`.
- Translation support via `.pot` file in `languages/`.
- Follows WordPress hook patterns and standard plugin header.

## Integration Points
- Integrates with WordPress via hooks in `toolpress.php` and supporting PHP classes.
- Loads third-party scripts/styles for enabled tools.
- No custom database tables or REST endpoints detected.

## Example: Plugin Structure
```php
// toolpress.php
<?php
/*
Plugin Name: ToolPress
Description: Adds various tools to your site.
*/
// ... WordPress hooks and logic ...
```

## How to Extend
- Add new tools by editing `toolpress.php` and/or adding PHP classes in `includes/`.
- Register new scripts/styles via WordPress hooks.
- Add JS/React features in `src/` and build with npm/webpack.
- Use Grunt for packaging and Composer for PHP dependencies.

## Translation Generation Guide

### File Types and Structure
```
languages/
├── toolpress.pot              # Template file
├── toolpress-{locale}.po     # Translation source
├── toolpress-{locale}.mo     # Compiled translations
└── toolpress-{locale}-toolpress-admin-dashboard.json  # JS translations
```

### PO File Generation
When asked to generate a PO file:
1. Create file in `languages/toolpress-{locale}.po`
2. Use standard gettext header with WordPress metadata
3. Include all strings from POT template
4. Use UTF-8 encoding
5. Set proper plural forms for target language

Example template:
```php
# Copyright (c) [YEAR] ToolPress. All Rights Reserved.
msgid ""
msgstr ""
"Project-Id-Version: ToolPress 1.0.1\n"
"Language: [LOCALE]\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\n"
```

### MO File Generation
When asked to generate MO file:
1. Use msgfmt command
2. Place in languages directory
3. Maintain same base name as PO file

Command pattern:
```bash
cd languages && msgfmt toolpress-{locale}.po -o toolpress-{locale}.mo
```

### JSON File Generation
When asked to generate JSON file:
1. Create file in `languages` directory
2. Name format: `toolpress-{locale}-toolpress-admin-dashboard.json`
3. Include only strings used in JavaScript
4. Maintain WordPress JSON translation format

Example structure:
```json
{
    "translation-revision-date": "[DATE]",
    "generator": "WP-CLI 2.12.0",
    "domain": "toolpress",
    "locale_data": {
        "toolpress": {
            "": {
                "domain": "toolpress",
                "lang": "[LOCALE]",
                "plural-forms": "nplurals=2; plural=(n != 1);"
            },
            [TRANSLATIONS]
        }
    }
}
```

### Translation Guidelines
1. **Brand Names**: Keep untranslated (Google, HubSpot, jQuery, etc.)
2. **UI Elements**: Translate all (buttons, labels, messages)
3. **Placeholders**: Maintain format specifiers
4. **Error Messages**: Translate maintaining technical terms
5. **JavaScript Strings**: Match PHP translations exactly

### Testing Process
1. Generate all three file types (PO, MO, JSON)
2. Set WordPress to target language
3. Check admin interface translations
4. Verify JavaScript string translations
5. Test error messages and notifications

### Supported Languages
- English (en_US) - Default
- Hindi (hi_IN)
- Nepali (ne_NP)

---

If any section is unclear or missing, please provide feedback so this guide can be improved.
