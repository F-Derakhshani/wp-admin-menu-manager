<?php

// Prevents class redeclaration
if (!class_exists('ParadoxPluginWPAMM')) :

/**
 * Loads our incredibily awesome Paradox Framework, which we are going to use a lot.
 */
// require 'vendor/autoload.php';

/**
 * Our plugin starts here
 *
 * MaterialAdmin is a WordPress plugin that completly transforms your WordPress admin interface, giving it a 
 * awesome and beautful Google Material Design interface.
 */
class ParadoxPluginWPAMM {

  /**
   * @property object $instance Our instance that allow us to only instantiate this class once.
   */
  public static $instance;
  
  /**
   * @property string $textDomain Our plugin textdomain, used across the app.
   */
  public $textDomain;
  
  /**
   * @property string $id Unique indentifier of our plugin. This is used to save options and generate admin views.
   */
  public $id;
  
  /**
   * @property string $file Contiain the path to main plugin file.
   */
  public $file;
  
  /**
   * @property string $metadataURL URL of the updates.
   */
  public $metadataURL;
  
  /**
   * @property string $fullSlug Dir name.
   */
  public $fullSlug;
  
  /**
   * @property string $path The plugin absolute path.
   */
  public $path;
  
  /**
   * @property string $url The plugin URL.
   */
  public $url;
  
  /**
   * @property bool $debug Either or not to display debuuging information and or menus.
   */
  public $debug = false;
  
  /**
   * @property object $adminController The instance of our SASS compiler.
   */
  public $sass;
  
  /** 
   * @property array $pluginPages Array of the plugins page slugs to add branding.
   */
  public $pluginPages = array();
  
  /** 
   * @property bool $whitelabel Wheater or not to display branding options.
   */
  public $whitelabel = false;
  
  /** 
   * @property bool $expandHeader Where to expand the header.
   */
  public $expandHeader = false;
  
  /** 
   * @property bool $expandHeader Where to expand the header.
   */
  public $aboutPage = false;
  
  /**
   * Initializes the plugin adding all important hooks and generating important instances of our framework.
   */
  public function __construct() {

    // Set PATH and URL
    $this->path = plugin_dir_path($this->file);
    $this->url  = plugin_dir_url($this->file);

    // Load text domain
    load_plugin_textdomain($this->textDomain, false, $this->path.'/lang');

    // Instantiate our SASS manager
    // $this->sass = new scssc();
    
    // Adds Backend scripts and styles
    add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
    add_action('admin_enqueue_scripts', array($this, 'enqueueAdminStyles'));
    
    // Adds Frontend scripts and styles
    add_action('wp_enqueue_scripts', array($this, 'enqueueFrontendScripts'));
    add_action('wp_enqueue_scripts', array($this, 'enqueueFrontendStyles'));
    
    // Adds Login scripts ans styles
    add_action('login_enqueue_scripts', array($this, 'enqueueLoginScripts'));
    add_action('login_enqueue_scripts', array($this, 'enqueueLoginStyles'));
    
    // Adds our custom Admin pages hook
    add_action('admin_menu', array(&$this, 'adminPages'));
    
    // Adds Branding
    add_action('init', array($this, 'branding'));
    
    // Run our plugin as soon as possible
    add_action('init', array(&$this, 'Plugin'), 0);
    
    // Adds our custom save hooks
    add_action('acf/save_post', array($this, 'onSave'), 20);
    
    // Check for whitelabel option
    add_action('init', array($this, 'checkWhiteLabel'), 9);
    
    // Check for updates
    $this->autoUpdates();
  }
  
  /**
   * UTILITY BELT
   * This is one of the single most important parts of the framework, a utility belt that makes much easier to 
   * get assets, paths, urls, add Advanced Custom Fields and etc.
   */
  
  /**
   * Return absolute path to some plugin subdirectory
   * @return string Absolute path
   */
  public function path($dir) {
    return $this->path.$dir;
  }
  
  /**
   * Return url to some plugin subdirectory
   * @return string Url to passed path
   */
  public function url($dir) {
    return $this->url.$dir;
  }
  
  /**
   * Return full URL relative to some file in assets
   * @return string Full URL to path
   */
  public function getAsset($asset, $assetsDir = 'img') {
    return $this->url("assets/$assetsDir/$asset");
  }
  
  /**
   * Render Views
   * @param string $view View to be rendered.
   * @param Array $vars Variables to be made available on the view escope, via extract().
   */
  public function render($view, $vars = false) {
    // Make passed variables available
    if (is_array($vars)) extract($vars);
    // Load our view
    include $this->path("views/$view.php");
  }
  
  /**
   * Compile SASS code
   * @param string $sass SASS to be compiled
   * @return string Compiled CSS
   */
  public function compileSass($sass) {
    return $this->sass->compile($sass);
  }
  
