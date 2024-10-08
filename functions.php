<?php

require_once 'booking-sync/main.php';

//add_filter( 'show_admin_bar', '__return_false' );

// Registrazione header main menu
function register_my_menus() {
    register_nav_menus(array(
      'header-menu' => 'Header Menu',
      'footer-menu' => 'Footer Menu',
    ));
}
add_action( 'init', 'register_my_menus' );


function load_jquery() {
    if ( ! wp_script_is( 'jquery', 'enqueued' )) {
        wp_enqueue_script( 'jquery' );
    }
}
add_action( 'wp_enqueue_scripts', 'load_jquery' );

function rn24_support() {

  // Add support for block styles.
  add_theme_support( 'wp-block-styles' );
  add_theme_support(
      'html5',
      array(
          'comment-form',
          'comment-list',
          'gallery',
          'caption',
          'style',
          'script',
          'navigation-widgets',
      )
  );
}
add_action( 'after_setup_theme', 'rn24_support' );

/**
* Abilita le zone dedicate ai widgets
*/
function rn24_widgets_init() {
register_sidebar(
  array(
    'name'          => esc_html__( 'Banner Evento', 'rn24' ),
    'id'            => 'banner_evento',
    'description'   => esc_html__( 'Aggiunti i widget da mostrare in testa agli eventi', 'rn24' ),
    'before_widget' => '<section id="%1$s" class="widget banner-widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  )
);
}
add_action( 'widgets_init', 'rn24_widgets_init' );

/**
 * Custom login page
 */
function rn24_redirect_login_page() {
  $login_url  = home_url( '/login' );
  $url = basename($_SERVER['REQUEST_URI']); // get requested URL
  isset( $_REQUEST['redirect_to'] ) ? ( $url   = "wp-login.php" ): 0; // if users ssend request to wp-admin
  if( $url  == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET' && !(isset($_GET['action']) && isset($_GET['action']) == 'logout'))  {
	  if (isset($_GET['redirect_to'])) {
		$login_url = esc_url( add_query_arg( 'on_success', $_GET['redirect_to'], home_url( '/login' ) ) );
	  }
	  wp_redirect($login_url);
      exit;
  }
}
add_action('init', 'rn24_redirect_login_page');

/**
 * Custom login page form
 * 
 */
function rn24_wp_login_form($args = array()) {
	$redirect_url = isset($_GET['on_success']) ? $_GET['on_success'] : site_url();
 	$defaults = array(
		'echo'           => true,
		'redirect'       => $redirect_url,
		'form_id'        => 'loginform',
		'label_username' => __( 'Username or Email Address' ),
		'label_password' => __( 'Password' ),
		'label_remember' => __( 'Remember Me' ),
		'label_log_in'   => __( 'Log In' ),
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'remember'       => true,
		'value_username' => '',
		// Set 'value_remember' to true to default the "Remember me" checkbox to checked.
		'value_remember' => false,
	);
	$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );
    $form =
		sprintf(
			'<form name="%1$s" id="%1$s" action="%2$s" method="post">',
			esc_attr( $args['form_id'] ),
			esc_url( site_url( 'wp-login.php', 'login_post' ) )
		)
		.
		sprintf(
			'<div class="form-group login-username">
				<label for="%1$s">%2$s</label>
				<input type="text" name="log" id="%1$s" autocomplete="username" class="form-control w-100" value="%3$s" />
			</div>',
			esc_attr( $args['id_username'] ),
			esc_html( $args['label_username'] ),
			esc_attr( $args['value_username'] )
		) .
		sprintf(
			'<div class="form-group login-password">
				<label for="%1$s">%2$s</label>
				<input type="password" name="pwd" id="%1$s" autocomplete="current-password" spellcheck="false" class="form-control w-100" value=""/>
			</div>',
			esc_attr( $args['id_password'] ),
			esc_html( $args['label_password'] )
		)
		.
		sprintf(
			'<input type="hidden" name="redirect_to" value="%3$s" />
       <button type="submit" name="wp-submit" id="%1$s" class="btn btn-primary">%2$s</button>',
			esc_attr( $args['id_submit'] ),
			esc_attr( $args['label_log_in'] ),
			esc_url( $redirect_url )
		)
		.
		'<a href="'.wp_lostpassword_url().'" class="lost-password-url">Hai dimenticato la password?</a>'.
		'</form>';
    if ( $args['echo'] ) {
      echo $form;
    } else {
      return $form;
    }
}

