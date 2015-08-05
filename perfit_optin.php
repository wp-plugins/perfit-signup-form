<?php
    /*
    Plugin Name: Perfit Optin
    Plugin URI: http://www.perfit.com.ar/
    Description: Plugin para suscribir contactos desde tu sitio WordPress
    Author: Perfit dev team
    Version: 1.1.3
    Author URI: https://developers.myperfit.com/wordpress
    */

include(dirname(__FILE__).'/includes/loader.php');

/***** Sanitize all params (GET, POST) *****/
/*
function sanitize_recursively ($data) {
    if (!is_array($data))
        return sanitize_text_field($data);
    foreach ($data as $k => $v) {
        $data[$k] = sanitize_recursively($v);
    }
    return $data;
}
foreach (array('_POST', '_GET') as $var) {
    ${$var} = sanitize_recursively(${$var});
}
*/

if(is_admin())
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

    wp_register_script(
        'bootstrap',
        plugins_url( 'js/bootstrap.min.js', __FILE__),
        array( 'jquery')
    );
    wp_register_script( 'perfit-app-js', 
        plugins_url( '/js/app.js', __FILE__ ), 
        array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'bootstrap')
    );

    wp_enqueue_script('bootstrap');
    wp_enqueue_script('perfit-app-js');

    wp_enqueue_style( 'bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__) );
    wp_enqueue_style( 'perfit-css', plugins_url( '/css/style.css', __FILE__) );
    wp_enqueue_style( 'perfit-custom-css', plugins_url( '/css/custom.css', __FILE__) );

}

$query_args = array(
    'family' => 'Source+Sans+Pro:400,300,700,600|Open+Sans:400,300,600,700,700italic',
    'subset' => 'latin,latin-ext',
);
wp_enqueue_style( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );

wp_enqueue_style( 'perfit-optin-default', plugins_url( '/css/default.css', __FILE__) );

// function google_fonts() {
//     $query_args = array(
//         'family' => 'Source+Sans+Pro:400,300,700,600|Open+Sans:400,300,600,700,700italic',
//         'subset' => 'latin,latin-ext',
//     );
//     wp_register_style( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
// }

/*
 * Process login action
 */
if ($_POST['login'] == 1) {
    $return = $perfit->login($_POST['email'], $_POST['password'], $_POST['account']);
// echo '<pre>'.print_r($_SESSION, true).'</pre>';
// echo '<pre>'.print_r($return, true).'</pre>';

    if ($return->error->type == 'ACCOUNT_REQUIRED') {
        $accountList = $return->data;
        $error = $return->error->userMessage;
        $_SESSION['error'] = $error;
        $_SESSION['accountList'] = $accountList;
        $_SESSION['userEmail'] = $_POST['email'];
        $_SESSION['last_action'] = time();
    }
    else if (!$return->success) {
        $error = $return->error->userMessage;
        $_SESSION['error'] = $error;
        unset($_SESSION['last_action']);
    }
    else {
        $_SESSION['token'] = $return->data->token;
        $_SESSION['account'] = $return->data->account;
        $_SESSION['acl'] = $return->data->acl->{$return->data->account};
        $_SESSION['last_action'] = time();
    }

// echo '<pre>'.print_r($_SESSION, true).'</pre>';
// die('a');
}

/*
 * Process login action
 */
add_shortcode('perfit_optin', function($_id) {
   // Contents of this function will execute when the blogger
  // uses the [shortcode_name] shortcode.
    $tpl = file_get_contents(dirname(__FILE__).'/tpl/shortcode.php');

    if (is_array($_id))
        $_id = current($_id);

    list($optin, $_mode) = explode('-', $_id);

    list($account, $id) = explode(':', $optin);

    $mode = '';
    if ($_mode) {
        list($mode_type, $mode_mode) = explode(':', $_mode);
        
        $mode = ' data-type="'.$mode_type.'" ';

        if ($mode_mode) {
            $mode .= ' data-mode="'.$mode_mode.'" ';
        }
    }

    // $tpl = str_replace(array('%%OPTIN%%','%%ACCOUNT%%'), array($id, $account), $tpl);
    $tpl = str_replace(array('%%OPTIN%%','%%ACCOUNT%%', '%%MODE%%'), array($id, $account, $mode), $tpl);


    echo $tpl;
});


// Create or modify optin
if ($_POST['save'] == 1) {

// echo '<pre>'.print_r($_POST['data'], true).'</pre>';
// die('a');
    // Load params

    if ($_POST['redirect-bool'] == 0) {
        $_POST['data']['form']['redirect'] = '';
    }

    $fields = $_POST['data']['form']['fields'];
    unset($_POST['data']['form']['fields']);
    foreach ($fields as $k => $v) {
        if ($v['id'])
            $_POST['data']['form']['fields'][] = $v;
    }

    $interests = $_POST['data']['form']['interests'];
    unset($_POST['data']['form']['interests']);
    if ($interests) {
        foreach ($interests as $k => $v) {
            if ($v['id'])
                $_POST['data']['form']['interests'][] = $v;
        }
    }
    $perfit->optins->params($_POST['data']);

    // Execute corresponding method
    $response = (isset($_POST['id']) && !empty($_POST['id']))? $perfit->id($_POST['id'])->put() : $perfit->post();

    if (!$response->success) {
        header("Content-type: application/json; charset=utf-8"); 
        die(json_encode($response->error));
    }

    unset($_GET['id']);

    die('true');
    // header("Location: options-general.php?page=perfit_optin");
}

function perfit_admin() {
    global $perfitConfig;

    include('optin_admin.php');
}

