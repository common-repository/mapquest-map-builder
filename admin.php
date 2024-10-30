<?php
/**
 * Class that contains plugin admin functionality.  Handles adding admin settings page and menu items/buttons
 * that launch the map and link dialogs.
 */
class MapQuestMapsAdmin {

    private $mcePluginName = "MapQuestMaps";
    private $mceLinkButtonName = "mqLink";
    private $mceMapButtonName = "mqMap";
    
    /**
     * Default constructor executed one time to initialize the plugin admin functionality.
     */
    function MapQuestMapsAdmin() {
        // Add the plugin hooks.
        add_action('admin_init', array(&$this, 'onAdminInit'));
        add_action('admin_menu', array(&$this, 'onAdminMenu'));
        add_action('admin_print_scripts', array(&$this, 'onAdminPrintScripts'));
        add_action('admin_print_styles', array(&$this, 'onAdminPrintStyles'));
        add_action('admin_footer', array(&$this, 'onAdminFooter'));
        add_action('wp_ajax_mq_get_token', array(&$this, 'getToken'));
        add_action('wp_ajax_mq_save_token', array(&$this, 'saveToken'));
        
        // Queue up the dependencies.
        wp_enqueue_script('jquery-ui-dialog');

        wp_enqueue_style('mq-jquery-ui', plugins_url('/tinymce/css/jquery-ui.css', __FILE__));
        wp_enqueue_style('mq-jquery-ui');        
    }
    
    /**
     * Callback executed when a user accesses the admin area.
     */
    function onAdminInit() {
        // Register the settings.
        register_setting('mq-maps-settings-group', 'mq_maps_map_height');
        register_setting('mq-maps-settings-group', 'mq_maps_map_width');
        register_setting('mq-maps-settings-group', 'mq_maps_use_sidebar');
        register_setting('mq-maps-settings-group', 'mq_maps_use_editor_toolbar');
        register_setting('mq-maps-settings-group', 'mq_maps_use_editor_toolbar_line');
        register_setting('mq-maps-settings-group', 'mq_maps_use_quicktags_toolbar');
        /*
        register_setting('mq-maps-settings-group', 'mq_maps_use_media_toolbar');
        */
    }
    
    /**
     * Callback executed after the basic admin panel menu structure is in place.  Adds the admin settings page and menu items/buttons
     * that launch the map and link dialogs.
     */
    function onAdminMenu() {
        // Add the settings menu.
        add_options_page(__('MapQuest Map Builder Options'), __('MapQuest'), 'manage_options', 'MapQuestMaps_options', array(&$this, 'printOptionsPageContent'));
        
        // Skip adding the buttons if the user lacks permissions or rich editing is not enabled.
        if ((!current_user_can('edit_posts') && !current_user_can('edit_pages')) || (get_user_option('rich_editing') != 'true')) {
            return;
        }
        
        // Add meta boxes with buttons to the sidebar.
        if (get_option('mq_maps_use_sidebar')) {
            add_meta_box('mqMaps', __('MapQuest Map Builder'), array(&$this, 'printMetaBoxContent'), 'post', 'side', 'high');
            add_meta_box('mqMaps', __('MapQuest Map Builder'), array(&$this, 'printMetaBoxContent'), 'page', 'side', 'high');
        }
        
        // Add buttons to the editor toolbar.
        if (get_option('mq_maps_use_editor_toolbar')) {
            add_filter('mce_external_plugins', array(&$this, 'addMcePlugin'));
            
            $line = get_option('mq_maps_use_editor_toolbar_line', 1);
            
            if ($line == 1) {
                add_filter('mce_buttons', array(&$this, 'addMceButtons'));              
            } else {
                add_filter('mce_buttons_' . $line, array(&$this, 'addMceButtons'));
            }
        }
        
        /*
         * TODO: There seems to be some pre/post display logic executing for media buttons that is getting in the way
         *       of the link/map dialog process, so remove for now.
         * 
        // Add buttons to the media toolbar.
        if (get_option('mq_maps_use_media_toolbar')) {
            add_action('media_buttons', array(&$this, 'printMediaButtonContent'));
        }
        */
    }
    