/**
 * Handle login failed login
 */
add_action( 'wp_login_failed', 'rn24_front_end_login_fail' );
function rn24_front_end_login_fail( $username ) {
   $referrer = $_SERVER['HTTP_REFERER'];
   if ( !empty($referrer) && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin') ) {
		if (str_contains($referrer, '?')) {
			wp_redirect( $referrer . '&error-login=true' );
			exit;
		} else {
			wp_redirect( $referrer . '?error-login=true' );
			exit;
		}
   }
}

/**
 * 
 */
add_shortcode('rn24_signup_btn', 'rn24_show_signup_btn');
function rn24_show_signup_btn($atts, $content = null, $tag = '') {
	$_atts = shortcode_atts(
		array(
			'page_iscrizioni' => '/iscriviti',
      		'page_evento' => '/eventi/rn24'
		), $atts, $tag
	);
  if (is_user_logged_in()) {
	return sprintf(
		'<a class="btn-link" href="%1$s"><button class="btn btn-primary">Iscrivi la tua Comunità capi</button></a>',
		esc_attr( $_atts['page_evento'] )
	);
  } else {
	return sprintf(
		'<a class="btn-link" href="%1$s"><button class="btn btn-primary">Iscrivi la tua Comunità capi</button></a>',
		esc_attr( $_atts['page_iscrizioni'] )
	  );
  }
}

/**
 * Redicrect automatica alla homepage dopo il logout
 */
add_action('wp_logout','auto_redirect_after_logout');
function auto_redirect_after_logout(){
  wp_safe_redirect( home_url() );
  exit;
}

/**
 * Pagina personalizzata per recupera password
 */
add_action( 'login_form_lostpassword', 'rn24_lost_password_page' );
function rn24_lost_password_page() {
	wp_safe_redirect(site_url( 'recupera-password' ));
	exit();
}

/**
 * Prepare RN24 registration email
 */
function wpse27856_set_content_type(){
    return "text/html";
}
add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );

function prepare_email_base($template) {
	ob_start();
	include(get_template_directory().'/email/'.$template.'.email.php');
	$output = ob_get_contents();
	ob_end_clean();
	$output = str_replace("[RN24_BASE_URL]", site_url(), $output);
	$output = str_replace("[RN24_THEME_URL]", get_bloginfo('template_directory'), $output);
	return $output;
}

/**
 * Build template email for registration purpose
 */
function prepare_registration_email($username, $groupName, $password) {
	$output = prepare_email_base('registration');
	$output = str_replace("[RN24_TITLE]", 'Registrazione', $output);
	$output = str_replace("[RN24_GRUPPO]", $groupName, $output);
	$output = str_replace("[RN24_EMAIL]", $username, $output);
	$output = str_replace("[RN24_PASSWORD]", $password, $output);
	return $output;
}

/**
 * Build template email for recover password purpose
 */
function prepare_recover_password_email($username, $groupName, $key) {
	$output = prepare_email_base('recover-password');
	$output = str_replace("[RN24_TITLE]", 'Recupera password', $output);
	$output = str_replace("[RN24_GRUPPO]", $groupName, $output);
	$output = str_replace("[RN24_EMAIL]", $username, $output);
	$output = str_replace("[RN24_RESET_KEY]", $key, $output);
	return $output;
}


/**
 * Disable WordPress sends email for password update
 */
add_filter( 'send_password_change_email', '__return_false' );
add_filter( 'send_email_change_email', '__return_false' );
add_filter( 'wp_send_new_user_notification_to_user', '__return_false' );
add_filter( 'wp_send_new_user_notification_to_admin', '__return_false' );

// Our custom post type function
function create_rn24_timeline_post_type() {
    register_post_type( 'timeline',
        array(
            'labels' => array(
                'name' => __( 'Eventi percorso' ),
                'singular_name' => __( 'Evento percorso' )
            ),
            'public' => true,
            'has_archive' => true,
			'menu_icon' => 'dashicons-calendar',
            'rewrite' => array('slug' => 'timeline'),
            'show_in_rest' => true,
			'menu_position' => 6,
			'supports' => array( 
				'title', 
				'editor', 
				'thumbnail', 
				'custom-fields', 
				'revisions',
				'excerpt'
			  )
        )
    );

}
add_action( 'init', 'create_rn24_timeline_post_type' );

