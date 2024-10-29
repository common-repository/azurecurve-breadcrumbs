<?php
/*
Plugin Name: azurecurve Breadcrumbs
Plugin URI: http://development.azurecurve.co.uk/plugins/breadcrumbs/

Description: Allows breadcrumbs to be placed before and after the content on a page; the azc_b_getbreadcrumbs() function can be added to a theme template to position the breadcrumbs elsewhere on the page.
Version: 1.0.0

Author: azurecurve
Author URI: http://development.azurecurve.co.uk/

Text Domain: azc-b
Domain Path: /languages

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

The full copy of the GNU General Public License is available here: http://www.gnu.org/licenses/gpl.txt
 */

/*
function azc_b_load_css(){
	wp_enqueue_style( 'azc-b', plugins_url( 'style.css', __FILE__ ) );
}
add_action('wp_enqueue_scripts', 'azc_b_load_css');
*/

//include menu
require_once( dirname(  __FILE__ ) . '/includes/menu.php');

function azc_b_set_default_options($networkwide) {
	
	$new_options = array(
				'add-homepage' => 1,
				'show-homepage' => 0,
				'page-before' => 'none',
				'page-after' => 'arrow',
				'breadcrumb-separator' => '&raquo;',
				'style-text' => "div.azc-b-textbreadcrumbs {
	font-size: 12px;
	color: grey;
	font-weight: 550;
	padding: 9px 0 9px 0;
}
a.azc-b-textbreadcrumbs {
	color: grey !important;
	font-weight: 550;
	text-decoration: none;
}
a.azc-b-textbreadcrumbs:hover {
	color: #007FFF !important;
	text-decoration: underline;
}",
				'style-arrow' => "div.azc-b-arrowbreadcrumbscontainer{
	display: block;
	width: 100%;
}
div.azc-b-arrowbreadcrumbs {
	display: inline-block;
	border: 1px solid #007FFF;
	overflow: hidden;
	border-radius: 5px;
}
div.azc-b-arrowbreadcrumbs a, span.azc-b-arrowbreadcrumbs {
	text-decoration: none;
	outline: none;
	display: block;
	float: left;
	font-size: 12px;
	line-height: 20px;
	/*need more margin on the left of links to accomodate the numbers*/
	padding: 0 10px 0 20px;
	position: relative;
}
div.azc-b-arrowbreadcrumbs a{
	color: #007FFF;
	font-weight: 550;
}
span.azc-b-arrowbreadcrumbs {
	color: #007FFF;
}
/*since the first link does not have a triangle before it we can reduce the left padding to make it look consistent with other links*/
div.azc-b-arrowbreadcrumbs a:first-child {
	border-radius: 5px 0 0 5px; /*to match with the parent's radius*/
	padding-left: 10px;
	color: #007FFF;
}
div.azc-b-arrowbreadcrumbs a:last-child {
	border-radius: 0 5px 5px 0; /*this was to prevent glitches on hover*/
	padding-right: 20px;
	color: #007FFF;
}
div.azc-b-arrowbreadcrumbs a:not(:first-child):not(:last-child) {
	color: #007FFF;
}

/*adding the arrows for the azc-b-arrowbreadcrumbss using rotated pseudo elements*/
div.azc-b-arrowbreadcrumbs a:after {
	content: '';
	position: absolute;
	top: 0;
	/*half of square's length*/
	right: -10px;
	/*same dimension as the line-height of div.azc-b-arrowbreadcrumbs a */
	width: 20px; 
	height: 20px;
	transform: scale(0.707) rotate(45deg);
	/*we need to prevent the arrows from getting buried under the next link*/
	z-index: 1;
	/*stylish arrow design using box shadow*/
	box-shadow:
		2px -2px 0 2px #007FFF, 
		3px -3px 0 2px #007FFF;
	border-radius: 0 5px 0 50px;
}