function perfit_list() {
    global $perfitConfig, $perfit;

    if ($_GET['delete']) {
        $perfit->optins->id($_GET['delete'])->delete();
        header("Location: options-general.php?page=perfit_optin");
    }

    if ($_GET['id']) {
        $id = $_GET['id'];
        include('optin_admin.php');
    }
    else if ($_GET['action'] == 'new') {
        $id = $_GET['id'];
        include('optin_admin.php');
    }
    else {
        include('optin_list.php');
    }
}

/**
 * Load required scripts
 */
function load_perfit_scripts() {

    wp_enqueue_script(
        'perfit-bootstrap-js',
        plugins_url( 'js/bootstrap.min.js', __FILE__),
        array( 'jquery' )
    );

    wp_enqueue_script(
        'perfit-app-js',
        plugins_url( 'js/app.js', __FILE__),
        array( 'jquery' )
    );
}

function perfit_admin_actions() {

    $favicon = plugin_dir_url( __FILE__ ) . '/js/logo.png';
    add_menu_page("Perfit Optin", "Perfit Optin", 9, 'perfit_optin', "perfit_list",$favicon);
    // add_submenu_page('social-engage', 'Optin', 'Optin', 9, 'social-engage', array('PerfitOptin', 'se_mainpage'));

    //add_options_page("Perfit Optin", "Perfit Optin", 1, "perfit_optin", "perfit_list");
    // add_action('wp_dashboard_setup', 'add_perfit_optin_widget');
    // do_action('wp_dashboard_setup');

// die('aa');
}

add_action('admin_menu', 'perfit_admin_actions');

/*******************************************/
/************* Widget section **************/
/*******************************************/

class perfit_optin_widget extends WP_Widget {

    // constructor
    function perfit_optin_widget() {
        global $perfit;
        $this->perfit = $perfit;

        load_plugin_textdomain( 'perfit' );
        
        parent::__construct(
            'perfit_widget',
            __( 'Perfit Optin Widget' , 'perfit'),
            array( 'description' => __( 'Formulario para que tus usuarios se registren en tu base de Perfit' , 'perfit') )
        );

    }

    // widget form creation
    function form($instance) {

        global $error;

        $optinModes = array (
            'inline:' => 'Widget',
            'button:' => 'Botón',
            'popup:once' => 'Pop-Up (mostrar una vez)',
            'popup:always' => 'Pop-Up (mostrar hasta lograr subscripción)',
        );

        $optin_id = ($instance)? esc_attr($instance['optin_id']) : 0;

        $optin_mode = ($instance)? esc_attr($instance['optin_mode']) : reset($optinModes);

// echo '<pre>'.print_r($instance, true).'</pre>';

        if (!$this->perfit->token()) {

            include(dirname(__FILE__).'/tpl/widget_login.php');
            unset($_SESSION['error']);
        }
        else {
            // Load optins
            $optins = $this->perfit->optins->limit(1000)->get();

            // Save optin list for WYSIWYG plugin
            if (!$optins->error) {

                delete_option("optin_list");
                add_option("optin_list", serialize($optins));

            }

            include(dirname(__FILE__).'/tpl/widget.php');
        }

    }

    // widget update
    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance['optin_id'] = strip_tags($new_instance['optin_id']);
        $instance['optin_mode'] = strip_tags($new_instance['optin_mode']);
        return $instance;
    }

    // widget display
    function widget($args, $instance) {

        $title = apply_filters('widget_title', $instance['title']);

        echo $args['before_widget'];
        if (!empty($instance['optin_id'])) {

            list($account, $id) = explode(':', $instance['optin_id']);
            list($mode_type, $mode_mode) = explode(':', $instance['optin_mode']);

            $mode = ' data-type="'.$mode_type.'" ';

            if ($mode_mode) {
                $mode .= ' data-mode="'.$mode_mode.'" ';
            }

            $tpl = file_get_contents(dirname(__FILE__).'/tpl/shortcode.php');
            $tpl = str_replace(array('%%OPTIN%%','%%ACCOUNT%%', '%%MODE%%'), array($id, $account, $mode), $tpl);
            // $tpl = str_replace(array('%%OPTIN%%'), array($instance['optin_id']), $tpl);

            echo $tpl;
        }
        echo $args['after_widget'];
// die('a');
    }
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("perfit_optin_widget");'));

/***************************************************/
/************** MCE Editor button ******************/
/***************************************************/


foreach ( array('post.php','post-new.php') as $hook ) {
     add_action( "admin_head-$hook", 'my_admin_head' );
}
/**
 * Localize Script
 */
function my_admin_head() {
    $plugin_url = plugins_url( '/', __FILE__ );

    $optins = json_encode(unserialize(get_option("optin_list")));

    echo "<!-- TinyMCE Shortcode Plugin --><script type='text/javascript'>var perfitConfig = {'url': '".$plugin_url."'};</script><!-- TinyMCE Shortcode Plugin -->";
    // echo "<!-- TinyMCE Shortcode Plugin --><script type='text/javascript'>var perfitConfig = {'url': '".$plugin_url."','optins': '".$optins."'};</script><!-- TinyMCE Shortcode Plugin -->";
}


add_action( 'init', 'perfit_buttons' );
function perfit_buttons() {
    add_filter( "mce_external_plugins", "perfit_add_buttons" );
    add_filter( 'mce_buttons', 'perfit_register_buttons' );
}
function perfit_add_buttons( $plugin_array ) {
    $plugin_array['perfit'] = plugins_url('/js/tinymce-plugin.js',__file__);
    return $plugin_array;
}
function perfit_register_buttons( $buttons ) {
    array_push( $buttons, 'perfit_optin'); // dropcap', 'recentposts
    return $buttons;
}
