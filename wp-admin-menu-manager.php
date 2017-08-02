<?php

/*
  Plugin Name: WP Admin Menu Manager
  Plugin URI: http://codecanyon.net/item/wp-admin-menu-manager/9520160
  Description: Edit, rename, reorder and hide WordPress menu and submenu items was never that easy!
  Version: 3.0.12
 */

/**
 * Loads our incredibily awesome Paradox Framework, which we are going to use a lot.
 */
require 'paradox/paradox-plugin.php';

/**
 * Our plugin starts here
 *
 * MaterialAdmin is a WordPress plugin that completly transforms your WordPress admin interface, giving it a 
 * awesome and beautful Google Material Design interface.
 */
class WPAMM extends ParadoxPluginWPAMM {
  
  /** @property string $slug Slug of the Plugin */
  public $slug;
  
  /** @property string $postType Post Type slug, so we can add some things */
  public $postType;
  
  /** @property string $optionsSlug Slug of the optiosn saved in the DB */
  public $optionsSlug;
  
  /** @property string $postMenu This contains the menu already saved */
  private $postMenu;
  
  /** @property string $activeMenu Once the user create some menu setup, it gets loaded here */
  private $activeMenu = false;
  
  /** @property string $menu Backup of the original menu created by WordPress */
  private $menu = array();

  /** @property string $submenu Backup of the original submenu created by WordPress */
  private $submenu = array();
  
  /** @property string $newSubmenu New submenu created by our plugin */
  private $newSubmenu;
  
  /** @property string $lists, Formatted list of the available and disabled items */
  private $lists;
  
  /**
   * Items to remove from admin menu
   */
  public $removeFromNew = array();
  
  /** @const int VERY LATE hook number so we can asure ourselves that this plugin will be the ladt thing added */
  const WPAMM_VERY_LATE = 100000000000;
  
  /**
   * Creates or returns an instance of this class.
   * @return object The instance of this class, to be used.
   */
  public static function init() {
    // If an instance hasn't been created and set to $instance create an instance and set it to $instance.
    if (null == self::$instance) {self::$instance = new self;}
    return self::$instance;
  }
  
  /**
   * Initializes the plugin adding all important hooks and generating important instances of our framework.
   */
  public function __construct() {
    
    // Setup
    $this->id          = 'wpamm';
    $this->textDomain  = 'wpamm';
    $this->file        = __FILE__;
    $this->fullSlug    = 'wp-admin-menu-manager';
    $this->metadataURL = 'http://weare732.com/versions/updates/?action=get_metadata&slug='.$this->fullSlug;
    
    // Aditional Setup
    $this->slug        = 'wpamm';
    $this->postType    = 'amm';
    $this->optionsSlug = 'amm_options_';
    
    $this->debug = true;
    
    // Calling parent construct
    parent::__construct();
    
    // Now we call the Advanced Custom Posts Plugin, that will handle our Options Page
    // $this->addACF();
    
  }
  
  /**
   * Assures that this plugin get loaded first that everyone else
   */
  public function loadFirst() {
    
    // Have we done this before?
    $alreadyReordered = get_option('wpamm_reordered');
    
    // If we already did this, return.
    if ($alreadyReordered === true) return;
    
	// ensure path to this file is via main wp plugin path
	$wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
	$this_plugin = plugin_basename(trim($wp_path_to_this_file));
	$active_plugins = get_option('active_plugins');
	$this_plugin_key = array_search($this_plugin, $active_plugins);
    
	if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
		array_splice($active_plugins, $this_plugin_key, 1);
		$active_plugins[] = $this_plugin;
		update_option('active_plugins', $active_plugins);
	}
    