div.azc-b-arrowbreadcrumbs a, div.azc-b-arrowbreadcrumbs a:after {
	background: #FFF;
	transition: all 0.5s;
}
div.azc-b-arrowbreadcrumbs a:hover, div.azc-b-arrowbreadcrumbs a.active, 
div.azc-b-arrowbreadcrumbs a:hover:after, div.azc-b-arrowbreadcrumbs a.active:after,
div.azc-b-arrowbreadcrumbs a:not(:first-child):not(:last-child):hover{
	background: #007FFF;
	color: #FFF;
}",
			);
	
	// set defaults for multi-site
	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			global $wpdb;

			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			$original_blog_id = get_current_blog_id();

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );

				if ( get_option( 'azc-b' ) === false ) {
					add_option( 'azc-b', $new_options );
				}
			}

			switch_to_blog( $original_blog_id );
		}else{
			if ( get_option( 'azc-b' ) === false ) {
				add_option( 'azc-b', $new_options );
			}
		}
		if ( get_site_option( 'azc-b' ) === false ) {
			add_site_option( 'azc-b', $new_options );
		}
	}
	//set defaults for single site
	else{
		if ( get_option( 'azc-b' ) === false ) {
			add_option( 'azc-b', $new_options );
		}
	}
}
register_activation_hook( __FILE__, 'azc_b_set_default_options' );

function azc_b_getbreadcrumbs( $type ){
	echo azc_b_generatebreadcrumbs(get_the_ID(), $type );
}

function azc_b_shortcodegetbreadcrumbs( $atts, $content = null ){
	if (empty($atts)){
		$type = 'text';
	}else{
		$attribs = implode('',$atts);
		$type = str_replace("'", '', str_replace('"', '', substr ( $attribs, 1)));
	}
	echo azc_b_generatebreadcrumbs(get_the_ID(), $type );
}
add_shortcode( 'getbreadcrumbs', 'azc_b_shortcodegetbreadcrumbs' );
add_shortcode( 'GetBreadcrumbs', 'azc_b_shortcodegetbreadcrumbs' );
add_shortcode( 'GETBREADCRUMBS', 'azc_b_shortcodegetbreadcrumbs' );

function azrcrv_b_displaybreadcrumbsbeforecontent($content) {
	$options = get_option( 'azc-b' );
	
	if ( $options['page-before'] != 'none' ) {
		return azc_b_generatepagebreadcrumbs(get_the_ID(), $options['page-before'] ) . $content;
	}else{
		return $content;
	}
}
add_filter( 'the_content', 'azrcrv_b_displaybreadcrumbsbeforecontent' );

function azrcrv_b_displaybreadcrumbsaftercontent($content) {
	$options = get_option( 'azc-b' );
	
	if ( $options['page-after'] != 'none' ) {
		return $content . azc_b_generatepagebreadcrumbs(get_the_ID(), $options['page-after'] );
	}else{
		return $content;
	}
}
add_filter( 'the_content', 'azrcrv_b_displaybreadcrumbsaftercontent' );

// return breadcrumbs
function azc_b_generatepagebreadcrumbs( $id, $type ) {
	$options = get_option( 'azc-b' );
	
	$breadcrumbs = '';
	if ( is_page($id) AND in_the_loop($id) ) {
					
		$breadcrumbs = azc_b_generatebreadcrumbs( $id, $type );
	
	}
	return $breadcrumbs;
}

// return breadcrumbs
function azc_b_generatebreadcrumbs( $id, $type ) {
	$options = get_option( 'azc-b' );
	
	$breadcrumbs = '';
	
	if ( $options['show-on-homepage'] == 1 AND is_front_page($id) or !is_front_page($id) ) {
		if (esc_html( stripslashes( $type )) == 'arrow'){
			$type = 'arrow';
			$breadcrumbseparator = '';
		}else{
			$type = 'text';
			$breadcrumbseparator = ' '.$options['breadcrumb-separator'].' ';
		}

		$post = get_post( $id );
		$title = $post->post_title;
		
		$parents = array();
		$parents = azc_b_getparents( $id, $parents);
		
		$pageurl = trailingslashit(get_site_url());
		
		if ( $options['add-homepage'] == 1 ) {
			$breadcrumbs .= '<a href="' . $pageurl . '" class="azc-b-'.$type.'breadcrumbs">'. get_bloginfo( 'name' ) . '</a>'.$breadcrumbseparator;
		}
		$link = '';
		foreach (array_reverse($parents) as $key => $value) {
		//for ($i = count($parents); $i = 0; $i--) {
			$link .= $value["name"].'/';
			$breadcrumbs .= '<a href="' . $pageurl . $link . '" class="azc-b-'.$type.'breadcrumbs">' . $value['title'] . '</a>'.$breadcrumbseparator;
		}
		if ( $type == 'arrow' ){
			$breadcrumbs .= '<span class="azc-b-arrowbreadcrumbs">' . $title . '</span>';
		}else{
			$breadcrumbs .= $title;
		}
		$breadcrumbs = "<div class='azc-b-arrowbreadcrumbscontainer'><div class='azc-b-".$type."breadcrumbs'>" . $breadcrumbs . "</div></div>";
	}
	
	return $breadcrumbs;
}