function create_rn24_timeline_taxonomy() {
    register_taxonomy(
        'timeline_categories',
        'timeline',
        array(
            'hierarchical' => true,
            'label' => 'Tipologia Evento Timeline',
			'show_in_menu' => true,
			'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'timeline_cat',
                'with_front' => false
            )
        )
    );
}
add_action( 'init', 'create_rn24_timeline_taxonomy');

add_theme_support('post-thumbnails');
add_theme_support( 'title-tag' );


function create_rn24_box_post_type() {
    register_post_type( 'box',
        array(
            'labels' => array(
                'name' => __( 'Scatole' ),
                'singular_name' => __( 'Scatola' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'box'),
            'show_in_rest' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-archive',
			'supports' => array( 
				'title', 
				'editor', 
				'thumbnail', 
				'custom-fields', 
				'revisions',
				'excerpt'
			  )
        )
    );

}
add_action( 'init', 'create_rn24_box_post_type' );

flush_rewrite_rules( false );

/**
 * Register Custom Navigation Walker
 */
function register_navwalker(){
	require_once get_template_directory() . '/include/class-wp-bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );

/**
 * Generate breadcrumbs
 * @author CodexWorld
 * @authorURL www.codexworld.com
 */
function get_breadcrumb() {
    echo '<a href="'.home_url().'" rel="nofollow">Home</a>';
    if (is_category() || is_single()) {
        echo "&nbsp;&nbsp;&gt;&nbsp;&nbsp;";
        the_category(' &bull; ');
            if (is_single()) {
                echo " &nbsp;&nbsp;&gt;&nbsp;&nbsp; ";
                the_title();
            }
    } elseif (is_page()) {
        echo "&nbsp;&nbsp;&gt;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&gt;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}


function get_italy_map() {
	return  file_get_contents(get_template_directory()."/img/italy.svg");
}

function rn24_get_zones($region) {
    $zones = array();
    foreach (rn24_get_groups() as $data) {
        if (strtoupper($data['Regione']) === strtoupper($region) && !in_array($data['zona'], $zones)) {
            array_push($zones, $data['zona']);
        }
    }
    return $zones;
}

function rn24_select_zones() {
    $region = stripslashes(isset($_POST['region']) ? strtoupper($_POST['region']) : '');
    $zones = rn24_get_zones($region);
	sort($zones);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($zones);
    wp_die();
}

add_action( 'wp_ajax_nopriv_rn24_select_zones', 'rn24_select_zones' );
add_action( 'wp_ajax_rn24_select_zones', 'rn24_select_zones' );

/**
 * FAQ
 */
function create_rn24_faq_post_type() {
    register_post_type( 'faq',
        array(
            'labels' => array(
                'name' => __( 'FAQ' ),
                'singular_name' => __( 'FAQ' )
            ),
            'public' => true,
            'has_archive' => true,
			'menu_icon' => 'dashicons-megaphone',
            'rewrite' => array('slug' => 'faq'),
            'show_in_rest' => true,
			'menu_position' => 7,
			'supports' => array( 
				'title', 
				'editor', 
				'thumbnail', 
				'custom-fields', 
				'revisions',
				'excerpt'
			  )
        )
    );

}
add_action( 'init', 'create_rn24_faq_post_type' );

function create_rn24_faq_taxonomy() {
    register_taxonomy(
        'faq_categories',
        'faq',
        array(
            'hierarchical' => true,
            'label' => 'Tipologia FAQ',
			'show_in_menu' => true,
			'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'faq_cat',
                'with_front' => false
            )
        )
    );
}
add_action( 'init', 'create_rn24_faq_taxonomy');



/*
 * Modulo per iscrizioni Comunità Capi
*/
 function _get_coca_signin_form() {

    $box_opts = '';
    $query = new WP_Query(array(
            'post_type' => 'box',
            'posts_per_page' => -1,
            'meta_key'  => 'box_number',
            'meta_type' => 'NUMERIC',
            'orderby' => 'meta_value_num',
            'order'          => 'ASC'
    ));
    
    $selected_box = get_user_meta(get_current_user_id(), '_selected_box', true);
    if ($query->have_posts() ) {
        $boxes = $query->get_posts();
        foreach ($boxes as $box) {
            $box_opts .= sprintf('<option '.
            ($selected_box == $box->ID ? 'selected': '')
            .' value="%s">%s</option>', $box->ID, $box->post_title);
        }
    } 
    
    $happy_desc = get_user_meta(get_current_user_id(), '_happy_description', true);
    $tangram_photo = get_user_meta(get_current_user_id(), 'tangram_photo', true);

    $signupform = <<<SIGNUPCOCAFORM
        <form method="POST" autocomplete="off" action="" class="rn24CocaSignupForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="felicita">Che cosa vi rende felici insieme nel servizio?</label>
                <textarea required maxlength="500" class="form-control" id="felicita" name="felicita" rows="10">$happy_desc</textarea>
                <small id="passwordHelpBlock" class="form-text text-muted">Massimo 500 caratteri</small>
            </div>
            <div style="font-size: 14px !important;margin-bottom:5px;">Carica la foto del Tangram della tua Comunità capi</div>
            
            <img class="tangram-photo-preview" src="$tangram_photo" style="max-width: 500px;">
            <div class="input-group mb-3">
                
                <div class="custom-file">
                    <input name="tangram-photo" type="file" class="custom-file-input" id="tangram-photo">
                    <label class="custom-file-label" for="tangram-photo">Seleziona</label>
                </div>
            </div>

            <div class="form-group" style="">
                <label for="box">In quale ambito si colloca l'orizzonte di felicità che sentite vi rappresenta tutti?</label>
                <select required id="box" name="selected-box" class="w-100 form-control" style="width: 100%">
                $box_opts
                </select>
            </div>
            <button type="submit" name="rn24-coca-sign-submit" class="btn btn-primary">Conferma</button>
        </form>
        SIGNUPCOCAFORM;

    return $signupform;
}

/*
 * Modulo per azioni felicità Comunità Capi
*/
function _get_coca_azione_felicita_form() {

    $happy_history = get_user_meta(get_current_user_id(), '_happy_history', true);
    $happy_irrinunciabile = get_user_meta(get_current_user_id(), '_happy_irrinunciabile', true);
    $happy_reel = get_user_meta(get_current_user_id(), '_happy_reel', true);

    $signupform = <<<SIGNUPCOCAHAPPYFORM
            <form method="POST" autocomplete="off" action="" class="rn24CocaSignupForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="felicita">1) La <b>narrazione del vostro capolavoro</b>: un testo libero (max 800 battute) che racconti il vostro percorso di felicità attraverso le azioni, i luoghi, la strada e le realtà coinvolte che lo hanno caratterizzato.</label>
                <textarea required maxlength="800" class="form-control" id="felicita" name="happy_history" rows="12">$happy_history</textarea>
                <small id="passwordHelpBlock" class="form-text text-muted">Massimo 800 caratteri</small>
            </div>
            <div style="font-size: 14px !important;margin-bottom:5px;">2) Una <b>foto o un reel</b>: che restituisca un momento importante e significativo del vostro percorso.</div>
            
            SIGNUPCOCAHAPPYFORM;

    if (!isset($happy_reel) || $happy_reel == null) {
        $signupform .= <<<SIGNUPCOCAHAPPYFORM
        <div class="input-group mb-3">
            
            <div class="custom-file">
                <input name="tangram-photo" type="file" class="custom-file-input" id="tangram-photo">
                <label class="custom-file-label" for="tangram-photo">Seleziona</label>
            </div>

            <p class="file-limit">La dimensione del file caricato non può superare 50MB.</p>

        </div>
        SIGNUPCOCAHAPPYFORM;
    } else {
        $signupform .= <<<SIGNUPCOCAHAPPYFORM
        <a class="input-group download-reel-btn" href="$happy_reel" target="_blank">
            <button class="btn btn-secondary"  type="button">Scarica</button>
        </a>
        <p class="file-limit">La dimensione del file caricato non può superare 50MB.</p>
        SIGNUPCOCAHAPPYFORM;
    }
    
    $signupform .= <<<SIGNUPCOCAHAPPYFORM

            <div class="form-group">
                <label for="box">3) Un <b>"irrinunciabile"</b>: un testo breve (max 300 battute) che presenti un'intuizione condivisa in Comunità capi, un'attenzione sperimentata, una frase di un documento, quelle parole di una testimonianza significativa che nel percorso avete sentito risuonare per la vostra felicità, ciò che credete fermamente debba diventare patrimonio di tutta l'Associazione, debba indirizzarne il percorso futuro affinché la vostra felicità vissuta e agita possa essere la felicità di tanti.</label>
                <textarea required maxlength="300" class="form-control" id="happy_irrinunciabile" name="happy_irrinunciabile" rows="8">$happy_irrinunciabile</textarea>
                <small id="passwordHelpBlock" class="form-text text-muted">Massimo 300 caratteri</small>
            </div>
            <button type="submit" name="rn24-coca-happy-submit" class="btn btn-primary">Conferma</button>
        </form>
        SIGNUPCOCAHAPPYFORM;

    return $signupform;
}

function _get_coca_signin_success_message() {
    return <<<SUCCESSMESSAGE
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Selezione avvenuta con successo!</h4>
        </div>
    SUCCESSMESSAGE;
}

function _get_coca_signin_error_message() {
    return <<<SUCCESSMESSAGE
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Errore durante la selezione</h4>
            <hr>
            <p class="mb-0">Contattaci per assistenza.</p>
        </div>
    SUCCESSMESSAGE;
}
function rn24_coca_signin_form($atts) {
    if(!is_user_logged_in()) {
        wp_redirect( wp_login_url(get_permalink()) );
    }
    if(isset($_GET['r24_coca_success']))
        return _get_coca_signin_success_message();

    if(isset($_GET['r24_coca_error']))
        return _get_coca_signin_error_message();

    return _get_coca_signin_form();
}

add_shortcode('rn24_coca_signin_form', 'rn24_coca_signin_form');

function rn24_coca_happy_form($atts) {
    if(!is_user_logged_in()) {
        wp_redirect( wp_login_url(get_permalink()) );
    }
    if(isset($_GET['r24_coca_success']))
        return _get_coca_signin_success_message();

    if(isset($_GET['r24_coca_error']))
        return _get_coca_signin_error_message();

    return _get_coca_azione_felicita_form();
}
add_shortcode('rn24_coca_happy_form', 'rn24_coca_happy_form');


function rn24_handle_coca_box_form(){

    if(!isset($_POST['rn24-coca-sign-submit'] ) )
        return;
    
    $redirect_url = sprintf(
        '%s%s', get_site_url(), $_SERVER['REQUEST_URI']
    );
  

    try {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        
        $photo = $_FILES['tangram-photo'];
        

        if ($photo['name']) {
            $uploadedfile = array(
                'name'     => $photo['name'],
                'type'     => $photo['type'],
                'tmp_name' => $photo['tmp_name'],
                'error'    => $photo['error'],
                'size'     => $photo['size']
            );
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile && !isset( $movefile['error'] ) ) {
                delete_user_meta(get_current_user_id(), 'tangram_photo');
                add_user_meta(get_current_user_id(), 'tangram_photo', $movefile["url"] );
            }
        }
        delete_user_meta(get_current_user_id(), '_selected_box');
        delete_user_meta(get_current_user_id(), '_happy_description');
        add_user_meta(get_current_user_id(), '_selected_box', $_POST['selected-box']);
        add_user_meta(get_current_user_id(), '_happy_description', $_POST['felicita']);
    } catch(Exception $e) {
        wp_redirect($redirect_url.'?r24_coca_error');
        exit();
    }
    wp_redirect($redirect_url.'?r24_coca_success');
    exit();
}


