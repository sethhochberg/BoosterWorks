<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div>
	<h3>Send mass mail to <? echo($target); ?></h3>
	
	<?php echo validation_errors(); ?>

	<?php echo form_open("{$url}"); ?>
	<p>Message Subject: <input type="text" name="subject" value="<?php echo set_value('subject'); ?>" size="50" /><p>
	
	<textarea name="message" id="message" ><?php echo set_value('message'); ?></textarea>
	<?php echo display_ckeditor($ckeditor); ?>
	<hr class="space"></hr>
	<p>The following users will recieve this email:</p>
	<?php echo $this->table->generate(); ?>
	<hr class="space"></hr>
	<p><b>If you are sure everything is in order</b>, click "send". <b>This is your last chance to make changes</b> before emails begin sending, and the process cannot be stopped. If you wish to preview your message, click the "Preview" icon near the top left of the message composition box. Click "send" only a single time, and do not close your browser until you see a success message.</p>
	<input type="submit" value="Send" />
	
</div>

 