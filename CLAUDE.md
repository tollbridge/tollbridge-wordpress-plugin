# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress plugin that integrates Tollbridge.co paywall functionality into WordPress sites. The plugin allows publishers to control article access based on user subscriptions, with support for global or per-article configuration.

## Architecture

### Core Bootstrap Flow

1. **tollbridge.php** - Main plugin file that WordPress reads. Defines plugin metadata, version constants (`TOLLBRIDGE_VERSION`, `TOLLBRIDGE_BASE_PATH`, `TOLLBRIDGE_BASE_URL`), and bootstraps the plugin via `run_tollbridge()`.

2. **autoload.php** - Custom autoloader that maps the `Tollbridge\Paywall` namespace to the `classes/` directory structure. Namespaces directly correspond to folder paths.

3. **Runner** (`classes/Runner.php`) - Core orchestrator that:
   - Instantiates the Loader (hook manager)
   - Initializes key classes: AdminArticle, FrontendArticle, RewriteHandler
   - Registers admin and public hooks via the Loader

4. **Loader** (`classes/Loader.php`) - Hook management system that collects and registers WordPress actions/filters via `add_action()` and `add_filter()` methods.

### Key Components

**Manager** (`classes/Manager.php`)
- Central business logic coordinator
- Retrieves and validates Tollbridge account settings (App ID, Client ID, Client Secret)
- Determines which subscription plans apply to a given post
- Handles time-based access rules (e.g., "free for 90 days, then paywalled")
- Manages user role bypass logic

**Client** (`classes/Client.php`)
- Singleton pattern for API communication with Tollbridge servers
- Handles OAuth token management (cached for 15 minutes via `wp_cache`)
- Fetches configuration and subscription plans from Tollbridge API
- Includes AMP view support

**Frontend\Article** (`classes/Frontend/Article.php`)
- Renders paywall metadata in `<head>` via `wp_head` hook
- Injects Tollbridge JavaScript via `wp_body_open` or `the_content` filter (fallback for themes without `wp_body_open` support)
- Determines paywall eligibility based on post type, user roles, and subscription plans
- Supports AMP page detection via `amp_is_request()`

**Settings\Article** (`classes/Settings/Article.php`)
- Manages per-article paywall settings via WordPress post meta
- Handles override logic for articles that don't use global rules

**Settings\Config** (`classes/Settings/Config.php`)
- Manages global paywall configuration
- Renders admin UI for post type selection, plan restrictions, time-based access, and user role bypasses
- Two access change directions: `ACCESS_CHANGE_PAID_TO_FREE` (embargo model) and `ACCESS_CHANGE_FREE_TO_PAID` (archive model)

**RewriteHandler** (`classes/RewriteHandler.php`)
- Manages custom URL rewrite rules for OAuth callback endpoint (`/tollbridge-callback`)

### Settings Hierarchy

The plugin supports a two-tier settings model:

1. **Global Settings** - Applied to all content by default (configured in `Settings\Config`)
2. **Per-Article Overrides** - Individual articles can override global rules (managed via `Settings\Article`)

When determining access, the Manager checks:
- Is global mode active? (`tollbridge_is_using_global_rules`)
- Does the article have meta overrides? (`tollbridge_override_global_rules`)
- Use appropriate settings source accordingly

### Paywall Eligibility Behaviors

Three modes controlled via `PAYWALL_ELIGIBILITY_BEHAVIOR_*` constants in Manager:
- `OPEN_TO_USERS_WITH_CONFIGURED_PLANS` (2) - Only users with specific subscription plans can access
- `OPEN_TO_ALL` (1) - No paywall restriction
- `OPEN_TO_ONLY_LOGGED_IN_USERS` (0) - Any authenticated user can access

### Time-Based Access

Articles can change access rules after a specified number of days:
- **Paid → Free** (`to_free`): Embargo period before going free
- **Free → Paid** (`to_paid`): Grace period before paywall activates

Logic in `Manager::getApplicablePlans()` compares post publication date with current date to determine if time threshold has passed.

## WordPress Integration Points

- **Admin Menu**: "Tollbridge" menu with Account Settings and Paywall Configuration pages
- **Post Meta Box**: Added to applicable post types for per-article configuration
- **Frontend Hooks**:
  - `wp_head` - Injects paywall metadata
  - `wp_body_open` - Injects Tollbridge JS (preferred)
  - `the_content` - Fallback injection point for older themes
- **Rewrite Rules**: Custom callback URL for OAuth flow

## Development

### Namespace Convention
All classes use `Tollbridge\Paywall` namespace. The autoloader maps this directly to file paths:
- `Tollbridge\Paywall\Manager` → `classes/Manager.php`
- `Tollbridge\Paywall\Frontend\Article` → `classes/Frontend/Article.php`
- `Tollbridge\Paywall\Exceptions\NoPlansExistException` → `classes/Exceptions/NoPlansExistException.php`

