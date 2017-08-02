<?php

$addIcon = true;

// Get our Menu Backup & settings
$menu      = $this->menu;
$available = $this->getMenu($menuID);
$disabled  = $this->getDisabled($menuID);

//var_dump($available);
//var_dump($disabled);

// Final menu
$lists = array('available' => array(), 'disabled' => array());

// Make Disabled List
if ($disabled) :
  // Loop
  foreach ($disabled as $item) :

    $menu[$item['order']]['separator'] = false;
    $menu[$item['order']]['rename']    = '';
    $menu[$item['order']]['icon']      = '';

    // Separator Exception
    if (isset($menu[$item['order']][0]) && $menu[$item['order']][0] === '') {
      $menu[$item['order']][0]           = __('Separator', $this->textDomain);
      $menu[$item['order']][6]           = 'dashicons-editor-insertmore';
      $menu[$item['order']]['separator'] = true;
    }

    // Check for impossible dashicons
    if ($addIcon) {
      $menu[$item['order']] = $this->handleIcon( $menu[$item['order']] );
    }

    $lists['disabled'][] = $item + $menu[$item['order']];

    // Unset this disabled from menu
    unset($menu[$item['order']]);

  endforeach;
  // End Loop

endif;

  $menuIndexes = 0;

  // Loop
  if ($available) :
    foreach ($available as $item) :

      // Checks if menu original exists
      if (isset($menu[$item['order']])) :

      $menu[$item['order']]['separator'] = false;
      $menu[$item['order']]['rename']    = '';
      $menu[$item['order']]['icon']      = '';

        // Separator Exception
        if ($menu[$item['order']][0] === '') {
          $menu[$item['order']][0]           = __('Separator', $this->textDomain);
          $menu[$item['order']][6]           = 'dashicons-editor-insertmore';
          $menu[$item['order']]['separator'] = true;
        }

        // Check for impossible dashicons
        if ($addIcon) {
          $menu[$item['order']] = $this->handleIcon( $menu[$item['order']] );
        }

        $menuIndexes = $menuIndexes + 10;
        $lists['available'][$menuIndexes] = $item + $menu[$item['order']];

        // Put flag on the base $menu used as reference to flag them as looped
        // so we can check if new items were added to the menu with the installation of new plugins.
        $menu[$item['order']]['looped'] = true;

      // Case of our custom added separators
      elseif (isset($item['separator']) && $item['separator'] === true) :
        // var_dump($item);
        // ID
        $menuIndexes = $menuIndexes + 10;
        
        $item[0]        = __('Separator', $this->textDomain);
        $item[1]        = 'read';
        $item[2]        = 'separator-'.$menuIndexes;
        $item[3]        = '';
        $item[4]        = 'wp-menu-separator';
        $item[6]        = 'dashicons-editor-insertmore';
        $item['id']     = 'separator-'.$menuIndexes;
        $item['rename'] = '';
        $item['icon']   = '';
        $item['looped'] = true;
        $item['order']  = $menuIndexes;
        
        // Check for impossible dashicons
        if ($addIcon) { $item = $this->handleIcon($item); }

        // Add menus
        $lists['available'][$menuIndexes] = $item;

      // END IF
      endif;

    endforeach;
    // End Loop
  endif;

  // Now we have to loop the adiitonal items and plugins that the user
  // can and WILL add after
  foreach ($menu as $order => &$item) :

    // if it was not looped already
    if (!isset($item['looped']) && is_array($item)) {
//      var_dump($item);
//      var_dump($order);

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

      // Adds
      // $order = 10;

      // Add, if not separator
      $lists['available']["$order"] = $item;

    }
  endforeach;

// Add to the global scope
$this->lists = $lists;
// var_dump($this->lists['available']);