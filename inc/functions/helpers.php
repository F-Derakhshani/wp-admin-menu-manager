<?php

/**
 * Get our menu index so we can retrive the real menu to use
 */
function getRightMenuItem($id, $haystack) {
  $menu = wpamm_get_admin_menu_section($id, $haystack);
  
  // Check if the result is valid
  if ($menu->index === false) return false;
  
  // Check if something was found
  if (isset($haystack[$menu->index])) return $haystack[$menu->index];
  else return false;
}

/**
 * Get our menu index so we can retrive the real menu to use
 */
function getItemIndex($id, $haystack) {
  $menu = wpamm_get_admin_menu_section($id, $haystack);
  return $menu->index;
}