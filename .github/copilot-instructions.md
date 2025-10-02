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

---

If any section is unclear or missing, please provide feedback so this guide can be improved.
