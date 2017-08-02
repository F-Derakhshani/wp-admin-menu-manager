<?php
// var_dump($_POST);
// Checks save status
$is_autosave = wp_is_post_autosave($postID);
$is_revision = wp_is_post_revision($postID);
$is_valid_nonce = (isset($_POST['amm_nonce']) && wp_verify_nonce($_POST['amm_nonce'], $this->optionsSlug)) ? true : false;

// Exits script depending on save status
if ($post->post_type !== $this->postType || $is_autosave || $is_revision || !$is_valid_nonce ) {
  return;
}

// Submenus
if (isset($_POST['submenu'])) {

  // Reorder submenu post
  $postSubmenus = array_map('array_values', $_POST['submenu']);
  
  // Save Active Menus
  $this->saveMeta($postID, 'submenu', $postSubmenus);

}

// Checks for input and sanitizes/saves if needed
if (isset($_POST['menu'])) {
  
  // Map the arrays
  $_POST['menu'] = array_map(array($this, 'separator'), $_POST['menu']);
  
//  var_dump($_POST['menu']);
//  die();

  // Set Post Globaly
  $this->postMenu = $_POST['menu'];

  // CUTTING OF DISABLED ONES
  $whereToCut = array_search("amm-separator", array_keys($this->postMenu));

  // Split
  $disabled = array_slice($this->postMenu, $whereToCut);
  // Makes final menu minus disabled
  unset($this->postMenu['amm-separator']);

  // Take amm-separator off
  array_shift($disabled);

  // Save Active Menus
  $this->saveMeta($postID, 'menu', $this->postMenu);
  // Save Disabled Menus
  $this->saveMeta($postID, 'disabled', $disabled);
}

// 3.0.5 Handles what ACF used to handle

// Roles
if (isset($_POST['roles'])) {
  $this->saveMeta($postID, 'roles', $_POST['roles'], false);
} else {
  $this->saveMeta($postID, 'roles', array(), false);
}

// Users
if (isset($_POST['apply_to'])) {
  $this->saveMeta($postID, 'apply_to', $_POST['apply_to'], false);
} else {
  $this->saveMeta($postID, 'apply_to', array(), false);
}

// Activated
if (isset($_POST['activated'])) {
  $this->saveMeta($postID, 'activated', true, false);
} else {
  $this->saveMeta($postID, 'activated', false, false);
}