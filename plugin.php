<?php
/**
 * Plugin Name: MapQuest Map Builder for WordPress
 * Plugin URI: http://tools.mapquest.com/map-builder-for-wordpress/
 * Description: This plugin allows a user to create custom MapQuest maps and links and insert them into a post.  Requires WordPress 2.8+ and PHP5.
 * Version: 1.0.5
 * Author: MapQuest
 * Author URI: http://tools.mapquest.com/
 */

/*  Copyright 2012  MapQuest

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Class that contains main plugin functionality. Initializes plugin settings and handles displaying a map or link via
 * an associated shortcode.
 */
class MapQuestMapsPlugin {
	
    /**
     * Default constructor executed one time to initialize the plugin.
     */
	function MapQuestMapsPlugin() {
		// Validate the WordPress version.
        global $wp_version;
        
        if (!defined ('IS_WP28')) {
            define('IS_WP28', version_compare($wp_version, '2.8', '>='));
        }
        
        if (!IS_WP28) {
            add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('The MapQuest plugin works only under WordPress 2.5 or higher') . '</strong></p></div>\';'));
            return;
        }
		
		// Define globals.
        define('MapQuestMaps_PATH', WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)));
        define('MapQuestMaps_URL', plugins_url(plugin_basename(dirname(__FILE__))));
        
        define('MapQuestMaps_SITE_BASE', 'http://www.mapquest.com');
        //define('MapQuestMaps_SITE_BASE', 'http://tools-qa.mapquest.com');
        define('MapQuestMaps_BUILDER_BASE', MapQuestMaps_SITE_BASE . '/mapbuilder');
        define('MapQuestMaps_EMBED_BASE', MapQuestMaps_SITE_BASE . '/embed');
        define('MapQuestMaps_LINK_BASE', MapQuestMaps_SITE_BASE);
        
        define('MapQuestMaps_MAP_WIDTH', 450);
        define('MapQuestMaps_MAP_HEIGHT', 400);

        define('MapQuestMaps_TOKEN_PREFIX', '_mq_token');
        
		// Add the short codes.
		add_shortcode('mqLink', array(&$this, 'onLinkShortcode'));
		add_shortcode('mqMap', array(&$this, 'onMapShortcode'));
		
		// Set default options.
        add_option('mq_maps_map_width', MapQuestMaps_MAP_WIDTH);
		add_option('mq_maps_map_height', MapQuestMaps_MAP_HEIGHT);
        add_option('mq_maps_use_sidebar', 0);
        add_option('mq_maps_use_editor_toolbar', 1);
        add_option('mq_maps_use_editor_toolbar_line', 1);
        add_option('mq_maps_use_quicktags_toolbar', 1);
        /*
        add_option('mq_maps_use_media_toolbar', 1);
        */
	}
	
	/**
	 * Callback executed when a link shortcode is processed.  Returns the associated markup to display the link.
	 * 
	 * @return the link markup
	 */
    function onLinkShortcode($atts, $content) {
        extract(shortcode_atts(array(
            'location' => null,
            'q' => null,
            'href' => null,
            'title' => null
        ), $atts));
        
        if (!is_null($location)) {
            // Backward compatibility of old "location" attribute.
            $q = $location;
        }
        
        if (!is_null($q)) {
            // Backward compatibility of old non Map Builder shortcodes.
        	$q = wp_specialchars_decode($q);
            $href = MapQuestMaps_LINK_BASE . '?q=' . urlencode($q); 
        } else if (!is_null($href)) {
            $href = wp_specialchars_decode($href);
        } else {
            return '';
        }
        
        return '<a class="mqLink" href="' . $href . '">' . $content . '</a>';
    }
    
    /**
     * Callback executed when a map shortcode is processed.  Returns the associated markup to display the map. 
     * 
     * @return the map markup
     */
    function onMapShortcode($atts, $content) {
        extract(shortcode_atts(array(
            'location' => null,
            'q' => null,
            'src' => null,
            'width' => get_option('mq_maps_map_width', MapQuestMaps_MAP_WIDTH),
            'height' => get_option('mq_maps_map_height', MapQuestMaps_MAP_HEIGHT),
            'zoom' => MapQuestMaps_ZOOM,
            'maptype' => MapQuestMaps_MAP_TYPE,
            'traffic' => null,
            'align' => null,
            'format' => null
        ), $atts));
        
        $class = "mqMap";
        if (!is_null($align) && ($align != 'none')) { $class = $class . " align" . $align; }
        
        if (!is_null($location)) {
            // Backward compatibility of old "location" attribute.
            $q = $location;
        }
        
        if (!is_null($q)) { 
            // Backward compatibility of old non Map Builder shortcodes.
            $q = wp_specialchars_decode($q);
        	
            $height = $height + 20;
        	           
            $src = MapQuestMaps_EMBED_BASE . '?q=' . urlencode($q) . '&zoom=' . $zoom . '&maptype=' . $maptype;
        	if (!is_null($traffic)) { $src = $src . '&layer=traffic'; }
        	
            return '<iframe class="' . $class . '" width="' . $width . '" height="' . $height . '" src="' . $src . '" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>';
        } else if (!is_null($src)) {
            $src = wp_specialchars_decode($src);
            
            if (is_null($format) || (format == 'interactive')) {
                return '<iframe class="' . $class . '" width="' . $width . '" height="' . $height . '" src="' . $src . '" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>';
            } else if ($format == 'static') {
                return '<img class="' . $class . '" width="' . $width . '" height="' . $height . '" src="' . $src . '"/>';
            } else {
                return '';
            }
        } else {
        	return '';
        }
    }
    
}

// Get things kicked off.
$mqMapsPlugin = new MapQuestMapsPlugin();

// Include the admin functionality.
include_once(dirname(__FILE__) . '/admin.php' );

?>