  /**
   * Add ACF if need as a dependencie
   */
  public function addACF() {
    
    // Change Path
    add_filter('acf/settings/path', array($this, 'acfPath'));
    
    // Change Dir
    add_filter('acf/settings/dir', array($this, 'acfDir'));
    
    // Hide UI, if debug is off
    if ($this->debug === false) add_filter('acf/settings/show_admin', '__return_false');
    
    // Load Plugin Core
    include_once $this->path('paradox/inc/acf/acf.php');
    
    // Remove NAG
    // add_filter('site_transient_update_plugins', array($this, 'removeACFNag'), 11);
    
  }
  
  /**
   * Change ACF Path
   */
  public function acfPath() {
    return $this->path('paradox/inc/acf/');
  }
  
  /**
   * Change ACF Dir
   */
  public function acfDir() {
    return $this->url('paradox/inc/acf/');
  }
  
  /**
   * Stop Displaying the NAG.
   */
  public function removeACFNag($value) {
    // remove the plugin from the response so that it is not reported
	unset($value->response[$this->fullSlug.'/paradox/inc/acf/acf.php']);
	return $value;
  }
  
  /**
   * Wrapper method to the ACF get field function
   * @param string $field The name of the field.
   * @param bool $display Either or not to display the content.
   */
  public function getField($field, $display = false) {
    return $display ? the_field($field, 'option') : get_field($field, 'option');
  }
  
  /**
   * Wrapper method to the ACF update field function
   * @param string $field The name of the field.
   * @param bool $value The new value of the field.
   */
  public function updateField($field, $value) {
    return update_field($field, $value, 'option');
  }
  
  /**
   * BRANDING AND STYLES
   * This section bellow adds the functions to whitelabel and brading the plugin.
   */
  
  /**
   * Used to get info directly retrieved from the plugin header
   */
  public function get_plugin_info($info) {
    $plugin_info = get_plugin_data($this->file);
    return $plugin_info[$info];
  }
  
  /**
   * Adds commom about pages
   */
  public function addAboutPage() {
    // Adds admin page
    $aboutPage = add_submenu_page($this->slug, __('About', $this->textDomain), __('About', $this->textDomain), 'manage_options', $this->slug.'-about', array($this, 'renderAboutPage'));
    $this->aboutPage     = $aboutPage;
    $this->pluginPages[] = $aboutPage;
  }

  /**
   * Adds commom about pages
   */
  public function renderAboutPage() {
    $this->render('branding/about');
  }

  /**
   * Create the plugins custom Footer
   */
  public function createFooterMenu() {
    // Menu carrier
    $footerMenu = array(
      // link => name
      $this->get_plugin_info('Name') . ' ' . $this->get_plugin_info('Version'),
      $this->get_plugin_info('PluginURI') => __('Get Support', $this->textDomain),
    );

    // Apply filters
    $this->footerMenu = apply_filters('add_footer_menu_732', $footerMenu);
  }

  /**
   * Load the plugins custom Header
   */
  public function addHeader() {
    $this->render('branding/header');
  }

  /**
   * Load the plugins custom Footer
   */
  public function addFooter() {
    $this->render('branding/footer');
  }

  /**
   * Add our custom classes to the admin body tag
   */
  public function addAdminBodyClasses($classes) {
    return "$classes plugin-page-732 plugin-{$this->slug}-732";
  }

  /**
   * Add classes and branding on custom post type pages
   */
  public function addBrandingPostTypePages() {
    // Check fot our post type
    $screen = get_current_screen();
    if ($screen->post_type === $this->postType) {
      // Add Classes to the admin body
      add_filter('admin_body_class', array($this, 'addAdminBodyClasses'));
      add_action("load-edit.php", array($this, 'addBranding'));
    }
  }

  /**
   * Expand Header
   */
  public function expandHeader() {
    $this->expandHeader = true;
  }

  /**
   * Adds Header, Footer and contextual Help when needed
   */
  public function addBranding() {

    // Body Classes
    add_filter('admin_body_class', array($this, 'addAdminBodyClasses'));

    // Fix WPAMM Whitelabeling bug
    if (!$this->whitelabel) {
      
      // Add Header
      add_action('admin_notices', array($this, 'addHeader'));

      // Add Footer
      add_action('in_admin_footer', array($this, 'addFooter'));
      
    } // end if;

    // Mount our tab contents
    ob_start();
    $this->render('branding/tab-support');
    $tabSupport = ob_get_contents();
    ob_end_clean();

    ob_start();
    $this->render('branding/tab-rate');
    $tabRate = ob_get_contents();
    ob_end_clean();

    // Get our current screen to check for our slug
    $screen = get_current_screen();

    // Add my_help_tab if current screen is My Admin Page
    $screen->add_help_tab(array(
      'id'      => 'get-support',
      'title'   => __('Get Support', $this->textDomain),
      'content' => $tabSupport,
    ));

    $screen->add_help_tab(array(
      'id'      => 'rate-our-plugin',
      'title'   => __('Rate our Plugin', $this->textDomain),
      'content' => $tabRate,
    ));
  }

  /**
   * Decides when to load header, footer and help
   */
  public function getBranding() {

    // Adds our custom header and footer
    foreach ($this->pluginPages as $pageSlug) {
      add_action("load-{$pageSlug}", array($this, 'addBranding'));
    }

    // Expand header in case of the about page
    add_action("load-{$this->aboutPage}", array($this, 'expandHeader'));

    // Adds in case of custom post type
    if (property_exists($this, 'postType')) {
      // adds aditional hooks when a custom post type exists
      add_action("current_screen", array($this, 'addBrandingPostTypePages'));
    }

  }