### Views Directory
Template files in `views/` are included via `require_once`:
- `views/admin/` - Admin interface templates
- `views/frontend/` - Public-facing templates
- `views/amp/` - AMP-specific templates

### Version Updates
When updating the plugin version:
1. Update `Version:` in tollbridge.php header comment
2. Update `TOLLBRIDGE_VERSION` constant in tollbridge.php
3. Update `$this->version` fallback in Runner.php constructor
4. Update `Stable tag:` in README.txt

### WordPress Options
Key settings stored in wp_options table:
- `tollbridge_app_id`, `tollbridge_client_id`, `tollbridge_client_secret` - API credentials
- `tollbridge_is_using_global_rules` - Boolean for global vs per-article mode
- `tollbridge_applicable_post_types` - Array of post types to apply paywall
- `tollbridge_plans_with_access` - Array of plan IDs with access (global)
- `tollbridge_user_types_with_bypass` - Array of user role slugs that bypass paywall
- `tollbridge_paywall_eligibility_check_behaviour` - Integer (0, 1, or 2)
- `tollbridge_time_access_*` - Time-based access configuration

### Caching
The Client class uses WordPress transient/object cache:
- Access tokens: 900 seconds (15 minutes)
- Plans list: 900 seconds
- AMP views: 900 seconds
Cache group: `tollbridge`

## Installation & Activation

Users install this as a WordPress plugin (upload ZIP or place in wp-content/plugins). After activation:
1. Navigate to Tollbridge → Account Settings
2. Enter App ID, Client ID, Client Secret from Tollbridge.co account
3. Configure global paywall rules in Tollbridge → Paywall Configuration
4. Optionally override rules on individual posts via the Tollbridge meta box

## AMP Support

Plugin includes AMP detection and special rendering via `amp_is_request()` function. AMP-specific templates are in `views/amp/` directory.

## Changelog

### Version 1.7.1 - February 2026
- Add comprehensive CLAUDE.md documentation file for AI assistance
- Add CHANGELOG.md for better version tracking
- Update README.txt with complete changelog history
- Improve project documentation and developer onboarding

### Version 1.7.0 - September 2023
- Migrate to Tollbridge CDN reference for JS payload (#12)

### Version 1.6.0 - May 2023
- Handle logged-in and free configuration options
- Improve error handling for config API responses
- Catch WP_Error from remote API calls

### Version 1.5.0 - May 2023
- Introduce subscription disabling capability

### Version 1.4.0 - September 2022
- Fix admin menu styling issues
- Improve CSS rendering

### Version 1.3.1 - September 2022
- Fix admin menu style bug

### Version 1.3.0 - August 2022
- Add ability to change config-base URL for custom Tollbridge environments

### Version 1.2.0 - June 2022
- Fix empty request data not being saved properly
- Fix inline paywall rendering on AMP views
- Fix paywall application on WordPress pages (not just posts)
- Add support for toggling trending articles tracking
- Add adaptive/dynamic paywall support
- Implement PHP CS Fixer with WordPress coding standards
- Fix missing AMP methods hotfix

### Version 1.1.0 - September 2021
- **Adaptive Paywall** (TOL-64): Dynamic paywall configuration based on user behavior
- **Article Tracking** (TOL-57): Log and track article views for analytics

### Version 1.0.0 - August 2021
- **Internationalization**: Full i18n support with translations
  - Spanish translation complete
  - French translation complete
  - Receive requirement text dynamically from app.tollbridge.co
- **AMP Access Integration**:
  - Full AMP (Accelerated Mobile Pages) support
  - Custom AMP widgets and views
  - AMP-specific paywall rendering (inline and overlay)
  - Cache API requests for performance (900 second TTL)
  - Remove auth tokens from AMP requests
  - Generate subscription requirements text locally
  - Customize widget branding
- **Core Features**:
  - OAuth callback handling via custom rewrite rules (`/tollbridge-callback`)
  - Global paywall settings with per-article override capability
  - User role bypass functionality (admins, editors, etc. can bypass paywall)
  - Plan-based access control (use plan IDs and names)
  - Support for multiple post types (posts, pages, custom post types)
  - Time-based access control (paid-to-free and free-to-paid)
  - Meta box for per-article paywall configuration
  - Integration with Tollbridge API for plan management
  - Paywall eligibility behaviors (open to all, logged-in users, or plan subscribers)

### Initial Development - February-March 2021
- First release (v1) with core paywall functionality
- Basic Tollbridge API integration
- Account settings management
- Plan-based subscription control