function rn24_handle_coca_happy_form(){
    if(!isset($_POST['rn24-coca-happy-submit'] ) )
        return;
    
    $redirect_url = sprintf(
        '%s%s', get_site_url(), $_SERVER['REQUEST_URI']
    );
  

    try {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        if (get_current_user_id() < 1) {
            wp_redirect($redirect_url.'?r24_coca_error');
            exit();
        }
            
        $photo = $_FILES['tangram-photo'];
        if (get_current_user_id() > 1) {
            $photo = $_FILES['tangram-photo'];
        }
        

        $upload_url = '';
        if ($photo && $photo['name']) {
            $uploadedfile = array(
                'name'     => $photo['name'],
                'type'     => $photo['type'],
                'tmp_name' => $photo['tmp_name'],
                'error'    => $photo['error'],
                'size'     => $photo['size']
            );
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile && !isset( $movefile['error'] ) ) {
                delete_user_meta(get_current_user_id(), '_happy_reel');
                add_user_meta(get_current_user_id(), '_happy_reel', $movefile["url"] );
                $upload_url = $movefile["url"];
            }
        }

        $happy_history = get_user_meta(get_current_user_id(), '_happy_history', true);
        $happy_irrinunciabile = get_user_meta(get_current_user_id(), '_happy_irrinunciabile', true);

        delete_user_meta(get_current_user_id(), '_happy_history');
        delete_user_meta(get_current_user_id(), '_happy_irrinunciabile');
        add_user_meta(get_current_user_id(), '_happy_history', $_POST['happy_history']);
        add_user_meta(get_current_user_id(), '_happy_irrinunciabile', $_POST['happy_irrinunciabile']);

        $currentUser = wp_get_current_user();
        // Create post object
        $azioneFelicita = array(
        'post_title'    => $currentUser->display_name,
        'post_content'  => $_POST['happy_history'],
        'post_status'   => 'publish',
        'post_author'   => get_current_user_id(),
        'post_type' => 'azioni_felicita',
        'meta_input'   => array(
            'irrinunciabile' => $_POST['happy_irrinunciabile'],
            'upload' => get_user_meta(get_current_user_id(), '_happy_reel', true)
            )
        );
        // Insert the post into the database
        wp_insert_post( $azioneFelicita );
    } catch(Exception | Error  $e) {
		error_log($e->getMessage());
        wp_redirect($redirect_url.'?r24_coca_error');
        exit();
    }

    wp_redirect($redirect_url.'?r24_coca_success');
    exit();
}