  /**
   * Dev Hooks
   */
  public function devHooks() {
    // Run filter that may set whitelable to true
    $this->whitelabel = apply_filters("{$this->slug}/settings/whitelabel", $this->whitelabel);
  }

  /**
   * Loads header and etc
   */
  public function branding() {
    
    // Add Class either way
    //add_filter('admin_body_class', array($this, 'addAdminBodyClasses'));
    
    if (!$this->whitelabel) {
      // Adds About Page
      add_action('admin_menu', array($this, 'addAboutPage'));
    }
    
    // Adds Header, footer and Help
    add_action('admin_menu', array($this, 'getBranding'), 999999);
    
  }
  
  /**
   * AUTOUPDATE 
   * This section handles our autoupdates and buyer checking.
   */
  
  /**
   * Check Purchase
   */
  public function checkBuyer() {
    
    // This buyer is already checked
    $isChecked = get_option('transient_test_update_saver_'.$this->slug);
         
    // Check if user has a purchase code
    // $purchaseCode = $this->getField($this->slug.'_purchase_code');
    $purchaseCode = get_option('options_wpamm_purchase_code');
    
    // Check if this buyer is already chcked
    if (!empty($purchaseCode)) {
      
      // Check if we already validated his purchase code
      $isValid = $this->validatePurchaseCode($purchaseCode);
      
      // Save new check
      update_option('transient_test_update_saver_'.$this->slug, $isValid);
      
    }
  
  }
  
  /**
   * Validate buyer purchase code
   */
  public function validatePurchaseCode($purchaseCode) {
    // Your Username
    $username = '732';
     
    // Set API Key  
    $APIKey = 'qdwhqspw1gezf5r1vtvw3t4k1rvuymqa';
     
    // Open cURL channel
    $ch = curl_init();
      
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, "http://marketplace.envato.com/api/edge/". $username ."/". $APIKey ."/verify-purchase:". $purchaseCode .".json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
      
    // Decode returned JSON
    $output = json_decode(curl_exec($ch), true);
     
    // Close Channel
    curl_close($ch);
      
    // Return output
    return isset($output['verify-purchase']['buyer']);
  }
  
  /**
   * Install AutoUpdates
   */
  public function autoUpdates() {
    
    // This buyer is already checked
    $isChecked = get_option('transient_test_update_saver_'.$this->slug);
    
    // Check if it's checked
    if ($isChecked) {
      
      // Requiring library
      require $this->path('/paradox/inc/updater/plugin-update-checker.php');
      
      // Instantiating it
      $updateChecker = PucFactory::buildUpdateChecker(
        $this->metadataURL, //Metadata URL.
        $this->file,        //Full path to the main plugin file.
        $this->fullSlug     //Plugin slug. Usually it's the same as the name of the directory.
      );
      
    } // End IF.
    
  }
  
  /**
   * Place code that will be run on first activation
   */
  public function onActivation() {}
  
  /**
   * After ACF saves
   * @param mixed $post_id The post being save or, in our case, the option.
   */
  public function onSave($post_id) {
    if ($post_id === 'options') {
      
      // CheckBuyer
      // $this->checkBuyer();
      
    }
  }
  
  /**
   * Check if our user checked the whitelabel option
   * @param mixed $post_id The post being save or, in our case, the option.
   */
  public function checkWhiteLabel() {
      // Get the information
      // $wl = $this->getField($this->slug.'_whitelabel');
      $wl = get_option('options_wpamm_whitelabel');
      
      // Update the value
      $this->whitelabel = $wl;
  }
  
  /**
   * SCRIPTS AND STYLES
   * The section bellow handles the adding of scripts and css files to the different hooks WordPress offers
   * such as Admin, Frontend and Login. Calling anyone of these hooks on the child class you automaticaly 
   * add the scripts hooked to the respective hook.
   */
  
  /**
   * Enqueue and register Admin JavaScript files here.
   */
  public function enqueueAdminScripts() {}
  
  /**
   * Enqueue and register Admin CSS files here.
   */
  public function enqueueAdminStyles() {}
  
  /**
   * Enqueue and register Frontend JavaScript files here.
   */
  public function enqueueFrontendScripts() {}
  
  /**
   * Enqueue and register Frontend CSS files here.
   */
  public function enqueueFrontendStyles() {}
  
  /**
   * Enqueue and register Login JavaScript files here.
   */
  public function enqueueLoginScripts() {}
  
  /**
   * Enqueue and register Login CSS files here.
   */
  public function enqueueLoginStyles() {}
  
  /**
   * IMPORTANT METHODS
   * Set bellow are the must important methods of this framework. Without them, none would work.
   */
  
  /**
   * Here is where we create and manage our admin pages
   */
  public function adminPages() {}
  
  /**
   * Place code for your plugin's functionality here.
   */
  public function Plugin() {}

}

endif;