    // Now we have done it.
    update_option('wpamm_reordered', true);
    
  }
  
  /**
   * Loads our ACF custom fields
   */
  public function acfAddFields() {
//    if (!class_exists('acf_field_role_selector'))
//      require $this->path('inc/acf-role/acf-role_selector-v5.php');
  }
  
  /**
   * Load Our ACF Options
   */
  public function loadAcfOptions() {
//    require $this->path('inc/custom-fields/amm.php');
//    require $this->path('inc/custom-fields/settings.php');
  }

  /**
   * Enqueue and register Admin JavaScript files here.
   */
  public function enqueueAdminScripts() {
    
    $screen = get_current_screen();
    if ($screen->post_type !== 'amm' && $screen->id !== 'manage-menus_page_wpamm-about') return;
    
    // Adds admin Scripts that we need
    // wp_enqueue_script('admin-widgets');
    wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('jquery-ui-sortable');
    
    // Get Version
    $version = $this->get_plugin_info('Version');
    
    // Common and Admin JS
    wp_enqueue_script($this->id.'admin', $this->url('assets/js/scripts.min.js'), array('jquery', 'jquery-ui-droppable', 'jquery-ui-sortable'), $version, true);

    // If Mobile Version, adds Punch
    if (wp_is_mobile()) wp_enqueue_script('jquery-touch-punch');
  }

  /**
   * Enqueue and register Admin CSS files here.
   */
  public function enqueueAdminStyles() {
    // Common and Admin styles
    wp_enqueue_style($this->id.'admin', $this->url('assets/css/main.min.css'));
  }
  
  /**
   * Here is where we create and manage our admin pages
   */
  public function customAdminPages() {

    // for ($i = 0; $i < 1500; $i++) {
    //   // Adds Index Main Page
    // $this->pluginPages[] = add_menu_page(__('WP Admin Menu Manager', $this->textDomain), __('Manage Menus', $this->textDomain), 'remove_users', $this->slug.$i, array($this, 'renderIndexView'), 'dashicons-menu', 10090.09+$i);
    // }
    
    // Adds Index Main Page
    $this->pluginPages[] = add_menu_page(__('WP Admin Menu Manager', $this->textDomain), __('Manage Menus', $this->textDomain), 'remove_users', $this->slug, array($this, 'renderIndexView'), 'dashicons-menu', 10090.09);
    
  }
  
  /**
   * Add out settings
   */
  public function settingsPage() {
    
//    // Adds Settings Page
//    if (function_exists('acf_add_options_sub_page')) {
//      acf_add_options_sub_page(array(
//        'menu_title'  => __('Settings', $this->textDomain),
//        'parent_slug' => $this->slug,
//      ));
//    }
//
//    // Add to branding
//    $this->pluginPages[] = 'manage-menus_page_acf-options-settings';
    
    $settingsPage = add_submenu_page($this->slug, __('Settings', $this->textDomain), __('Settings', $this->textDomain), 'manage_options', $this->slug.'-settings', array($this, 'renderSettingsPage'));
    
    // Add to branding
    $this->pluginPages[] = $settingsPage;
    
  }
  
  /**
   * Adds Settings page
   */
  public function renderSettingsPage() {
    $this->render('settings-page');
  }
  
  /**
   * Adds custom body class, based on our theme
   */
  public function bodyClass($classes) {
    return $classes.$this->id;
  }
  
  
  /**
   * Gets the current user role, used to figure out either to apply or not some menu setup.
   */
  public function getCurrentUserRole() {
    global $current_user;
    wp_get_current_user();
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    return $user_role;
  }

  /**
   * Equivalent of makeLists for menus, only for submenus
   */
  public function values($value) {
    return array_values($value);
  }

  /**
   * Equivalent of makeLists for menus, only for submenus
   */
  public function makeSubmenu($ID) {
    
    // Our saved submenu version
    $ourSubmenu = $this->getSubmenu($ID);
    // var_dump($ourSubmenu);
    
    // The default submenu
    $defaultSubmenu = $this->submenu;
    // var_dump($defaultSubmenu);
    
    // Now we flat the default (ajust the indexes) submenu so we can add them together
    $defaultSubmenu = array_map(array($this, 'values'), $defaultSubmenu);
    
    // Loop default menus
    foreach ($defaultSubmenu as $parent => &$submenus) {
      foreach ($submenus as $index => &$item) {
        
        // Add fields from our save version, with the modifications
        if (isset($ourSubmenu[$parent][$index])) {
          // var_dump($item);
          // var_dump($ourSubmenu[$parent][$index]);
          // We can only truly add if the two are the same type
          if (is_array($item) && is_array($ourSubmenu[$parent][$index])) {
            $item = $item + $ourSubmenu[$parent][$index];
            //var_dump($item);
          }

        }
      }
    }
    
    // var_dump($defaultSubmenu);
    // Return the result
    return $defaultSubmenu;
  }
  
  /**
   * Get Active menu and its options
   */
  public function getActiveMenu() {
    require_once $this->path('inc/functions/get-active-menu.php');
  }
  
  /**
   * Actual Hook on users
   */
  public function changeMenu() {
    require_once $this->path('inc/functions/change-menu.php');
  }
  
  /**
   * Handles Save da Metabox
   */
  public function saveMetaData($postID, $post) {
    require_once $this->path('inc/functions/save-meta-data.php');
  }
  
  /**
   * Rename Menu
   */
  public function renameMenus($menu) {
    if ($menu) :
      // Rename
      foreach ($menu as $menu) :
    
        // Rename Menu item
        if (isset($menu['rename']) && $menu['rename'] !== '') {
          
          // Rename
          rename_admin_menu_section($menu[0], $menu['rename']); 
          
          // Now that we renamed, we need to refresh this menu indentificator
          $menu[0] = $menu['rename'];
          
        }
    
        // Change Icon
        if (isset($menu['icon']) && $menu['icon'] !== '')
          change_icon($menu[0], $menu['icon']);
    
        //var_dump($menu);
    
      endforeach;
    endif;
    return $menu;
  }

  /**
   * Rename, relink and hide submenus
   */
  public function reStuffSubmenus($submenu) {

    if ($submenu) :
      // Rename
      foreach ($submenu as &$block) {
        foreach ($block as $id => &$item) {

          // Rename
          if (isset($item['rename']) && $item['rename'] !== '') {
            $item[0] = $item['rename'];
          }

          // Relink
          if (isset($item['link']) && $item['link'] !== '') {
            $item[2] = $item['link'];
          }

          // Hide
          if (isset($item['hide']) && $item['hide'] == 'on') {
            unset($block[$id]);
            
            /**
             * We check for edit-post items so we can removed them from the new menu
             * @since 3.0.8
             */
            $this->checkRemoveNew($item[2]);
            // end hack;
            
          }

        }
      }
    endif;
    //var_dump($submenu);
    return $submenu;
  }

  /**
   * Get Options Plugin
   */
  public function getMeta($postID, $option, $useSlug = true) {
    //delete_post_meta($postID, $this->optionsSlug.$option);
    $slug = $useSlug ? $this->optionsSlug.$option : $option;
    return get_post_meta($postID, $slug, true);
  }

  public function saveMeta($postID, $option, $value, $useSlug = true) {
    //delete_post_meta($postID, $this->optionsSlug.$option);
    $slug = $useSlug ? $this->optionsSlug.$option : $option;
    return update_post_meta($postID, $slug, $value);
  }

  /**
   * Specifics Gets Meta
   */
  public function getMenu($postID) {
    // var_dump($postID);
    return $this->getMeta($postID, 'menu');
  }

  public function getDisabled($postID) {
    return $this->getMeta($postID, 'disabled');
  }

  public function getSubmenu($postID) {
    return $this->getMeta($postID, 'submenu');
  }

  // To end, order the arrays
  public function reOrder($a, $b) {
    return $a["order"] - $b["order"];
  }

  // Check if has submenu
  public function hasItemSubmenus($id) {
    return isset($this->newSubmenu[$id]);
  }

  // item submenu
  public function getItemSubmenus($id) {
    if (isset($this->newSubmenu[$id])) {
      // Set Submenu
      $submenu  = $this->newSubmenu[$id];
      // Include view
      //var_dump($submenu);
      $newOrder = 0;
      foreach ($submenu as $order => $submenu) {

        // Add Aditional offsets
        if (!isset($submenu['rename'])) $submenu['rename'] = '';
        if (!isset($submenu['link'])) $submenu['link'] = '';
        if (!isset($submenu['hide'])) $submenu['hide'] = '';

        include $this->path('views/submenu-item.php');
        $newOrder++;
      }
    } else return false;
  }

  /**
   * Dashicons handler for especial icons, like woocommerce and so on
   * Also removing impossible icons
   */
  function handleIcon($item) {

    // Return if custom icon
    if (!isset($item[6])) return $item;
    
    // Replace it for something we can effectivily display
    if (empty($item[6]) || $item[6] == ' ' || $item[6] == 'div') {
      $item[6] = 'dashicons-minus';
    }
    
    // return item object
    return $item;
    
  }
  
  /**
   * Effectivelly make the menu list that iis used everywhere in the plugin
   */
  public function makeLists($menuID, $addIcon = true) {
    return include $this->path('inc/functions/make-lists.php');
  }

  /**
   * Get Submenus
   */
  public function getSubMenus($parent) {
    $submenus = $GLOBALS['submenu'];
  }

  /**
   * Make available Menus List
   */
  public function makeAvailableList($menuID, $type = 'available') {

    // Get Menu
    $lists = $this->makeLists($menuID);
    $available = $lists['available'];
    //var_dump($available);

    // Make Loop
    $newOrder = 0;

    foreach ($available as $menu) {
      // Get Template
      $menuID = $menu['order'];
      include $this->path('views/menu-item.php');
      // Increase NewOrder
      $newOrder++;
    }
  }

  /**
   * Make available Menus List
   */
  public function makeDisabledList($menuID) {

    // Get Menu
    $lists = $this->makeLists($menuID);
    // var_dump($lists);
    $available = $lists['available'];
    $disabled = $lists['disabled'];
    // var_dump($disabled);

    // Make Loop
    $newOrder = count($available);

    foreach ($disabled as $menu) {
      // Get Template
      $menuID = $menu['order'];
      include $this->path('views/menu-item.php');
      // Increase NewOrder
      $newOrder++;
    }
  }
  
  /**
   * Adds the extra tables to the Menu Setup list
   */
  public function addCustomColumns() {
    // Adds Exporter Action
    add_action('load-edit.php', array($this, 'addExporterAction'));
    
    // Custom Columns pra fields
    add_action("manage_{$this->postType}_posts_custom_column", array($this, 'wpammColumnsValues'));
    add_filter("manage_edit-{$this->postType}_columns", array($this, 'wpammColumns'));
    add_filter("manage_edit-{$this->postType}_sortable_columns", array($this, 'wpammAddSortableColumns'));
    add_filter("post_row_actions", array($this, 'addExportLink'), 10, 2);
  }
  
  /**
   * Created our custom columns
   */
  function wpammColumns($columns) {
	$columns = array(
		'cb'	 	=> '<input type="checkbox" />',
		'title' 	=> __('Title', $this->textDomain),
		'roles' 	=> __('Roles under Effect', $this->textDomain),
		'users'	    => __('Users under Effect', $this->textDomain),
        'activated'	=> __('Activated?', $this->textDomain),
		'date'		=> __('Date', $this->textDomain),
	);
	return $columns;
  }

  /**
   * Display the custom values of our custom columns
   */
  public function wpammColumnsValues($column) {
	global $post;
  
    /**
     * Display Roles under effect
     */
	if ($column == 'roles') {
      
      // Display roles
      $rolesList = "";
      
      // Make list of roles under effect
      $roles = $this->getMeta($post->ID, 'roles', false);
      
      // Null or false
      if (!$roles) _e('No roles under effect.', $this->textDomain);
      
      // add role
      else {
        foreach($roles as $role) {
          $role = ucfirst($role);
          $rolesList .= (empty($rolesList)) ? $role : ", $role";
        }
      }
      
      // Display list
      echo $rolesList;
	}
    
    else if ($column == 'users') {
      
      // Display users
      $userList = "";
      
      // Make list of users under effect
      $users = $this->getMeta($post->ID, 'apply_to', false);
      //var_dump($users);
      
      // Null or false
      if (!$users) _e('No <strong>specific</strong> users under effect.');
      
      // add role
      else {
        foreach($users as $user) {
          //var_dump($user);
          $user = get_user_by('id', (int) $user);
          $user = $user->display_name;
          $userList .= (empty($userList)) ? $user : ", $user";
        }
      }
      
      // Display list
      echo $userList;
      
	}
    
    else if ($column == 'activated') {
      // Make list of roles under effect
      $activated = $this->getMeta($post->ID, 'activated', false);
      if ($activated) _e('Yes', $this->textDomain);
      else            _e('No', $this->textDomain);
	}
    
  }
  
  /**
   * Adds activated column to the sortable ones
   */
  function wpammAddSortableColumns($columns) {
	$columns['activated'] = 'activated';
	return $columns;
  }
  
  /**
   * Adds out custom export link
   */
  function addExportLink($actions, $post) {
    // Add Export button if is amm
    if ($post->post_type == $this->postType) {
      // Vars
      $label = __('Export', $this->textDomain);
      $link  = admin_url("edit.php?post_type={$this->postType}&{$this->slug}_export={$post->ID}");
      
      // Adds our link
      $actions['export'] = "<a href='{$link}'>{$label}</a>"; 
    }
    
    // Return
	return $actions;
  }
  
  /**
   * Export the only menu passind its ID.
   */
  public function addExporterAction() {
    // Check if request exists
    if (isset($_GET["{$this->slug}_export"])) {
      require $this->path('inc/functions/export.php');
      // Run function and pass the ID
      wpamm_export_wp($_GET["{$this->slug}_export"]);
      exit;
    }
  }
  
  /**
   * Register AMM custom post Type
   */
  public function addPostType() {

    $labels = array(
      'name'               => _x( 'Custom Admin Menus', 'post type general name', $this->textDomain),
      'singular_name'      => _x( 'Custom Admin Menu', 'post type singular name', $this->textDomain),
      'menu_name'          => _x( 'Menus', 'admin menu', $this->textDomain),
      'name_admin_bar'     => _x( 'Menu', 'add new on admin bar', $this->textDomain),
      'add_new'            => _x( 'Create new Menu Setup', 'book', $this->textDomain),
      'add_new_item'       => __( 'Create new Menu Setup', $this->textDomain),
      'new_item'           => __( 'New Menu Setup', $this->textDomain),
      'edit_item'          => __( 'Edit Menu Setup', $this->textDomain),
      'view_item'          => __( 'View Menu Setups', $this->textDomain),
      'all_items'          => __( 'Menu Setups', $this->textDomain),
      'search_items'       => __( 'Search Menu Setups', $this->textDomain),
      'parent_item_colon'  => __( 'Parent Menu Setups:', $this->textDomain),
      'not_found'          => __( 'No menu setups found.', $this->textDomain),
      'not_found_in_trash' => __( 'No menu setups found in Trash.', $this->textDomain)
    );

    $args = array(
      'labels'             => $labels,
      'public'             => false,
      'publicly_queryable' => false,
      'show_ui'            => true,
      'show_in_menu'       => $this->slug,
      'query_var'          => true,
      'rewrite'            => array('slug' => 'amm'),
      'capability_type'    => 'post',
      'has_archive'        => true,
      'hierarchical'       => false,
      'menu_position'      => null,
      'supports'           => array('title', 'amm_metabox')
    );

    // Actually adds the custom post type
    register_post_type($this->postType, $args);
  }
  
  /**
   * Loads Metaboxes
   */
  public function addAMMMetaboxes() {
    
    // Target Selector Metabox
    add_meta_box('amm_target', __('Custom Menu Setup', $this->textDomain), array($this, 'renderAMMTargetMetabox'), $this->postType, 'normal', 'low');
    
    // Activate Metabox
    add_meta_box('amm_activated', __('Activated', $this->textDomain), array($this, 'renderAMMActivatedMetabox'), $this->postType, 'side', 'low');
    
    // Menu Drag and Drop Interface
    add_meta_box('amm_metabox', __('Manage Menus', $this->textDomain), array($this, 'renderAMMMetabox'), $this->postType, 'normal', 'low');
    
  }
  
  /*
   * Render Metabox Target
   */
  public function renderAMMTargetMetabox($post) {
    // Include our view
    include $this->path('views/target-metabox.php');
  }
  
  /*
   * Render Activated Metabox
   */
  public function renderAMMActivatedMetabox($post) {
    // Include our view
    include $this->path('views/activated-metabox.php');
  }
  
  /*
   * Render Metabox D&D Interface
   */
  public function renderAMMMetabox($post) {
    // Make Lists
    $this->makeLists($post->ID);
    $this->newSubmenu = $this->makeSubmenu($post->ID);
    include $this->path('views/amm-metabox.php');
  }
  
  /**
   * Remove Slider Revolution Metaboxes from our post type
   */
  public function removeSRMetabox() {
    remove_meta_box('mymetabox_revslider_0', $this->postType, 'normal');
  }
  
  /**
   * Separators, add true
   */
  public function separator(&$item) {
    if (isset($item['separator'])) $item['separator'] = true;
    
    // Clean outputs
    
    
    return $item;
  }
  
  /**
   * Saves Settings page setup
   */
  public function saveSettings() {
    // Save Functions
    if (isset($_POST['wpamm-settings-page-submit']) && check_admin_referer('___wpamm_settings') ) :

      // Save Purchase Code
      if (isset($_POST['wpamm_purchase_code'])) {
        update_option('options_wpamm_purchase_code', $_POST['wpamm_purchase_code']);
        
        // CheckBuyer
        $this->checkBuyer();
      }

      // Save Whitelabel
      if (isset($_POST['wpamm_whitelabel'])) {
        update_option('options_wpamm_whitelabel', true);
      } else {
        update_option('options_wpamm_whitelabel', false);
      }

      // We need to redirect the user to this page again
      wp_redirect($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&updated=true');
      exit;

    endif; 
  }
  
  /**
   * Place code for your plugin's functionality here.  
   */
  public function Plugin() {
    
    // Activation order
    add_action("init", array($this, 'saveSettings'));
    
    // Remove Slider Revolution Metabox
    add_action('do_meta_boxes', array($this, 'removeSRMetabox'));
    
    // Activation order
    // add_action("init", array($this, 'loadFirst'));
    
    // adds body class to our admin pages
    add_filter('admin_body_class', array($this, 'bodyClass'));
    
    // Effectvilly changes the final menu
    add_action('admin_init', array($this, 'getActiveMenu'), self::WPAMM_VERY_LATE);
    
    // Effectvilly changes the final menu
    add_action('admin_init', array($this, 'changeMenu'), self::WPAMM_VERY_LATE + 10);
    
    // Check if we need to add our menu
    $canSee = $this->getCurrentUserRole() == 'administrator';
    $canSee = $canSee || $this->getCurrentUserRole() == 'super-admin';
    
    if ($canSee) {
      // Creates our WPAMM Menu
      add_action('admin_menu', array($this, 'customAdminPages'));

      // Adds settings page
      add_action('admin_menu', array($this, 'settingsPage'));
    }
    
    // handles data
    add_action('save_post', array($this, 'saveMetaData'), 9, 2);
    
    // Adds the extra tables to the Menu Setup list
    $this->addCustomColumns();
    
    // Add our custom fields
    // $this->acfAddFields();
    
    // Check if this user has menus
    // $this->loadAcfOptions();
    
    // Adds Custom Post Type
    add_action('init', array($this, 'addPostType'));
    
    // Add Metboxes
    add_action('add_meta_boxes', array($this, 'addAMMMetaboxes'));
    
    // Admin bar fix
    add_action('wp_before_admin_bar_render', array($this, 'fixAdminbar'));

  } // end Plugin;
  
  /**
   * Check for the removal of new items
   * @param string $menuSlug Slug of the menu
   */
  public function checkRemoveNew($menuSlug) {
    
    /**
     * We check for edit-post items so we can removed them from the new menu
     * @since 3.0.8
     */
    
    // Run pregmacth
    preg_match("/edit\.php\?post_type=([a-z]\w+)/", $menuSlug, $matches);
    preg_match("/post-new\.php\?post_type=([a-z]\w+)/", $menuSlug, $matches2);
           
    // If is post
    if ($menuSlug == 'edit.php') {
      $this->removeFromNew[] = 'post';
    }
    
    else if ($menuSlug == 'post-new.php') {
      $this->removeFromNew[] = 'post';
    }

    // if is media
    else if ($menuSlug == 'upload.php') {
      $this->removeFromNew[] = 'media';
    }

    // If matches the pattern
    else if (!empty($matches)) {
      $this->removeFromNew[] = $matches[1];
    }
    
    // If matches the pattern
    else if (!empty($matches2)) {
      $this->removeFromNew[] = $matches2[1];
    }
    
  } // end checkRemoveNew;
  
  /**
   * We need to remove new items from the new menu when the corresponding 
   * post type is removed
   * @since 3.0.8
   */
  public function fixAdminbar() {
    
    // Get global
    global $wp_admin_bar;
    
    // Loop our removed items so we can also removed them from here
    foreach ($this->removeFromNew as $toRemove) {
      
      // Remove the item
      $wp_admin_bar->remove_menu("new-$toRemove");
      
    } // end foreach;
    
  } // end fixAdminbar;
  
}

/**
 * Finally we get to run our plugin.
 */
$WPAMM = WPAMM::init();