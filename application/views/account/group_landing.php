<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<div id="contents">
	<h2>Request Family/Group Change</h2>
	<p>You are about to request a change to your family/group name. This requires manual action from an administrator so that they may verify you are 
	requesting a valid change. You will no longer be able to make changes on behalf of users in your current family/group if you continue through with the change. 
	If you are sure this is what you want to do, hit 'request', and an event coordinator will contact you via email shortly.</p>
		
	<?php echo validation_errors(); ?>
	<?php echo form_open("profiles/change_group"); ?>
	<input type="submit" name="request" value="Request"/>
	</form>
</div>