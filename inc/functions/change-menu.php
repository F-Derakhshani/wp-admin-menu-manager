<?php

// if has menu modifier
if ($this->activeMenu) :
  
  // Make new submenu
  $this->newSubmenu = $this->makeSubmenu($this->activeMenu);

  // Rename, relink and hide submenus
  $newSubmenu = $this->reStuffSubmenus($this->newSubmenu);

  // Resets our submenus as weel
  unset($GLOBALS['submenu']);
  $GLOBALS['submenu'] = $newSubmenu;

  // Make new menu array available
  $this->lists = $this->makeLists($this->activeMenu, false); // Fix for 3.0.5
  $newMenu = $this->lists['available'];

  // Sets as global menu
  unset($GLOBALS['menu']);
  $GLOBALS['menu'] = $newMenu;

  // Rename whats to rename
  $newMenu = $this->renameMenus($newMenu);

endif;