    function onAdminPrintScripts() {
        if ($GLOBALS['editing']) {
            // Make the settings available to the plug-in scripts.
            global $post;
?>
<script type="text/javascript">
var mqMapsSettings = {
    pluginUrl : '<?php echo MapQuestMaps_URL ?>',
    builderBase : '<?php echo MapQuestMaps_BUILDER_BASE ?>',
    mapWidth : <?php echo get_option('mq_maps_map_width', MapQuestMaps_MAP_WIDTH); ?>,
    mapHeight : <?php echo get_option('mq_maps_map_height', MapQuestMaps_MAP_HEIGHT); ?>,
    useQuicktags : <?php echo get_option('mq_maps_use_quicktags_toolbar') ? 'true' : 'false' ?>,
    postId : <?php echo $post->ID ?>
};    
</script>
<?php
            // Queue up the plug-in scripts.
            wp_register_script('mqmce', plugins_url('/tinymce/js/mce.js', __FILE__));
            wp_enqueue_script('mqmce');
            
            wp_register_script('mqbd', plugins_url('/tinymce/js/builder.js', __FILE__));
            wp_enqueue_script('mqbd');
        }
    }
    
    function onAdminPrintStyles() {
        if ($GLOBALS['editing']) {
            // Queue up the plug-in styles.
            wp_register_style('mqmce', plugins_url('/tinymce/css/mce.css', __FILE__));
            wp_enqueue_style('mqmce');

            wp_register_style('mqbd', plugins_url('/tinymce/css/builder.css', __FILE__));
            wp_enqueue_style('mqbd');
        }
    }
        
    function onAdminFooter() {
        if ($GLOBALS['editing']) {
?>
<div id="mqMapsDialog" style="display:none;">
    <iframe id="mqBuilder" frameborder="0" marginwidth="0" marginheight="0"></iframe>
</div>
<?php
        }
    }
    
    /**
     * Callback executed when the settings page is displayed.  Outputs the page content.
     */
    function printOptionsPageContent() {
?>
        <div class="wrap">
            <h2><?php _e('MapQuest Map Builder Options'); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields('mq-maps-settings-group'); ?>        
                <table class="form-table">
                    <tr valign="top">
                    <th scope="row"><?php _e('Default Map Width') ?></th>
                    <td><input type="text" name="mq_maps_map_width" value="<?php echo get_option('mq_maps_map_width', MapQuestMaps_MAP_WIDTH); ?>"/></td>
                    </tr>
                    
                    <tr valign="top">
                    <th scope="row"><?php _e('Default Map Height') ?></th>
                    <td><input type="text" name="mq_maps_map_height" value="<?php echo get_option('mq_maps_map_height', MapQuestMaps_MAP_HEIGHT); ?>"/></td>
                    </tr>
                     
                    <tr valign="top">
                    <th scope="row"><?php _e('Link/Map Buttons') ?></th>
                    <td>
                        <fieldset>
                            <label for="mq_maps_use_sidebar">
                                <input type="checkbox" name="mq_maps_use_sidebar" value="1" <?php checked('1', get_option('mq_maps_use_sidebar')); ?>/> <?php _e('Show in sidebar') ?>
                            </label>
                            <br/>
                            <label for="mq_maps_use_editor_toolbar">
                                <input type="checkbox" name="mq_maps_use_editor_toolbar" value="1" <?php checked('1', get_option('mq_maps_use_editor_toolbar')); ?>/> <?php _e('Show in editor toolbar') ?> on line <input style="width:3em;" type="text" name="mq_maps_use_editor_toolbar_line" value="<?php echo get_option('mq_maps_use_editor_toolbar_line', 1); ?>"/> 
                            </label>
                            <br/>
                            <label for="mq_maps_use_quicktags_toolbar">
                                <input type="checkbox" name="mq_maps_use_quicktags_toolbar" value="1" <?php checked('1', get_option('mq_maps_use_quicktags_toolbar')); ?>/> <?php _e('Show in quicktags (HTML editor view) toolbar') ?>
                            </label>
                            <!-- 
                            <br/>
                            <label for="mq_maps_use_media_toolbar">
                                <input type="checkbox" name="mq_maps_use_media_toolbar" value="1" <?php checked('1', get_option('mq_maps_use_media_toolbar')); ?>/> <?php _e('Show in media toolbar') ?>
                            </label>
                            -->
                        </fieldset>
                    </td>
                    </tr>
                </table>
                
                <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                </p>
            </form>
        </div>
<?php
    }
    