add_action( 'init', 'rn24_handle_coca_box_form' );
add_action( 'init', 'rn24_handle_coca_happy_form' );

add_action( 'show_user_profile', 'extra_user_profile_fields_rn24' );
add_action( 'edit_user_profile', 'extra_user_profile_fields_rn24' );

function extra_user_profile_fields_rn24( $user ) {
    $box_opts = '';
    $query = new WP_Query(array(
            'post_type' => 'box',
            'posts_per_page' => -1,
            'meta_key'  => 'box_number',
            'meta_type' => 'NUMERIC',
            'orderby' => 'meta_value_num',
            'order'          => 'ASC'
    ));
          
    if ($query->have_posts() ) {
        $boxes = $query->get_posts();
        foreach ($boxes as $box) {
            $box_opts .= sprintf('<option '.
            (get_user_meta($user->ID, '_selected_box', true) == $box->ID ? 'selected': '')
            .' value="%s">%s</option>', $box->ID, $box->post_title);
        }
    } 
    
    ?>
    <h3>RN24</h3>

    <table class="form-table">
    <tr>
        <th><label for="selected-box">Scatola selezionata</label></th>
        <td>
            <select id="box" name="selected-box" class="w-100 form-control" style="width: 100%">
               <?php echo $box_opts; ?>
            </select>            
        </td>
    </tr>
    <tr>
        <th><label for="happy-description">Descrizione felicità</label></th>
        <td>
            <textarea name="selected-box" id="selected-box" class="regular-text"><?php echo esc_attr( get_user_meta($user->ID, '_happy_description', true) ); ?></textarea><br />
        </td>
    </tr>
    <tr>
        <th><label for="tangram-photo">Tangram</label></th>
        <td>
            <img src="<?php echo get_user_meta($user->ID, 'tangram_photo', true); ?>" style="max-width: 500px;">
        </td>
    </tr>
    </table>
<?php }


