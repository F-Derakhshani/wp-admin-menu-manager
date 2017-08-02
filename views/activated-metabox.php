<?php
// Get Users
$activated = $this->getMeta($post->ID, 'activated', false);
$checked = $activated || $post->post_status == 'auto-draft' ? 'checked="checked"' : '';
?>
<div class="wpamm-metabox-field wpamm-metabox-field-true-false wpamm-metabox-field-54c235579bfe9 field_type-true_false field_key-field_54c235579bfe9" data-name="activated" data-type="true_false" data-key="field_54c235579bfe9">
  
  <div class="wpamm-metabox-label">
    <label for="wpamm-metabox-field_54c235579bfe9"><?php _e('Activate Menu?', $this->textDomain); ?></label>
    <p class="description"><?php _e('To prevent this menu from taking any effect, just turn off this switch.', $this->textDomain); ?></p>
  </div>
  
  <div class="wpamm-metabox-input">
    <ul class="wpamm-metabox-checkbox-list wpamm-metabox-bl">
      <li>
        <label>
          <input <?php echo $checked; ?> type="checkbox" id="wpamm-metabox-field_54c235579bfe9-1" name="activated" value="1"><?php _e('Activate this menu setup.', $this->textDomain); ?></label>
      </li>
    </ul>		
  </div>
  
</div>

<script type="text/javascript">
</script>