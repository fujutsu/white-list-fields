<?php
/*
Plugin Name: Additional Fields
Description: Adds additional fields for storing URL links.
Version: 1.0
Author: <a href="https://www.facebook.com/fujutsu">Ihor Kraikivskyi</a>
*/







/**
 * Plugin direction path.
 */
define( 'AF_PATH', plugin_dir_url( __FILE__ ) );



/**
 * Add custom title.
 */
function username_title(){
	$username = af_get_url()[1];
	
	if( !isset($username) or !get_user_by('login', $username) ){
		return;
	}
	return ucfirst($username);
}

add_theme_support('title-tag');
add_filter('wp_title', 'username_title', 99);
add_filter('pre_get_document_title', 'username_title', 99);




/**
 * Adds an item (page) of the top level in the admin panel menu (in a row with posts, pages, users, etc.).
 */
add_action('admin_menu', function(){
	add_menu_page( 'Additional Input URL fields', 'Additional fields', 'manage_options', 'additional-fields-page', 'af_menu_page', 'dashicons-admin-links', '110.86' ); 
} );

function af_menu_page(){
	?>
	<h2 style="margin-top: 35px; text-align: center;"><?php echo get_admin_page_title(); ?></h2>
	<p>
		<form role="form" action="/wp-admin/options.php" method="POST">
			<?php
				// settings_errors() does not fire automatically on pages other than options
				if( get_current_screen()->parent_base !== 'options-general' )
					settings_errors('Option_name');
					
				settings_fields("af_option_group");     // hidden protective fields
				do_settings_sections("additional-fields-page"); // section with settings (options).
				submit_button();
			?>
		</form>
		
		<div style="background-color: #E3E3E3; margin: 50px 10px 10px 10px; padding: 10px 10px 10px 10px; border-radius: 10px 10px 10px 10px;">
			<div><strong>Reference:</strong></div><br>
			<div><strong>*</strong> To display a form with input fields for links, paste this shortcode into the WordPress page or template code:</div>
			<div style="margin-left: 9px;"> Template code: <strong style="margin-left: 27px;"><?php echo htmlspecialchars("<?php echo do_shortcode('[af_shortcode]'); ?>"); ?></strong></div>
			<div style="margin-left: 9px;"> WordPress page: <strong style="margin-left: 20px;">[af_shortcode]</strong></div>
			<br>
			<div><strong>*</strong> For the possibility of users registering from shortcode form, the option <strong>"Anyone can register"</strong> in the site section <strong>"Settings">"General"</strong> should be enabled.</div>
			<br>
			<div><strong>*</strong> To correctly compare links from this admin whitelist page with links entered by users in the form of a shortcode, use the link format</div>
			<div style="margin-left: 9px;"> without <strong>"http"</strong> or <strong>"www"</strong> at the beginning in the whitelist on this page. For example, just domain.com</div>
			<div style="margin-left: 9px;"></div>
		</div>
	</p>
	<?php
}




/**
 * Correctly connects the scripts.
 */
function af_admin_scripts() {
	
	wp_register_script( 'admin-fields', AF_PATH . 'js/admin-fields.js', array('jquery'), null, true);
	wp_enqueue_script( 'admin-fields' );
	}
add_action('admin_enqueue_scripts', 'af_admin_scripts', 1);

function af_frontend_scripts() {
	
	wp_register_script( 'frontend-fields', AF_PATH . 'js/frontend-fields.js', array('jquery'), null, true);
	wp_enqueue_script( 'frontend-fields' );
	
	wp_register_style( 'frontend-fields-style', AF_PATH . 'css/frontend-fields-style.css' );
	wp_enqueue_style( 'frontend-fields-style' );
}
add_action('wp_enqueue_scripts', 'af_frontend_scripts', 1);




/**
 * Register settings.
 * Settings will be stored in the array, not one setting = one option.
 */
