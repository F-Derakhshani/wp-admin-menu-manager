<div class="wrap wpamm-metabox-settings-wrap">

  <h2></h2>
  
  <?php if (isset($_GET['updated']) and $_GET['updated'] == 'true') : ?>
  <div class="updated "><p><?php _e('Settings successfully updated!'); ?></p></div>
  <?php endif; ?>
  
  <?php

  // Get Purchase Code
  $pc         = get_option('options_wpamm_purchase_code');
  $whitelabel = get_option('options_wpamm_whitelabel');
  $checked    = $whitelabel ? 'checked="checked"' : '';

  ?>

  <form id="post" method="post" name="post">
    
    <?php echo wp_nonce_field('___wpamm_settings'); ?>
    <input type="hidden" name="wpamm-settings-page-submit">
    
    <div id="poststuff">

      <div id="post-body" class="metabox-holder columns-2">

        <!-- Main -->
        <div id="post-body-content">

          <div id="normal-sortables" class="meta-box-sortables ui-sortable">

            <div id="normal-sortables" class="meta-box-sortables"><div id="wpamm-metabox-group_5544d8a4b84a4" class="postbox  wpamm-metabox-postbox default">
              <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Settings</span></h3>
              <div class="inside wpamm-metabox-fields wpamm-metabox-cf">
                <div class="wpamm-metabox-field wpamm-metabox-field-text wpamm-metabox-field-5554ae984791b field_type-text field_key-field_5554ae984791b" data-name="wpamm_purchase_code" data-type="text" data-key="field_5554ae984791b">
                  <div class="wpamm-metabox-label">
                    <label for="wpamm-metabox-field_5554ae984791b">Purchase Code</label>
                    <p class="description">Add your purchase code to get automatic updates to the plugin.</p>
                  </div>
                  
                  <div class="wpamm-metabox-input">
                    <div class="wpamm-metabox-input-wrap"><input type="text" id="wpamm-metabox-field_5554ae984791b" class="" name="wpamm_purchase_code" value="<?php echo $pc; ?>" placeholder=""></div>		
                  </div>
                </div>
                
                <div class="wpamm-metabox-field wpamm-metabox-field-true-false wpamm-metabox-field-5544d8fa32d3c field_type-true_false field_key-field_5544d8fa32d3c" data-name="wpamm_whitelabel" data-type="true_false" data-key="field_5544d8fa32d3c">
                  <div class="wpamm-metabox-label">
                    <label for="wpamm-metabox-field_5544d8fa32d3c">White-label the Plugin</label>
                    <p class="description">Checking this box will remove the branding elements of the plugin, like the custom header of the pages and the about page. 
                      <br><br>
                      This can also be achieved by adding <code>add_filter('wpamm/settings/whitelabel', '__return_true');</code> to your functions.php.</p>
                  </div>
                  <div class="wpamm-metabox-input">
                    <ul class="wpamm-metabox-checkbox-list wpamm-metabox-bl ">
                      <li><label>
                        
                        <input <?php echo $checked; ?> type="checkbox" id="wpamm-metabox-field_5544d8fa32d3c-1" name="wpamm_whitelabel" value="1">White-label the Plugin.</label></li></ul>		
                  </div>
                </div>
              </div>
              </div>
            </div>					
          </div>

        </div>

        <!-- Sidebar -->
        <div id="postbox-container-1" class="postbox-container">

          <div id="side-sortables" class="meta-box-sortables ui-sortable">

            <!-- Update -->
            <div id="submitdiv" class="postbox">

              <h3 class="hndle" style="border-bottom:none;"><span>Publish</span></h3>

              <div id="major-publishing-actions">

                <div id="publishing-action">
                  <span class="spinner"></span>
                  <input type="submit" accesskey="p" value="Save Options" class="button button-primary button-large" id="publish" name="publish">
                </div>

                <div class="clear"></div>

              </div>

            </div>

            <div id="side-sortables" class="meta-box-sortables"></div>						
          </div>

        </div>

      </div>

      <br class="clear">

    </div>

  </form>

</div>