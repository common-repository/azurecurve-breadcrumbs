=== azurecurve Breadcrumbs ===
Contributors: azurecurve
Donate link: http://development.azurecurve.co.uk/support-development/
Author URI: http://development.azurecurve.co.uk/
Plugin URI: http://development.azurecurve.co.uk/plugins/breadcrumbs/
Tags: breadcrumbs, pages, page, WordPress,ClassicPress
Requires at least: 3.3
Tested up to: 5.0.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows breadcrumbs to be placed before and after the content on a page; the azc_b_getbreadcrumbs() function can be added to a theme template to position the breadcrumbs elsewhere on the page.

Plugin supports both text based and arrowed breadcrumbs; styles maintainable via the admin console.

== Description ==
Allows breadcrumbs to be placed before and after the content on a page; the azc_b_getbreadcrumbs() function can be added to a theme template to position the breadcrumbs elsewhere on the page.

Plugin supports both text based and arrowed breadcrumbs; styles maintainable via the admin console.

This plugin is multi-site compatible.

This plugin supports language translations. If you want to translate this plugin please sent the .po and .mo files to wordpress.translations@azurecurve.co.uk for inclusion in the next version (full credit will be given). The .pot fie is in the languages folder of the plugin and can also be downloaded from the plugin page on http://development.azurecurve.co.uk.

== Installation ==
To install the plugin:
* Copy the <em>azurcurve-breadcrumbs</em> folder into your plug-in directory.
* Activate the plugin.
* Emable relevant settings via the configuration page in the admin control panel (azurecurve menu).
* If required, add the function azc_b_getbreadcrumbs() to a theme file to place the breadcrumbs (for example, just above or below the page title). Use the following syntax to avoid errors if the plugin is deactivated: if (function_exists(azc_b_getbreadcrumbs)){ echo azc_b_getbreadcrumbs( 'arrow'); }
* Shortcode getbreadcrumbs can be used anywhere to place breadcrumbs: [getbreadcrumbs=arrow]

== Changelog ==
Changes and feature additions for the Breadcrumbs plugin:
= 1.0.0 =
* First version

== Frequently Asked Questions ==
= Is this plugin compatible with both WordPress and ClassicPress? =
* Yes, this plugin will work with both.
= Can I translate this plugin? =
* Yes, the .pot fie is in the plugin's languages folder and can also be downloaded from the plugin page on http://development.azurecurve.co.uk; if you do translate this plugin please sent the .po and .mo files to wordpress.translations@azurecurve.co.uk for inclusion in the next version (full credit will be given).