    /**
     * Called when the sidebar meta box is displayed.  Outputs the meta box content.
     */
    function printMetaBoxContent() {
?>      
        <div class="submit" style="margin: 0; padding: 0.5em 0;">
            <div style="text-align:center;">
                <input type="button" onclick="mq.mce.dialog.openMap(); return false;" value="<?php _e('Create New Map'); ?>"/>
            </div>
            
            <div style="padding: 15px 0 5px 3px;">Edit Existing Maps</div>            
            <div id="existingMaps" class="tabs-panel" style="background-color: #fff; border: 1px solid #dfdfdf; padding: 5px 0;">
            	<ol><li>No maps found for this post.</li></ol>
            </div>
        </div>
<?php
    }
    
    /*
    function printMediaButtonContent() {
?>
<a title="<?php _e('MapQuest - Add Map'); ?>" class="thickbox" id="mq_add_map" onclick="tinyMCE.activeEditor.execCommand('mqMap'); return false;"><img alt="<?php _e('MapQuest - Add Map'); ?>" src="<?php echo plugins_url('/img/media-map.gif', __FILE__) ?>"></a>
<a title="<?php _e('MapQuest - Add Link'); ?>" class="thickbox" id="mq_add_link" onclick="tinyMCE.activeEditor.execCommand('mqLink'); return false;"><img alt="<?php _e('MapQuest - Add Link'); ?>" src="<?php echo plugins_url('/img/media-link.gif', __FILE__) ?>"></a>
<?php       
    }
    */
    
    /**
     * Callback executed when the TinyMCE plugins are initialized.  Adds the editor plugin to display the map and link dialogs.
     * 
     * @return the plugins
     */
    function addMcePlugin($plugins) {
        $plugins[$this->mcePluginName] =  plugins_url('/tinymce/editor_plugin.js', __FILE__);
        return $plugins;
    }
    
    /**
     * Callback executed when the TinyMCE buttons are displayed.  Adds the editor buttons to display the map and link dialogs.  
     *
     * @return the buttons
     */
    function addMceButtons($buttons) {
        if (count($buttons) > 0) {
            array_push($buttons, "separator");
        }
        array_push($buttons, $this->mceMapButtonName);
        return $buttons;
    }
    
    /**
     * Gets a token associated with the given post and key.  Called from the client dialog via AJAX.
     */
    function getToken() {
        $id = $_POST['id'];
        $key = MapQuestMaps_TOKEN_PREFIX . '_' . $_POST['key'];
        
        $field = get_post_custom($id);
        $token = $field[$key][0];
        
        echo $token;
        
        die();
    }
    
    /**
     * Saves a token associated with the given post and key.  Called from the client dialog via AJAX.
     */
    function saveToken() {
        $id = $_POST['id'];
        $key = MapQuestMaps_TOKEN_PREFIX . '_' . $_POST['key'];
        $token = $_POST['token'];
        
        add_post_meta($id, $key, $token, true);
        
        die();
    }
}

// Get things kicked off.
$mqMapsAdmin = new MapQuestMapsAdmin();
?>