function azc_b_add_inline_css(){
	
	wp_enqueue_style( 'azc-b', plugins_url( 'style.css', __FILE__ ) );
	
	$options = get_option( 'azc-b' );
		
	wp_add_inline_style( 'azc-b', stripslashes($options['style-text']).stripslashes($options['style-arrow']) );
}
add_action( 'wp_enqueue_scripts', 'azc_b_add_inline_css' ); //Enqueue the CSS style

function azc_b_getparents( $id, $array){
	$parentid = wp_get_post_parent_id( $id );
	if ( $parentid != '' ) {
		$post = get_post( $parentid );
		$array[count($array)] = array("title"=>$post->post_title,"name"=>$post->post_name);
		$array = azc_b_getparents( $parentid, $array);
	}
	return $array;
}

// azurecurve menu
function azc_b_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=azc-b">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}
add_filter('plugin_action_links', 'azc_b_plugin_action_links', 10, 2);

function azc_create_b_plugin_menu() {
	global $admin_page_hooks;
    
	add_submenu_page( "azc-plugin-menus"
						,"Breadcrumbs"
						,"Breadcrumbs"
						,'manage_options'
						,"azc-b"
						,"azc_b_settings" );
}
add_action("admin_menu", "azc_create_b_plugin_menu");

function azc_b_settings() {
	if (!current_user_can('manage_options')) {
		$error = new WP_Error('not_found', __('You do not have sufficient permissions to access this page.' , 'azc_b'), array('response' => '200'));
		if(is_wp_error($error)){
			wp_die($error, '', $error->get_error_data());
		}
    }
	
	// Retrieve plugin configuration options from database
	$options = get_option( 'azc-b' );
	
	?>
	<div id="azc-b-general" class="wrap">
		<fieldset>
			<h2><?php _e('azurecurve Breadcrumbs Settings', 'azc-b'); ?></h2>
			<?php if( isset($_GET['settings-updated']) ) { ?>
				<div id="message" class="updated">
					<p><strong><?php _e('Settings have been saved.') ?></strong></p>
				</div>
			<?php } ?>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="save_azc_b_options" />
				<input name="page_options" type="hidden" value="show-on-homepageadd-homepage,before-title,after-title,page-before,page-after,style" />
				
				<!-- Adding security through hidden referrer field -->
				<?php wp_nonce_field( 'azc-b-nonce', 'azc-b-nonce' ); ?>
				<table class="form-table">
				<tr><th scope="row" colspan="2">
					<label for="explanation">
						azurecurve Breadcrumbs <?php _e('allows the display of breadcrumbs before and after the title and content.', 'azc_b'); ?>
					</label>
				</th></tr>
				<tr><th scope="row"><?php _e('Show on homepage', 'azc-b'); ?></th><td>
					<fieldset><legend class="screen-reader-text"><span>Show on homepage</span></legend>
						<label for="show-on-homepage"><input name="show-on-homepage" type="checkbox" id="show-on-homepage" value="1" <?php checked( '1', $options['show-on-homepage'] ); ?> /></label>
					</fieldset>
					<p class="description"><?php _e('Shows breadcrumbs on the homepage.', 'azc-b'); ?></p>
				</td></tr>
				<tr><th scope="row"><?php _e('Add homepage', 'azc-b'); ?></th><td>
					<fieldset><legend class="screen-reader-text"><span>Add homepage</span></legend>
						<label for="add-homepage"><input name="add-homepage" type="checkbox" id="add-homepage" value="1" <?php checked( '1', $options['add-homepage'] ); ?> /></label>
					</fieldset>
					<p class="description"><?php _e('Adds homepage to the breadcrumb trail.', 'azc-b'); ?></p>
				</td></tr>
				<tr><th scope="row"><?php _e('Breadcrumbs Before Page', 'azc-b'); ?></th><td>
					<select name="page-before">
						<option value="none" <?php if($options['page-before'] == 'none'){ echo ' selected="selected"'; } ?>>None</option>
						<option value="text" <?php if($options['page-before'] == 'text'){ echo ' selected="selected"'; } ?>>Text</option>
						<option value="arrow" <?php if($options['page-before'] == 'arrow'){ echo ' selected="selected"'; } ?>>Arrow</option>
					</select>
					<p class="description"><?php _e('Shows breadcumbs before page.', 'azc-b'); ?></p>
				</td></tr>
				<tr><th scope="row"><?php _e('Breadcrumbs After Page', 'azc-b'); ?></th><td>
					<select name="page-after">
						<option value="none" <?php if($options['page-after'] == 'none'){ echo ' selected="selected"'; } ?>>None</option>
						<option value="text" <?php if($options['page-after'] == 'text'){ echo ' selected="selected"'; } ?>>Text</option>
						<option value="arrow" <?php if($options['page-after'] == 'arrow'){ echo ' selected="selected"'; } ?>>Arrow</option>
					</select>
					<p class="description"><?php _e('Shows breadcumbs after page.', 'azc-b'); ?></p>
				</td></tr>
				<tr><th scope="row"><label for="breadcrumb-separator"><?php _e('Text Breadcrumbs Separator', 'azc-b'); ?></label></th><td>
					<input type="text" name="breadcrumb-separator" value="<?php echo esc_html( stripslashes($options['breadcrumb-separator']) ); ?>" class="regular-text" />
					<p class="description"><?php _e(sprintf('Character(s) to show between text breadcrumbs (for example %s or %s).', '<strong>&amp;raquo;</strong>', '<strong>::</strong>'), 'azc-b'); ?></p>
				</td></tr>
				<tr><th scope="row"><?php _e('Style for text breadcrumbs', 'azc-b'); ?></th><td>
					<textarea name="style-text" rows="20" cols="80" id="style-text" class="large-text code"><?php echo esc_html( stripslashes($options['style-text'])) ?></textarea>
				</td></tr>
				<tr><th scope="row"><?php _e('Style for arrow breadcrumbs', 'azc-b'); ?></th><td>
					<textarea name="style-arrow" rows="40" cols="80" id="style" class="large-text code"><?php echo esc_html( stripslashes($options['style-arrow'])) ?></textarea>
				</td></tr>
				<tr><th scope="row"><label for="shortcode"><?php _e('Shortcode', 'azc-b'); ?></label></th><td>
					<?php _e(sprintf('%s can be added anywhere to place breadcrumbs in desired location.', "<strong>[getbreadcrumbs=arrow]</strong>"), 'azc-b'); ?>
				</td></tr>
				<tr><th scope="row"><label for="function"><?php _e('Function', 'azc-b'); ?></label></th><td>
					<?php _e(sprintf('%s can be added to a theme to place breadcrumbs in desired location; exists syntax prevents errors if plugin deactivated.', "<strong>if (function_exists(azc_b_getbreadcrumbs)){ echo azc_b_getbreadcrumbs( 'arrow'); }</strong>"), 'azc-b'); ?>
				</td></tr>
				</table>
				<input type="submit" value="Submit" class="button-primary"/>
			</form>
		</fieldset>
	</div>
<?php }


