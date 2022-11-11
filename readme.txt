=== Parish Announcements ===
Contributors:       The WordPress Contributors
Tags:               block
Tested up to:       6.1
Requires at least:  5.3
Requires PHP:       7.4
Stable tag:         1.0.0-beta
License:            GPL-2.0-or-later
License URI:        https://www.gnu.org/licenses/gpl-2.0.html
Contributors:       mjvbarton

Block for displaying parish announcements in pdf file.

== Description ==

A handy tool for websites of parishes or for all of those who need to regularly update some pdf document used at real world dashboards.
Only thing you need is to prepare a PDF file that you upload to your Wordpress site. In the plugin you select the active announcement file from the media gallery.
The file is then converted to the jpeg and displayed for the visitors.

You can choose where to display the contents by our custom block "Parish Announcements" in the Block Editor. The plugin uses the native `php_imagick` extension for the conversion.

**Note:** This is the BETA version of the plugin. The future version will have support for WordPress Plugin Directory and for shortcodes.

== Installation ==

1. Install the plugin through the WordPress plugins screen directly by uploading it to the site.
2. Activate the plugin through the 'Plugins' screen in WordPress

= For plugin developers =
1. Download the source code from the github repository to your development environment
2. Unzip the contents to `{your-wordpress}/wp-content/plugins/parish-announcements`
3. Open terminal in the plugin folder and run `npm install` and `composer install`


== Frequently Asked Questions ==

= Will this plugin be available in the WordPress Plugin Directory? =

Soon. The biggest benefit from this will be the option to automatically update the plugin.

= I am not using the WordPress Block Editor, can I use the plugin via shortcode? =

Unfortunately not. In the `1.0.0-beta` version there will be no support for WordPress shortcodes.

= My plugin cannot activate, what should I do? =
First of all, check if the `php_imagick` extension is enabled. If the problem persists, check if the php process has sufficient privileges
to create files and directories in your `WP_CONTENT_DIR` location.

If the problem still persists, please see the open issues [here](https://github.com/mjvbarton/parish-announcements/issues).

== Changelog ==

= 1.0.0-beta =
* Pre-Release