add_action('admin_init', 'af_plugin_settings');
function af_plugin_settings(){
	// options: $option_group, $option_name, $sanitize_callback
	register_setting( 'af_option_group', 'af_option', 'sanitize_callback' );

	// options: $id, $title, $callback, $page
	add_settings_section( 'af_section', 'Main settings', '', 'additional-fields-page' ); 

	// options: $id, $title, $callback, $page, $section, $args
	add_settings_field('field_count', 'Number of fields', 'field_count_callback', 'additional-fields-page', 'af_section' );
	add_settings_field('field_whitelist', 'Whitelist', 'field_whitelist_callback', 'additional-fields-page', 'af_section' );
}


// Fill in option "Available fields"
function field_count_callback(){
	$val = get_option('af_option');
	$val = $val['field_count'];
	?>
	<input type="number" name="af_option[field_count]" value="<?php echo esc_attr( $val ); ?>" min="1" max="10"  >
	<?php
}

// Fill in option "Whitlist"
function field_whitelist_callback(){
	$val = get_option('af_option');
	?>
		<div class="multi-field-wrapper">
			<div class="multi-fields">
			
				<?php if( array_key_exists('field_whitelist1', $val) ): ?>
				
					<?php foreach( $val as $index => $value ): ?>
						<?php if( $index != 'field_count' ): ?>
							<div class="multi-field" style="margin-bottom: 10px;">
								<input type="text" name="af_option[<?php echo $index; ?>]" value="<?php echo esc_attr( $value ); ?>">
								<button type="button" class="remove-field">Remove</button>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
					
				<?php else: ?>
				
					<div class="multi-field" style="margin-bottom: 10px;">
						<input type="text" name="af_option[field_whitelist1]" value="">
						<button type="button" class="remove-field">Remove</button>
					</div>
					
				<?php endif; ?>
				
			</div>
			<p><button type="button" class="add-field">Add field</button></p>
		</div>
	<?php
}

// Clear data.
function sanitize_callback( $options ){ 
	
	foreach( $options as $name => & $val ){
		if( $name == 'input' )
			$val = strip_tags( $val );

		if( $name == 'checkbox' )
			$val = intval( $val );
	}
	return $options;
}




/**
 * Initializing the myajax.url variable for javascript.
 */
function af_ajax_url_script() {
	
	wp_localize_script('frontend-fields', 'af_ajax',
		array( 'url' => admin_url('admin-ajax.php') )
	);
}
add_action( 'wp_enqueue_scripts', 'af_ajax_url_script', 99);




/**
 * Whitelist validation.
 */
function my_is_valid_domain( $url ) {
	
	$whitelisted_domains = array_filter(get_option('af_option'));
	unset( $whitelisted_domains['field_count'] );
	
	
	$url = str_ireplace('www.', '', $url);
	
	if( stripos($url, 'http://') === false and stripos($url, 'https://') === false ){
		$url = 'http://' . $url;
	}
	
	$domain = parse_url( $url, PHP_URL_HOST );
	$domain = strtolower( $domain );
		
	// Check if we match the domain exactly
	if ( in_array( $domain, $whitelisted_domains ) )
		return 'true';

	$valid = 'false';
	
	foreach( $whitelisted_domains as $whitelisted_domain ) {
		$whitelisted_domain = '.' . $whitelisted_domain; // Prevent things like 'evilsitetime.com'
		if( strpos( $domain, $whitelisted_domain ) === ( strlen( $domain ) - strlen( $whitelisted_domain ) ) ) {
			$valid = 'true';
			break;
		}
	}
	return $valid;
}




/**
 * Ajax Whitelist hendler.
 */
function af_frontend_fields(){
	
	// POST processing
	$url = trim( $_POST['params'] );
	
	if( $url == '' ){
		echo 'empty';
		wp_die();
	}
	
	echo my_is_valid_domain($url);
	wp_die();
}
add_action('wp_ajax_af_frontend', 'af_frontend_fields');
add_action('wp_ajax_nopriv_af_frontend', 'af_frontend_fields');




/**
 * Gets the URL and parse it.
 */
function af_get_url(){
	$parsed_url = explode('/', $_SERVER['REQUEST_URI']);
	return $parsed_url;	
}




/**
 * URL domain to lower case.
 */
