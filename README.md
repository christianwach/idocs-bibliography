i-Docs Bibliography
===================

**Contributors:** [needle](https://profiles.wordpress.org/needle/)<br/>
**Donate link:** https://www.paypal.me/interactivist<br/>
**Tags:** ACF, bibliography, citation<br/>
**Requires at least:** 4.9<br/>
**Tested up to:** 5.5<br/>
**Stable tag:** 0.2<br/>
**License:** GPLv2 or later<br/>
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

i-Docs Bibliography provides a Custom Post Type and ACF Fields to build a Bibliography or Citation Archive.

---

### Description

The *i-Docs Bibliography* plugin provides a Custom Post Type with ACF Fields that allows you to build a Bibliography or Citation Archive.

#### Shortcodes

Two Shortcodes are provided by this plugin: an "Individual Citations" Shortcode and a "Lists of Citations" Shortcode. Both Shortcodes work best with the Shortcake plugin.

Individual Citations can be inserted into Posts or Pages with the `[idocs_citation]` Shortcode. The Citation can be manually configured by using `id` attribute, e.g.

* `[idocs_citation id="123" /]`
* `[idocs_citation id="234" /]`

Lists of Citations can be inserted into Posts or Pages with the `[idocs_citations]` Shortcode. The Lists of Citations Shortcode can be manually configured by using the `category`, `tag` and `relation` attributes to filter by category ID and/or tag ID, e.g.

* `[idocs_citations category="2,3" tag="8,9,11" relation="OR" /]`
* `[idocs_citations category="3" tag="4" relation="AND" /]`

---

### Requirements

This plugin recommends a minimum of *WordPress 4.9* and requires [Advanced Custom Fields Pro](https://www.advancedcustomfields.com/).

---

### Installation

There are two ways to install from GitHub:

##### ZIP Download

If you have downloaded *i-Docs Bibliography* as a ZIP file from the GitHub repository, do the following to install and activate the plugin:

1. Unzip the .zip file and rename the enclosing folder so that the plugin's files are located directly inside `/wp-content/plugins/idocs-bibliography`
2. Activate the plugin
3. You are done!

##### git clone

If you have cloned the code from GitHub, it is assumed that you know what you're doing.