/**
 * Songs
 */
function create_rn24_song_post_type() {
    register_post_type( 'song',
        array(
            'labels' => array(
                'name' => __( 'Canzoni' ),
                'singular_name' => __( 'Canzone' )
            ),
            'public' => true,
            'has_archive' => true,
			'menu_icon' => 'dashicons-format-audio',
            'rewrite' => array('slug' => 'song'),
            'show_in_rest' => true,
			'menu_position' => 7,
			'supports' => array( 
				'title', 
				'editor', 
				'thumbnail', 
				'custom-fields', 
				'revisions'
			  )
        )
    );

}
add_action( 'init', 'create_rn24_song_post_type' );




/**
 * Grab latest post title by an author!
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest, * or null if none.
 */
function get_coca_boxes( $data ) {    
    global $wpdb;    
    
    $sql = "SELECT u.user_login AS codice_gruppo, u.display_name AS denominazione, g.zona, g.regione,
    (SELECT p.post_title FROM wp_usermeta umb 
    LEFT JOIN wp_posts p ON p.ID = umb.meta_value where umb.meta_key = '_selected_box'
    and umb.user_id = u.ID) AS selected_box
    FROM wp_usermeta um
    LEFT JOIN wp_users u ON u.ID = um.user_id
    LEFT JOIN rn24_gruppi g ON g.codice_gruppo = u.user_login
     WHERE um.meta_key = '_selected_box' ";

    if (isset($_GET['zona'])) {
        $sql .= " AND g.zona = %s ";
    }
    $sql .= " HAVING selected_box = %s ORDER BY u.display_name";
    $safe_sql = isset($_GET['zona']) ? $wpdb->prepare(
        $sql, [$_GET['zona'], $_GET['box']]
    ) : $wpdb->prepare($sql, $_GET['box']);

    $result = $wpdb->get_results($safe_sql);
    return $result;
  }

  add_action( 'rest_api_init', function () {
    register_rest_route( 'rn24/v1', '/boxes/', array(
      'methods' => 'GET',
      'callback' => 'get_coca_boxes',
      'permission_callback' => '__return_true',
    ) );
    register_rest_route( 'rn24/v1', '/boxes/export', array(
        'methods' => 'GET',
        'callback' => 'get_coca_happines_export',
        'permission_callback' => '__return_true',
      ) );
      register_rest_route( 'rn24/v1', '/azioni-felicita/export', array(
        'methods' => 'GET',
        'callback' => 'get_coca_azioni_felicita_export',
        'permission_callback' => '__return_true',
      ) );

      register_rest_route( 'rn24/v1', '/visitatori-arena24/export', array(
        'methods' => 'GET',
        'callback' => 'export_visitatori_arena24',
        'permission_callback' => '__return_true'
      ) );
      
  } );

  /**
 * 
 *
 * @param array $data Options for the function.
 * @return string|null Post title for the latest, * or null if none.
 */
