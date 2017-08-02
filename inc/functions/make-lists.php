<?php

$addIcon = true;
// var_dump($menuID);
// Get our Menu Backup & settings
$menu      = $this->menu;
$available = $this->getMenu($menuID);
$disabled  = $this->getDisabled($menuID);

// Hack for Visual Composer
$removeVC = false;

// var_dump($available);
// var_dump($disabled);
// var_dump($menu);

// Final menu
$lists = array('available' => array(), 'disabled' => array());

// Make Disabled List
if (!empty($disabled)) :
  // Loop
  foreach ($disabled as $item) :

    // Escape if does not have an id
    if (!isset($item['id'])) continue;

    // Get our Menu
    // 3.0.4 Fix
    $rightMenu = getRightMenuItem($item['id'], $menu);
    // var_dump($item);
    // var_dump($rightMenu);

    // Visual Composer Hack
    // @since 3.0.11
    if ($item['id'] == "vc-general") {
      $removeVC = true;
    }

    // Checks if menu original exists
    if ($rightMenu !== false) :

    /**
     * We check for edit-post items so we can removed them from the new menu
     * @since 3.0.8
     */
    $this->checkRemoveNew($rightMenu[2]);
    // end hack;

    $rightMenu['separator'] = false;
    $rightMenu['rename']    = '';
    $rightMenu['icon']      = '';

    // Separator Exception
    if (isset($rightMenu[0]) && $rightMenu[0] === '') {
      $rightMenu[0]           = __('Separator', $this->textDomain);
      $rightMenu[6]           = 'dashicons-editor-insertmore';
      $rightMenu['separator'] = true;
      $rightMenu['looped']    = true;
    }

    // Check for impossible dashicons
    if ($addIcon) {
      $rightMenu = $this->handleIcon( $rightMenu );
    }

    $lists['disabled'][] = $item + $rightMenu;

    // Unset this disabled from menu
    // 3.0.4 Fix: Replace the menu
    $itemIndex = getItemIndex($item['id'], $menu);
//    if (isset($rightMenu['separator']) && $rightMenu['separator']) {$menu[$itemIndex] = $rightMenu;}
//    else {unset($menu[$itemIndex]);}
    unset($menu[$itemIndex]);

    // End if isset
    endif;

  endforeach;
  // End Loop

endif;

  $menuIndexes = 0;
//  var_dump($menu);

  // Loop
  if ($available) :
    foreach ($available as $item) :

      // Escape if does not have an id
      if (!isset($item['id'])) continue;

      // Visual Composer Hack
      // @since 3.0.11
      // var_dump($item);
      if ($item['id'] == 'vc-general' && !current_user_can('manage_options')) {
        // var_dump('tem');
        $item['id'] = 'vc-welcome';

        if ($removeVC) {
          // $item['looped'] = true;
          // var_dump($item); die;
          // $item[0] = 'Lolzinho';
          continue;
        }

      } // end if;

      // Get our Menu
      // 3.0.4 Fix
      $rightMenu = getRightMenuItem($item['id'], $menu);

      // Checks if menu original exists
      if ($rightMenu !== false) :

      $rightMenu['separator'] = false;
      $rightMenu['rename']    = '';
      $rightMenu['icon']      = '';

        // Separator Exception
        if ($rightMenu[0] === '') {
          $rightMenu[0]           = __('Separator', $this->textDomain);
          $rightMenu[6]           = 'dashicons-editor-insertmore';
          $rightMenu['separator'] = true;
        }

        // Check for impossible dashicons
        if ($addIcon) {
          $rightMenu = $this->handleIcon( $rightMenu );
        }

        $menuIndexes = $menuIndexes + 10;
        $lists['available'][$menuIndexes] = $item + $rightMenu;

        // Put flag on the base $menu used as reference to flag them as looped
        // so we can check if new items were added to the menu with the installation of new plugins.
        $rightMenu['looped'] = true;

        // 3.0.4 Fix: Replace the menu
        $itemIndex = getItemIndex($item['id'], $menu);
        $menu[$itemIndex] = $rightMenu;

      // Case of our custom added separators
      elseif (isset($item['separator']) && $item['separator'] === true) :
        // var_dump($item);

        // Check if exists
        $rightMenu = getRightMenuItem($item['id'], $this->menu);
  
        if ($rightMenu === false) {
          
          // ID
          $menuIndexes = $menuIndexes + 10;

          // If has no id, adds one
          if (!isset($item['id'])) {
            $item['id'] = 'separator-'.$menuIndexes;
          }

          $item[0]        = __('Separator', $this->textDomain);
          $item[1]        = 'read';
          $item[2]        = $item['id'];
          $item[3]        = '';
          $item[4]        = 'wp-menu-separator';
          $item[6]        = 'dashicons-editor-insertmore';
          //$item['id']     = $item[2];
          $item['rename'] = '';
          $item['icon']   = '';
          $item['looped'] = true;
          $item['order']  = $menuIndexes;

          // Check for impossible dashicons
          if ($addIcon) { $item = $this->handleIcon($item); }

          // Add menus
          $lists['available'][$menuIndexes] = $item;
          
        }
      // END IF
      endif;

    endforeach;
    // End Loop
  endif;

  // Now we have to loop the adiitonal items and plugins that the user
  // can and WILL add after
  foreach ($menu as $order => &$item) :

    // var_dump($menu);
    // var_dump($item);
    // if it was not looped already
    if (!isset($item['looped']) && is_array($item)) {

      // var_dump($item);

      if ($removeVC) {
        // $item['looped'] = true;
        // var_dump($item); die;
        // $item[0] = 'Lolzinho';
        continue;
      }

      // Order Fix 3.0.5
      // $order = $order + 0.1;
      
      $item['rename']    = '';
      $item['icon']      = '';
      $item['order']     = $order;
      $item['id']        = $item[2];
      $item['separator'] = false;

      // Separator Exception
      if ($item[0] === '') {
        $item[0]           = __('Separator', $this->textDomain);
        $item[6]           = 'dashicons-editor-insertmore';
        $item['separator'] = true;
        
        // We need to check if some menu already exists. If it does, we can ignore separators.
        if (!empty($available)) continue;
      }

      // Check for impossible dashicons
      if ($addIcon) {
        $item = $this->handleIcon( $item );
      }

      $item['looped'] = true;
      
      // Add, if not separator
      // We need to check if some menu already exists. If it does, we can ignore separators.
      $lists['available']["$order"] = $item;

    }
  endforeach;

// Add to the global scope
// $this->lists = $lists; // Uncomment
// ksort($lists['available']);
// var_dump($lists['available']);
return $lists;