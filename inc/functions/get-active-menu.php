<?php

// Prevent Save Bugs
if (!isset($GLOBALS['menu'])) return;

// Require our lib
require_once $this->path('inc/functions/wp-menu.php');
require_once $this->path('inc/functions/helpers.php');

// Saves Menu before any changes
$this->menu = $GLOBALS['menu'];

// Add Submenu support
$this->submenu = $GLOBALS['submenu'];

// Get Actual user
$user = wp_get_current_user();

// Get Configs
$args = array(
  'posts_per_page' => -1,
  'post_type'      => $this->postType,
  'post_status'    => 'publish',

  // Optimizing query
  'cache_results' => false,
  'no_found_rows' => true,
  'fields'        => 'ids',
);

// Get the menus
$menus = get_posts($args);

// Loop menus
if (!empty($menus)) :
  foreach ($menus as $menu) :
    $users = $this->getMeta($menu, 'apply_to', false);
    if (!$users) $users = array();

    // Added role support
    $roles = $this->getMeta($menu, 'roles', false);
    $userRole = $this->getCurrentUserRole();

    // If menu exists but is deactivated
    if ($this->getMeta($menu, 'activated', false)) :

      // Check if user is of role
      if (($roles) && array_search($userRole, $roles) !== false) {
        $this->activeMenu = $menu;
      }

      // Removed else is statement so we can have menus applied to specific roles
      // Added in 3.0.8
      if ($users) {
//        foreach ($users as $userToApply) {
//          $found = array_search($user->user_email, $userToApply);
//          if ($found) $this->activeMenu = $menu;
//        }
        
        // Fix on 3.0.5
        if (in_array($user->ID, $users)) $this->activeMenu = $menu;
      }

    // end if activated
    endif;
  endforeach;

// End if
endif;