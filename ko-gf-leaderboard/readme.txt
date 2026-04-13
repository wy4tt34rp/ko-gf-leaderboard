=== KO GF Leaderboard ===
Contributors: KO
Tags: gravity forms, leaderboard, voting, contest, bar graph, poll results
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A flexible, real-time leaderboard for Gravity Forms with a clean bar-graph display. 
Supports merge tags (for confirmations/emails) and shortcode output on any page or module.

== Description ==

KO GF Leaderboard adds a visual bar-chart leaderboard to Gravity Forms. It counts how many times each selectable option has been chosen in a specific field and displays the results in a clean, responsive bar graph.

Perfect for:

- Holiday contests  
- Department voting  
- Employee polls  
- Simple head-to-head matchups  
- Real-time results pages  
- Redirect-after-submission pages  

You can display the leaderboard using:

- **A Gravity Forms merge tag** inside confirmations & emails  
- **A shortcode** anywhere on the site (pages, posts, Divi, Elementor, widgets, etc.)

Supports:

✔ Custom bar colors  
✔ Hiding counts  
✔ Hiding total submissions  
✔ Overriding form & field IDs per-use  
✔ Full mobile responsiveness  
✔ Custom CSS included inside the plugin  
✔ Safe output for AJAX / page refresh confirmations  

== Installation ==

1. Upload the plugin folder `ko-gf-leaderboard` to `/wp-content/plugins/`
2. Activate the plugin in **Plugins → Installed Plugins**
3. Edit the top of the main plugin file if you want to set default:
   - Form ID  
   - Field ID  
   - Default bar color  
4. Add the merge tag to a confirmation or add the shortcode to any page.

== Merge Tag Usage ==

You can insert the leaderboard directly into a **confirmation message**, **notification email**, or **admin email** using:

`{ko_2025_leaderboard}`

The merge tag uses the plugin’s configured defaults:

- Uses the default Form ID constant  
- Uses the default Field ID constant  
- Always shows submission counts  
- Always shows total submissions  
- Uses the default bar color  

Example confirmation:

Thanks for voting!

Here are the current results:

`{ko_2025_leaderboard}`

== Shortcode Usage ==

The shortcode displays the leaderboard anywhere on the site:

`[ko_2025_leaderboard]`

The shortcode accepts attributes for full customization.

=== Shortcode Attributes ===

All attributes are optional.

**form_id**  
Overrides the form to pull data from.  
Example: `form_id="23"`

**field_id**  
Overrides which field’s choices are tallied.  
Example: `field_id="5"`

**show_counts**  
Show or hide submission counts next to the percentage.  
Accepts: yes, no, true, false, 1, 0  
Example: `show_counts="0"`

**show_total**  
Show or hide the "Total submissions" line.  
Example: `show_total="no"`

**bar_color**  
Override the bar graph color (HEX).  
Accepts #rrggbb, rrggbb, #rgb, rgb.  
Example: `bar_color="#ff6600"`

=== Example Shortcodes ===

Default:
`[ko_2025_leaderboard]`

Override form & field:
`[ko_2025_leaderboard form_id="23" field_id="7"]`

Hide counts:
`[ko_2025_leaderboard show_counts="no"]`

Hide total:
`[ko_2025_leaderboard show_total="0"]`

Custom bar color:
`[ko_2025_leaderboard bar_color="#00b140"]`

Full custom:
`[ko_2025_leaderboard form_id="23" field_id="5" show_counts="0" show_total="0" bar_color="#ff9900"]`

== Frequently Asked Questions ==

= Does the leaderboard update automatically? =  
Yes. Each page load retrieves the latest Gravity Forms entries.  

= Does this work with radio buttons, dropdowns, or checkboxes? =  
Yes, anything with selectable options.  
Checkbox fields count each checked box as separate entries.

= Can I use this in Divi? =  
Absolutely—use the shortcode inside a **Text Module** or **Code Module**.

= Does this work for confirmations that redirect to a page? =  
Yes. Place the shortcode on the target page.

= Can I style the leaderboard? =  
Yes. All markup uses isolated CSS classes that can be overridden in your theme or Divi.

== Screenshots ==

1. Example leaderboard bar graph  
2. Shortcode inside a Divi Module  
3. Merge tag inside Gravity Forms confirmation  

== Changelog ==

= 1.3.0 =
* Added `bar_color` shortcode attribute.
* Renamed plugin to KO GF Leaderboard.
* Updated CSS block with Kiro font and transparent background.

= 1.2.0 =
* Added `show_counts` and `show_total` shortcode attributes.
* Improved shortcode processing inside confirmations.

= 1.1.0 =
* Added shortcode version.
* Enabled shortcodes inside confirmation messages.

= 1.0.0 =
* Initial release with merge tag and core leaderboard renderer.

== Upgrade Notice ==

Version 1.3.0 adds new customization options.  
Existing merge tag and shortcode usages continue to work.