function azc_b_admin_init() {
	add_action( 'admin_post_save_azc_b_options', 'process_azc_b_options' );
}
add_action( 'admin_init', 'azc_b_admin_init' );

function process_azc_b_options() {
	// Check that user has proper security level
	if ( !current_user_can( 'manage_options' ) ){
		wp_die( __('You do not have permissions to perform this action', 'azc-b') );
	}
	// Check that nonce field created in configuration form is present
	if ( ! empty( $_POST ) && check_admin_referer( 'azc-b-nonce', 'azc-b-nonce' ) ) {
	
		// Retrieve original plugin options array
		$options = get_option( 'azc-b' );
		
		$option_name = 'show-on-homepage';
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = 1;
		}else{
			$options[$option_name] = 0;
		}
		
		$option_name = 'add-homepage';
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = 1;
		}else{
			$options[$option_name] = 0;
		}
		
		$option_name = 'page-before';
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = ($_POST[$option_name]);
		}
		
		$option_name = 'page-after';
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = ($_POST[$option_name]);
		}
		
		$option_name = 'style-text';
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = ($_POST[$option_name]);
		}
		
		$option_name = 'style-arrow';
		if ( isset( $_POST[$option_name] ) ) {
			$options[$option_name] = ($_POST[$option_name]);
		}
		
		// Store updated options array to database
		update_option( 'azc-b', $options );
		
		// Redirect the page to the configuration form that was processed
		wp_redirect( add_query_arg( 'page', 'azc-b&settings-updated', admin_url( 'admin.php' ) ) );
		exit;
	}
}

?>