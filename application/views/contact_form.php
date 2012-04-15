<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div>
	<p>Send your questions, comments, or other requests - a booster representative will be in touch promptly. All fields below, except phone, are required. If you have an urgent question or issue, please visit the homepage and contact an event coordinator directly via their posted email address.</p>
	

	<?php echo validation_errors(); ?>

	<?php echo form_open("contact"); ?>


	<fieldset id="personal">
	<legend>Your info</legend>
		Name<br /><input type="text" name="name" value="" /><br />
		Phone<br /><input type="text" name="email" value="" /><br />
		Email Address<br /><input type="text" name="email" value="" /><br />
	</fieldset>
	<fieldset>
	<legend>Your message</legend>
		Message Type<br />
			<select id="type" name="type">
				<option>Please Select</option>
			 	<option>Question/Comment</option>
			  	<option>Software Bug Report</option>
			</select><br />
		Subject<br /><input type="text" name="subject" value="" /><br />
		Message - Be concise, but informative. The more information you provide to us, the better response we can give you!<br /><textarea rows="17" cols="70" name="message"></textarea>
	<fieldset>


	<input type="submit" name="contact" value="Send!" />


	</form>
</div>