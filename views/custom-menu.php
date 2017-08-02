<div class="widget wpamm-custom-menu"><div class="widget-top">
  <div class="widget-title-action">
    <!-- Show more Icon, for non-separator buttons -->
    <a class="widget-action hide-if-no-js" href="#available-widgets"></a>
  </div>
  <div class="widget-title ui-sortable-handle">
    <h4 style="">
      <span data-wpamm-icon="dashicons-admin-post" class="dashicons <%= icon %>" style="">
      </span>
      <span class="wp-admin-menu-title"><%= title %></span>
        </h4>
      </div>
  </div>
  <div class="widget-inside" style="padding: 15px; display: block;">
    <div class="widget-content">
      <p>
        <label for="CM<%= id %>-rename">
          <span class="wpamm-title">Rename</span>
          <input class="widefat" id="CM<%= id %>-rename" name="menu[menu-<%= id %>][rename]" type="text" value="<%= title %>">
        </label>
      </p>

      <!--
Also Block Access?
-->
      <p>
        <label for="CM<%= id %>-block">
          <input class="" id="CM<%= id %>-block" name="menu[menu-<%= id %>][block]" type="checkbox">
          <span class="wpamm-title">Block Access?</span> <span class="dashicons dashicons-info" style="font-size: 16px; vertical-align: middle;" title="Checking this box will also block the access to the page via URL when this item is in the Disabled zone. A error message will be displayed or the user will be redirected to the URL you entered in the plugin Settings page."></span>
        </label>
      </p>

      <!--
Change Icons
-->
      <p>
        <label for="CM<%= id %>-icon">
          <span class="wpamm-title">Icon</span>

          <input class="widefat" id="CM<%= id %>-icon" name="menu[menu-<%= id %>][icon]" type="text" value="<%= icon %>">
          <input type="button" data-target="#CM<%= id %>-icon" class="button dashicons-picker" value="Choose Icon">

        </label>
      </p>

      <p>
        <span class="wpamm-title">Submenus</span><br>
        Edit name, link and position of submenus.
      </p><div class="wpamm-submenu-sortable ui-sortable">
      </div>


      <p></p>

    </div>



    <div class="widget-control-actions">

      <!-- Other Actions -->
      <div class="widget-control-actions-panel">

        <ul>
          <li><a href="#" class="wpamm-widget-send-to-available"><i class="dashicons dashicons-arrow-left-alt"></i> Send to available</a></li>
          <li><a href="#" class="wpamm-widget-send-to-disabled">Send to disabled <i class="dashicons dashicons-arrow-right-alt"></i></a></li>
        </ul>

      </div>
      <!-- Other actions end -->

      <div class="alignleft">
        <a class="widget-control-close wpamm-close" href="#close">Close Item</a>
      </div>

      <!--
<div class="alignright">
<input type="submit" name="savewidget" id="widget-search-2-savewidget" class="button button-primary widget-control-save right" value="Save"><span class="spinner"></span>
</div>
-->
      <br class="clear">
    </div>
  </div>

  <!-- Is SEPARATOR, show options -->

  <input class="order-carrier" type="hidden" name="menu[menu-<%= id %>][order]" value="<%= id %>">
  <input class="id-carrier" type="hidden" name="menu[menu-<%= id %>][id]" value="CM<%= id %>">
  <input class="is-custom-menu" type="hidden" name="menu[menu-<%= id %>][custom]" value="1">


  </div>