function get_coca_happines_export( $data ) {
    global $wpdb;    
    
    $sql = "SELECT u.user_login AS codice, u.display_name AS display_name, g.zona, g.regione,
    (SELECT p.post_title FROM wp_usermeta umb 
    LEFT JOIN wp_posts p ON p.ID = umb.meta_value where umb.meta_key = '_selected_box'
    and umb.user_id = u.ID) AS selected_box,
    (SELECT umb.meta_value FROM wp_usermeta umb where umb.meta_key = 'tangram_photo' and umb.user_id = u.ID) AS tangram_photo,
    um.meta_value as descrizione
    FROM wp_usermeta um
    LEFT JOIN wp_users u ON u.ID = um.user_id
    LEFT JOIN rn24_gruppi g ON g.codice_gruppo = u.user_login
    WHERE um.meta_key = '_happy_description'
    ORDER BY u.display_name";

    $result = $wpdb->get_results($sql);

    $out = '"Codice gruppo";"Gruppo";"Zona";"Regione";"Felici di...";"Descrizione";"Immagine";'."\r\n";

    foreach ($result as &$value) {
        //If the character " exists, then escape it, otherwise the csv file will be invalid.
        $out .= '"'.$value->codice.'";';
        $out .= '"'.$value->display_name.'";';
        $out .= '"'.$value->zona.'";';
        $out .= '"'.$value->regione.'";';
        $out .= '"'.$value->selected_box.'";';
        $out .= '"'.str_replace('"', '""', $value->descrizione).'";';
        $out .= '"'.$value->tangram_photo.'"'."\r\n";
    }

    //var_dump($out);

    // Output to browser with the CSV mime type
    header("Content-type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=rn24_felici_di_export.csv");
    header("Content-Transfer-Encoding: UTF-8");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo $out;
  }



  function get_coca_azioni_felicita_export( $data ) {
    global $wpdb;    
    
    $sql = "SELECT u.user_login AS codice, u.display_name AS display_name, g.zona, g.regione,
    (SELECT umb.meta_value FROM wp_postmeta umb WHERE  um.ID = umb.post_id and umb.meta_key = 'irrinunciabile') AS irrinunciabile,
    (SELECT umb.meta_value FROM wp_postmeta umb WHERE  um.ID = umb.post_id and umb.meta_key = 'upload') AS upload,
    um.post_content as 'azione_felicita'
    FROM wp_posts um
    LEFT JOIN wp_users u ON u.ID = um.post_author
    LEFT JOIN rn24_gruppi g ON g.codice_gruppo = u.user_login
    WHERE um.post_type = 'azioni_felicita' AND um.post_author <> 1
    ORDER BY u.display_name";

    $result = $wpdb->get_results($sql);

    $out = '"Codice gruppo";"Gruppo";"Zona";"Regione";"Irrinunciabile";"Media";"Azione felicità";'."\r\n";

    foreach ($result as &$value) {
        //If the character " exists, then escape it, otherwise the csv file will be invalid.
        $out .= '"'.$value->codice.'";';
        $out .= '"'.$value->display_name.'";';
        $out .= '"'.$value->zona.'";';
        $out .= '"'.$value->regione.'";';
        $out .= '"'. str_replace("\r\n", "", str_replace('"', '""', $value->irrinunciabile)) .'";';
        $out .= '"'.$value->upload.'";';
        $out .= '"'.str_replace("\r\n", "", str_replace('"', '""', $value->azione_felicita)).'"'."\r\n";
    }

    //var_dump($out);

    // Output to browser with the CSV mime type
    header("Content-type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=rn24_azioni_felicita_export.csv");
    header("Content-Transfer-Encoding: UTF-8");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo $out;
  }


  /**
 * FAQ
 */