function af_domaintolower( $fields ){
	
	foreach($fields as $index => $url){
			
		if( stripos($url, 'http://') === false and stripos($url, 'https://') === false ){
			$url = 'http://' . $url;
			$edited = true; // if http or https was added in code - remove it in the end
		}
				
		$domain = parse_url( $url, PHP_URL_HOST );
		$domain_lower = str_ireplace($domain, strtolower($domain), $url);
		$fields[$index] = $domain_lower;
		
		if( isset($edited) ){
			$fields[$index] = str_ireplace( array('http://', 'https://'), '', $fields[$index] );	
			unset($edited);
		}	
	}
	return $fields;
}

	
	

/**
 * Sets cookies and validation while registering form fields, to create a link page.
 */
function af_set_cookie_and_validation(){
	
	if( isset($_POST['fields']) and isset($_POST['front-page']) ){

		// number of admissible fields by the administrator
		$admissible_fields = get_option('af_option')['field_count'];
		
		// validation
		$url_list_valdiation = array_map('trim', $_POST['fields']);
		$whitelist_valdiation = array_map('my_is_valid_domain', $url_list_valdiation);
		
		
		if( in_array('', $whitelist_valdiation) or count($_POST['fields']) > $admissible_fields or in_array('false', $whitelist_valdiation) ) {
			header('Location:' . home_url());
		}
		else{
			$_POST['fields'] = af_domaintolower( $_POST['fields'] );
			
			$cookie_serialize = base64_encode(serialize($_POST['fields']));
			setcookie('front_url_fields', $cookie_serialize, time()+900);
			
			header('Location:' . home_url() . '/register');
		}
		
		exit();
	}
}
add_action( 'init', 'af_set_cookie_and_validation');
	
	
	
	
/**
 * Unserialize cookie.
 */
function af_unserialize_cookie( $cookie ){
	$unserialize_cookie = unserialize( base64_decode($cookie) );
	$restored_cookie = array_map('stripslashes', $unserialize_cookie);
	
	return $restored_cookie;
}
		
	

	
/**
 * Executing during user authorization.
 */
function af_meta_fields_update( $user_login, $user ) {
	
	$user_url_list = get_user_meta( $user->ID, 'af_url_list', true );

	if( isset($_COOKIE['front_url_fields']) and !empty(af_unserialize_cookie($_COOKIE['front_url_fields'])) and $user_url_list == false ) { // і пустий url-list
		update_user_meta( $user->ID, 'af_url_list', af_unserialize_cookie($_COOKIE['front_url_fields']) );
		update_user_meta( $user->ID, 'af_page_created', array('afer_login' => 0) );
		
		header('Location:' . home_url() . '/' . $user_login);
		exit();
	}
	elseif( $user_url_list == true ){
		header('Location:' . home_url() . '/' . $user_login);
		exit();
	}
}
add_action('wp_login', 'af_meta_fields_update', 10, 2);




/**
 * Executing during user registration.
 */
function af_user_registration_meta( $meta, $user, $update ) {
	
	// go out if this is not a user registration
	if( $update ){
		return $meta;
	}
			
	// create user URL list
	if( isset($_COOKIE['front_url_fields']) and !empty(af_unserialize_cookie($_COOKIE['front_url_fields'])) ) { 
		$meta['af_url_list'] = af_unserialize_cookie($_COOKIE['front_url_fields']);
		$meta['af_page_created'] = array('afer_register' => 0);
	}
	return $meta;
}
add_filter( 'insert_user_meta', 'af_user_registration_meta', 10, 3 );




/**
 * Redirect user to his personal page after registration.
 */
function af_register_redirect_func( $user_id ){
	$user_login = get_user_by( 'id' , $user_id );
	
	header('Location:' . home_url() . '/' . $user_login->user_login);
	exit;
}
add_action('register_new_user', 'af_register_redirect_func');

	


/**
 * Include a page template for the display page of user fields and their editing.
 */ 
