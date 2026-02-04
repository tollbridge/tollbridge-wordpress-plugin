=== Plugin Name ===
Contributors: conroyp
Tags: publisher
Requires at least: 5.2.0
Tested up to: 5.9.0
Stable tag: 1.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds Tollbridge.co paywall integration to your WordPress site.

== Description ==

This plugin integrates Tollbridge.co paywall rules to an existing WordPress site.

== Installation ==

Activate the plugin. This will create a menu in the sidebar - "Tollbridge". Click into this menu, and enter the Tollbridge credentials from the admin panel in your Tollbridge account.

Paywall rules can be applied globally to all posts and pages, or applied individually per-post.

== Frequently Asked Questions ==

= Can I have some articles behind the paywall and some free to access? =

Absolutely! There are two ways to achieve this, depending on how many articles you wish to make free:
* set the global paywall rules to restrict access, then on the articles you want free, override the paywall settings on those articles.
* set no global paywall rules (making the full site free to access), then set the paywall restrictions on articles you wish to limit.

= What about time-based access, e.g. "paywalled for a week, then free to access"? =

This can be managed in the global settings, selecting the "Change paywall access over time" section, and selecting the number of days after which access should change. This setting can also be set on a per-article level.

= Does this plugin support AMP? =

AMP pages are not currently supported by the Tollbridge platform.


== Changelog ==

= 1.7.1 =
* Add comprehensive CLAUDE.md documentation file for AI assistance
* Add CHANGELOG.md for better version tracking
* Update README.txt with complete changelog history
* Improve project documentation and developer onboarding

= 1.7.0 =
* Migrate to Tollbridge CDN reference for JS payload

= 1.6.0 =
* Handle logged-in and free configuration options
* Improve error handling for config API responses
* Catch WP_Error from remote API calls

= 1.5.0 =
* Introduce subscription disabling capability

= 1.4.0 =
* Fix admin menu styling issues
* Improve CSS rendering

= 1.3.1 =
* Fix admin menu style bug

= 1.3.0 =
* Add ability to change config-base URL for custom Tollbridge environments

= 1.2.0 =
* Fix empty request data not being saved properly
* Fix inline paywall rendering on AMP views
* Fix paywall application on WordPress pages (not just posts)
* Add support for toggling trending articles tracking
* Add adaptive/dynamic paywall support
* Implement PHP CS Fixer with WordPress coding standards
* Fix missing AMP methods hotfix

= 1.1.0 =
* Adaptive Paywall: Dynamic paywall configuration based on user behavior
* Article Tracking: Log and track article views for analytics

= 1.0.0 =
* Full internationalization support with Spanish and French translations
* Complete AMP (Accelerated Mobile Pages) integration
* OAuth callback handling via custom rewrite rules
* Global paywall settings with per-article override capability
* User role bypass functionality
* Plan-based access control
* Support for multiple post types
* Time-based access control (paid-to-free and free-to-paid)
* Integration with Tollbridge API for plan management
* Initial release
