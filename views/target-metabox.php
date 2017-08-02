<?php

// Get Users
$users = $this->getMeta($post->ID, 'apply_to', false);
if (is_string($users)) $users = array();

// Get Roles
$rolesMeta = $this->getMeta($post->ID, 'roles', false);
$roles = is_array($rolesMeta) ? $rolesMeta : array();

?>

<div class="wpamm-metabox-field wpamm-metabox-field-user wpamm-metabox-field-545bc84396b19 field_type-user field_key-field_545bc84396b19" data-name="apply_to" data-type="user" data-key="field_545bc84396b19">
  
  <div class="wpamm-metabox-label">
    <label for="wpamm-metabox-field_545bc84396b19">
      <?php _e('Users to apply', $this->textDomain); ?>
    </label>
    <p class="description">
      <?php _e('Select the Users to which this menu will be visible.', $this->textDomain); ?>
    </p>
  </div>
  
  <div class="wpamm-metabox-input">
    <select id="wpamm-field-users" name="apply_to[]" multiple="multiple">
      <?php foreach(get_users() as $userName => $userInfo) :
      $selected = in_array($userInfo->ID, $users) ? 'selected="selected"' : '';
      echo "<option $selected value='$userInfo->ID'>".$userInfo->display_name." ($userInfo->user_email)</option>"; 
      endforeach; ?>
    </select>
  </div>
  
</div>

<div class="wpamm-metabox-field wpamm-metabox-field-role-selector wpamm-metabox-field-547845c8605b9 field_type-role_selector field_key-field_547845c8605b9" data-name="roles" data-type="role_selector" data-key="field_547845c8605b9">
  
  <div class="wpamm-metabox-label">
    <label for="wpamm-metabox-field_547845c8605b9"><?php _e('Roles to apply', $this->textDomain); ?></label>
    <p class="description"><?php _e('Select the Roles to which this menu will be visible.', $this->textDomain); ?></p>
  </div>
  
  <div class="wpamm-metabox-input">
    <select id="wpamm-field-roles" name="roles[]" multiple="multiple">
      <?php foreach(get_editable_roles() as $roleName => $roleInfo) :
      $selected = in_array($roleName, $roles) ? 'selected="selected"' : '';
      echo "<option $selected value='$roleName'>".$roleInfo['name']."</option>"; 
      endforeach; ?>
    </select>
  </div>
  
</div>
<script type="text/javascript">
  (function($){
    $(document).ready(function() {
      
      // Select: Roles
      $('#wpamm-field-roles').select2({placeholder: '<?php _e('Select which roles you want this menu setup to be applied to.', $this->textDomain); ?>'});
      
      // Select: Users
      $('#wpamm-field-users').select2({placeholder: '<?php _e('Select which users you want this menu setup to be applied to.', $this->textDomain); ?>'});
      
    });
  })(jQuery);
</script>