add_filter('template_include', 'book_archive_tpl_include');
function book_archive_tpl_include( $template ){
	
	$parsed_url = af_get_url();
	
	$get_users_list = get_users( array( 'fields' => array( 'user_login' ) ) );
	$users_list = array();
	
	foreach( $get_users_list as $index ){
		array_push( $users_list , $index->user_login);
	}
		
	// include template if first param from url (username) - if this user exist
	if( in_array($parsed_url[1], $users_list) and empty($parsed_url[2]) ){
		$template = plugin_dir_path(__FILE__) . 'templates/user-page.php';
	}
	elseif( in_array($parsed_url[1], $users_list) and $parsed_url[2] == 'edit' and empty($parsed_url[3]) ){
		$template = plugin_dir_path(__FILE__) . 'templates/user-page-edit.php';
	}

	return $template;
}




/**
 *  Updates fields on the edit page.
 */
if( isset($_POST['fields']) and isset($_POST['edit-page']) ){

	// $_SERVER['HTTP_REFERER'] - full URL of the page where the user came from
	// $url[0] - without GET parameters
	// this we need for the correct redirects
	$url = explode("?" , $_SERVER['HTTP_REFERER']);
	 
	// file connected to initialize the function of the get_current_user_id()
	require_once( ABSPATH . 'wp-includes/pluggable.php' );
	
	// get the object of the current user with the necessary data
	$user_ID = get_current_user_id();

	// get the user object from the referral page
	$referer_user = get_user_by('login', explode( "/" , $_SERVER['HTTP_REFERER'] )[3]);
	
	// if the user is not logged in or his page is not edited by him
	if( !is_user_logged_in() or $user_ID != $referer_user->ID ){
		header('Location:' . $url[0]);
		exit;
	}
	
	
	// number of admissible fields by the administrator
	$admissible_fields = get_option('af_option')['field_count'];
	
	
	$url_list_valdiation = array_map('trim', $_POST['fields']);
	$whitelist_valdiation = array_map('my_is_valid_domain', $url_list_valdiation);
	

	// data validation
	if( in_array('', $whitelist_valdiation) or count($_POST['fields']) > $admissible_fields or in_array('false', $whitelist_valdiation) ) {
	
		// error validation 
		header('Location:' . $url[0]);
		exit;
		
	} else {
		
		$url_list_valdiation = af_domaintolower($url_list_valdiation);
		
		// success updete url list 
		update_user_meta($user_ID, 'af_url_list', $url_list_valdiation);
		update_user_meta($user_ID, 'af_page_updated', 'updated');
	}
	 
	// if the code execution has reached here, then everything is OK
	header('Location:' . home_url() . '/' . $user_login);
	exit;
}




/**
 * Shortcode for displaying addidional fields
 */
function af_shortcode_func(){
	
	// file connected to initialize the function of the get_current_user_id()
	require_once( ABSPATH . 'wp-includes/pluggable.php' );
	
	if( is_user_logged_in() ){
		$list_exist = get_user_meta( get_current_user_id() , 'af_url_list' );
		if( $list_exist != false ){
			return false;
		}
	}
	
	// number of admissible fields by the administrator
	$admissible_fields = get_option('af_option')['field_count'];
	
    return '<div class="container">
				<div class="row">
					<div class="control-group" id="fields">
						<div class="controls"> 
								<form action="' . home_url() . '/wp-login.php" method="POST" id="af-form" role="form" autocomplete="off">
									<div class="af-wrap">
										<div class="entry input-group col-xs-3">
											<span class="af-favicon"></span>
											<input class="form-control" name="fields[]" type="text" value="" placeholder="Type URL"/>
											<span class="input-group-btn">
												<button class="btn btn-success btn-add" type="button">
													<span class="glyphicon glyphicon-plus"></span>
												</button>
											</span>
										</div>
									</div>
									<div class="warning-messages"></div>
									<input type="button" name="af-button-front" class="btn btn-success" value="Create my page">	
									<input type="hidden" name="front-page">	
								</form>
								<input type="hidden" id="allowable-number-of-fields" value="' . $admissible_fields . '">
						</div>
					</div>
				</div>
			</div>';
}
add_shortcode( 'af_shortcode', 'af_shortcode_func' );
?>