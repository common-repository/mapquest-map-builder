=== Plugin Name ===
Contributors: mapquest 
Tags: maps, map, geo, mapping, custom maps, map plugin, mapquest, map builder, map creator, map maker, insert map, map images, directions, map generator 
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: trunk

MapQuest Map Builder BETA plug-in for Wordpress.  

== Description ==

MapQuest Map Builder is a quick, easy tool for adding maps to your WordPress posts or pages.  With Map Builder you can easily add locations to the map via search, enter lat/long coordinates, or drop points directly on the map.  

For each point added to the map, you can customize the map pointer icon, title and description.  You can even use custom images that you host as the map pin.  You can format text inside the descriptions as well as add images and videos to the pin descriptions.  

Map Builder also has tools for drawing lines and shapes on your map.  Create lines, rectangles, ellipses, and custom polygons on the map and set the color, opacity, border color and border width for each polygon on the map. 

MapQuest Map Builder inserts a map as either an embedded interactive map in an iframe or can also insert as a static image file. 

No API key or other sign-up required.  Start making maps instantly!

Read more at http://features.mapquest.com/mapbuilder/


== Installation ==

= Install Through the WordPress Control Panel =

1. Click 'Plugins' in the WordPress Control Panel and choose 'Add New'
2. Search for 'MapQuest Map Builder'
3. Click 'Install' next to the plug-in in the search results
4. Click the 'Install Now' button
5. Once the plug-in is installed, click the 'Activate Plugin' link next to the plug-in in the list

= Download and Install Manually =

1. Download the MapQuest Map Builder Plug-in for WordPress
2. Upload the `mapquest-map-builder` directory to the `/wp-content/plugins/` directory 
3. Activate the plugin through the 'Plugins' menu in WordPress

Once you have the plugin installed, you will see two new buttons in your editor.

* MapQuest - Insert Map
* MapQuest - Insert Link

Insert Map opens the Map Builder tool and allows you to insert the map you create directly into your page.  Insert Link inserts a link to the map you created into the content of the page. 

== Frequently Asked Questions ==

= 403 Forbiddon error from some hosts =

Error message upon saving maps: "You don't have permission to access /../wp-content/plugins/mapquest-map-builder/tinymce/callback.htm on this server."

Some hosting providers may need to whitelist the plugin due to mod_security rules.  If you experience a 403 error using the plug-in, you may need to contact your host to whitelist the plugin.  See a help thread about this here: http://forums.mapquesthelp.com/posts/e573daf64b.

= Support =

For support and discussion about MapQuest Map Builder, please visit our forums at http://forums.mapquesthelp.com/hives/f93af1aaa0/summary

= Terms of use =

Your use of the plug-in is subject to Terms of Use at http://cdn.mapquest.com/mq_legal/termsofuse.html.

== Screenshots ==

1. MapQuest Map Builder
2. Icon Gallery Samples
3. Custom Polygon Example

== Changelog ==

= 1.0.5 =
* Fixes to re-editing of descriptions with photos
* Fixes to options area to repair map creation bug
* Performance optimization

= 1.0.4 =
* Fixes to tinyMCE integration to address user-reported issues in both the HTML and Visual tabs

= 1.0.3 = 
* Ability to add videos to descriptions of map pins via the TinyMCE editor
* Bug fixes

= 1.0.2 =
* Maps are now selectable in the side navigation for easy editing once created in a post
* Added tinyMCE editor for map pin descriptions to allow addition of photos and formatting of text
* Bug fixes

= 1.0.1 =
* Fixed an issue in which the center parameter was being incorrectly encoded to the "cent" symbol

= 1.0 =
* Tools for drawing circles, rectangles, lines, and polygon shapes
* Ability to set shape opacity, color, border color and border width
* Use your own hosted graphics as map pointers 
* New stock map pin designs added to gallery

= 0.9 =
* BETA version
* Create interactive embedded and static image maps
* Set the height and width of maps
* Set the alignment of maps to content
* Map pin gallery for customizing mapped locations
* Add locations by dropping pins on the map, searching for locations, or adding by latitude and longitude


== Upgrade Notice ==

= 1.0 =
New drawing tools and custom icon support added