function create_azioni_felicita_post_type() {
    register_post_type( 'azioni_felicita',
        array(
            'labels' => array(
                'name' => __( 'Azioni di felicità' ),
                'singular_name' => __( 'Azione di felicità' )
            ),
            'public' => true,
            'has_archive' => true,
			'menu_icon' => 'dashicons-smiley',
            'rewrite' => array('slug' => 'happy-action'),
            'show_in_rest' => true,
			'menu_position' => 8,
			'supports' => array( 
				'title', 
				'editor', 
                'author',
				'thumbnail', 
				'custom-fields', 
				'revisions',
				'excerpt'
			  )
        )
    );

}
add_action( 'init', 'create_azioni_felicita_post_type' );

 /**
 * FAQ
 */
function create_sustainability_post_type() {
    register_post_type( 'sustainability',
        array(
            'labels' => array(
                'name' => __( 'Sostenibilità' ),
                'singular_name' => __( 'Sostenibilità' )
            ),
            'public' => true,
            'has_archive' => true,
			'menu_icon' => 'dashicons-buddicons-replies',
            'rewrite' => array('slug' => 'sostenibilita'),
            'show_in_rest' => true,
			'menu_position' => 9,
			'supports' => array( 
				'title', 
				'editor', 
				'thumbnail', 
				'custom-fields', 
				'revisions',
				'excerpt'
			  )
        )
    );

}
add_action( 'init', 'create_sustainability_post_type' );




function export_visitatori_arena24( $data ) {
    global $wpdb;    
    
    $sql = "SELECT t.id, tt.title,
(SELECT UPPER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = '_give_donor_billing_first_name') AS nome,
(SELECT UPPER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = '_give_donor_billing_last_name') AS cognome,
(SELECT LOWER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = '_give_payment_donor_email') AS email,
(SELECT LOWER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = '_give_payment_donor_phone') AS telefono,
(SELECT UPPER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = 'codice_fiscale_1') AS codice_fiscale,
(SELECT UPPER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = 'data_di_nascita') AS data_nascita,
(SELECT UPPER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = 'luogo_di_nascita') AS luogo_nascita,
(SELECT UPPER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = 'campo_di_testo') AS provincia,
(SELECT UPPER(dm.meta_value) FROM wp_give_donationmeta dm WHERE dm.donation_id = t.donation_id AND dm.meta_key = 'con_quale_mezzo_di_trasporto_arriverai_a_villa_buri') AS mezzo_trasporto,
COUNT(*) AS numero_biglietti
FROM wp_give_event_tickets t
LEFT JOIN wp_give_event_ticket_types tt ON tt.id = t.ticket_type_id
GROUP BY email
ORDER BY tt.title desc
";

    $result = $wpdb->get_results($sql);

    $out = '"ID";"Giorno";"Nome";"Cognome";"Email";"Telefono";"Codice Fiscale";"Data nascita";"Luogo nascita";"Provincia";"Mezzo trasporto";"Num. biglietti";'."\r\n";

    foreach ($result as &$value) {
        //If the character " exists, then escape it, otherwise the csv file will be invalid.
        $out .= '"'.$value->id.'";';
        $out .= '"'.$value->title.'";';
        $out .= '"'.$value->nome.'";';
        $out .= '"'.$value->cognome.'";';
        $out .= '"'.$value->email.'";';
        $out .= '"'.$value->telefono.'";';
        $out .= '"'.$value->codice_fiscale.'";';
        $out .= '"'.$value->data_nascita.'";';
        $out .= '"'.$value->luogo_nascita.'";';
        $out .= '"'.$value->provincia.'";';
        $out .= '"'.$value->mezzo_trasporto.'";';
        $out .= '"'.str_replace("\r\n", "", str_replace('"', '""', $value->numero_biglietti)).'"'."\r\n";
    }

    //var_dump($out);

    // Output to browser with the CSV mime type
    header("Content-type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=visitatori_arena24.csv");
    header("Content-Transfer-Encoding: UTF-8");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo $out;
  }