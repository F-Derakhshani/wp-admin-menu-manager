<div id="" class="widget">
  <div class="widget-top">
    <div class="widget-title-action">
      
      <?php 
      // Ajust in the menu id tag
      $target = strip_tags($menu[0]);
      // var_dump($target);
      $target = str_replace(' ', '', $target);
      $target = preg_replace("/ [0-9]/","", $target);
      $target = preg_replace("/[0-9]/","", $target);
      ?>
      
      <?php if (isset($menu['separator']) && !$menu['separator']) : ?>
        <!-- Show more Icon, for non-separator buttons -->
        <a class="widget-action hide-if-no-js" href="#available-widgets"></a>
      
      <?php else : ?>
        <!-- Separator delete button -->
        <a class="wpamm-separator-delete" href="#"><i class="dashicons dashicons-dismiss"></i></a>
      <?php endif; ?>
      
    </div>

    <?php
    /**
     * Icon as images
     * Little hack so we can display some icons that are images adn can be shown up.
     */
    
    $icon = true;
    $image = false;

    // Check if has image
    if (filter_var($menu[6], FILTER_VALIDATE_URL) == true) {
      $icon = false;
      $image = true;
    }

    ?>
    
    <div class="widget-title">
      <h4 style="<?php echo ($menu['separator']) ? 'background-color: #fefefe !important;' : ''; ?>">
        <span data-wpamm-icon="<?php echo $menu[6]; ?>" class="dashicons <?php echo ($menu['icon']) ? $menu['icon'] : $menu[6]; ?>" style="">
        
          <?php if ($image) : ?>
            <img <?php if (!empty($menu['icon'])) echo 'style="display:none;"'; ?> class="wpamm-icon-image" src="<?php echo $menu[6]; ?>">
          <?php endif; ?>
        
        </span>
        <span class="wp-admin-menu-title"><?php echo ($menu['rename']) ? $menu['rename'].' ('.$menu[0].')' : $menu[0]; ?></span>
      </h4>
    </div>
  </div>

  <?php if (!$menu['separator']) : ?>
  <div class="widget-inside" style="padding: 15px;">
      
      <?php
      // var_dump($menu[0]);
      ?>
    
      <div class="widget-content">
        <p>
          <label for="<?php echo $target; ?>-rename">
          <span class="wpamm-title"><?php _e('Rename', $this->textDomain); ?></span>
          <input class="widefat" id="<?php echo $target; ?>-rename" name="menu[menu-<?php echo $newOrder; ?>][rename]" type="text" value="<?php echo $menu['rename']; ?>">
          </label>
        </p>
        
        <!--
        Change Icons
        -->
        <p>
          <label for="<?php echo $target; ?>-icon">
          <span class="wpamm-title"><?php _e('Icon', $this->textDomain); ?></span>
          
          <input class="widefat" id="<?php echo $target ?>-icon" name="menu[menu-<?php echo $newOrder; ?>][icon]" type="text" value="<?php echo $menu['icon']; ?>">
          <input type="button" data-target="#<?php echo $target ?>-icon" class="button dashicons-picker" value="<?php _e('Choose Icon', $this->textDomain); ?>" />
            
          </label>
        </p>

        <?php if ($this->hasItemSubmenus($menu[2])) : ?>
        <p>
          <span class="wpamm-title"><?php _e('Submenus', $this->textDomain); ?></span><br>
          <?php _e('Edit name, link and position of submenus.', $this->textDomain); ?>

          <div class="wpamm-submenu-sortable">
            <?php $this->getItemSubmenus($menu[2]); ?>
          </div>

          <!-- <div class="wpamm-add-more">
            <div class="alignleft">
              <a class="wpamm-button wpadmin-add-new-submenu" href="#"><?php _e('Add new submenu', $this->textDomain); ?></a>
            </div>
          </div> -->
        </p>
      <?php endif; ?>

      </div>

      <!--
      <input type="hidden" name="id_base" class="id_base" value="search">
      <input type="hidden" name="widget-width" class="widget-width" value="250">
      <input type="hidden" name="widget-height" class="widget-height" value="200">
      <input type="hidden" name="widget_number" class="widget_number" value="2">
      <input type="hidden" name="multi_number" class="multi_number" value="">
      <input type="hidden" name="add_new" class="add_new" value="">
      -->

      <div class="widget-control-actions">
                          
        <!-- Other Actions -->
        <div class="widget-control-actions-panel">
          
          <ul>
            <li><a href="#" class="wpamm-widget-send-to-available"><i class="dashicons dashicons-arrow-left-alt"></i> <?php _e('Send to available', $this->textDomain); ?></a></li>
            <li><a href="#" class="wpamm-widget-send-to-disabled"><?php _e('Send to disabled', $this->textDomain); ?> <i class="dashicons dashicons-arrow-right-alt"></i></a></li>
          </ul>
          
        </div>
        <!-- Other actions end -->
        
        <div class="alignleft">
          <a class="widget-control-close wpamm-close" href="#close"><?php _e('Close Item', $this->textDomain); ?></a>
        </div>

        <!--
        <div class="alignright">
          <input type="submit" name="savewidget" id="widget-search-2-savewidget" class="button button-primary widget-control-save right" value="<?php _e('Save', $this->textDomain); ?>"><span class="spinner"></span>
        </div>
        -->
        <br class="clear">
      </div>
  </div>

<!-- Is SEPARATOR, show options -->
<?php elseif (false) : ?>

  <div class="widget-inside" style="padding: 15px;">
      <div class="widget-content">
        
        <p>
          <span class="wpamm-title"><?php _e('Separator options', $this->textDomain); ?></span><br>
          <?php _e('You can duplicate or delete them.', $this->textDomain); ?>
        </p>
        
        <p class="separator-action-button">
          <button class="wpamm-separator-duplicate button button-primary"><?php _e('Duplicate', $this->textDomain); ?></button>
          <button class="wpamm-separator-delete button"><?php _e('Delete', $this->textDomain); ?></button>
        </p>
        
        <div class="widget-control-actions">
          
          <div class="">
            <a class="widget-control-close wpamm-close" href="#close"><?php _e('Close Item', $this->textDomain); ?></a>
          </div>

          
<!--
          <div class="alignright">
            <input type="submit" name="savewidget" id="widget-search-2-savewidget" class="button button-primary widget-control-save right" value="<?php _e('Save', $this->textDomain); ?>"><span class="spinner"></span>
          </div>
-->
          
          <br class="clear">
        </div>
        
      </div>
  </div>

<!-- End IF separator -->
<?php endif; ?>

<input class="order-carrier" type="hidden" name="menu[menu-<?php echo $newOrder; ?>][order]" value="<?php echo $menuID; ?>">
<input class="id-carrier" type="hidden" name="menu[menu-<?php echo $newOrder; ?>][id]" value="<?php echo $menu[2]; ?>">

<?php if ($menu['separator'] === true) : ?>
<input class="separator" type="hidden" name="menu[menu-<?php echo $newOrder; ?>][separator]" value="1">
<?php endif; ?>

</div>