# Changelog

All notable changes to the Tollbridge WordPress Plugin are documented in this file.

## [1.7.1] - 2026-02-04
- Add comprehensive CLAUDE.md documentation file for AI assistance
- Add CHANGELOG.md for better version tracking
- Update README.txt with complete changelog history
- Improve project documentation and developer onboarding

## [1.7.0] - 2023-09-20
- Migrate to Tollbridge CDN reference for JS payload (#12)

## [1.6.0] - 2023-05-16
- Handle logged-in and free configuration options
- Improve error handling for config API responses
- Catch WP_Error from remote API calls

## [1.5.0] - 2023-05-16
- Introduce subscription disabling capability

## [1.4.0] - 2022-09-19
- Fix admin menu styling issues
- Improve CSS rendering

## [1.3.1] - 2022-09-01
- Fix admin menu style bug

## [1.3.0] - 2022-08-05
- Add ability to change config-base URL for custom Tollbridge environments

## [1.2.0] - 2022-06-20
- Fix empty request data not being saved properly
- Fix inline paywall rendering on AMP views
- Fix paywall application on WordPress pages (not just posts)
- Add support for toggling trending articles tracking
- Add adaptive/dynamic paywall support
- Implement PHP CS Fixer with WordPress coding standards
- Fix missing AMP methods hotfix

## [1.1.0] - 2021-09-06
- **Adaptive Paywall** (TOL-64): Dynamic paywall configuration based on user behavior
- **Article Tracking** (TOL-57): Log and track article views for analytics

## [1.0.0] - 2021-08-16
### Internationalization
- Full i18n support with translations
- Spanish translation complete
- French translation complete
- Receive requirement text dynamically from app.tollbridge.co

### AMP Access Integration
- Full AMP (Accelerated Mobile Pages) support
- Custom AMP widgets and views
- AMP-specific paywall rendering (inline and overlay)
- Cache API requests for performance (900 second TTL)
- Remove auth tokens from AMP requests
- Generate subscription requirements text locally
- Customize widget branding

### Core Features
- OAuth callback handling via custom rewrite rules (`/tollbridge-callback`)
- Global paywall settings with per-article override capability
- User role bypass functionality (admins, editors, etc. can bypass paywall)
- Plan-based access control (use plan IDs and names)
- Support for multiple post types (posts, pages, custom post types)
- Time-based access control (paid-to-free and free-to-paid)
- Meta box for per-article paywall configuration
- Integration with Tollbridge API for plan management
- Paywall eligibility behaviors (open to all, logged-in users, or plan subscribers)

## Initial Development - February-March 2021
- First release (v1) with core paywall functionality
- Basic Tollbridge API integration
- Account settings management
- Plan-based subscription control
