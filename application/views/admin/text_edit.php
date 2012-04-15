<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div>
	<h2>Edit <?php echo $item ?></h2>
	<?php echo $form_open; ?>

	<?php echo validation_errors(); ?>

	<textarea name="ckedit" id="text" ><?php echo set_value('ckedit', $current); ?></textarea>
	<?php echo display_ckeditor($ckeditor); ?>
	<input type="submit" value="Save" />
</div>