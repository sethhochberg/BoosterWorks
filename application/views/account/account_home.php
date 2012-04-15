<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>

<div id="contents">
	<h2>Welcome home, <?php echo $profile_data->first_name; ?></h2>
	<p>This is your personal account page. Here, you can see a quick overview of your information, your events, and maintain your profile.</p>
	<fieldset>
	<legend>Work</legend>
	<p><a href="account/future_shifts">See shifts my family group and I are signed up to work in the future</a></p>
	<p><a href="account/past_shifts">See past shifts my family group and I have worked</a></p>
	<p><a href="account/wait_list">See future events I am on the waiting list for</a></p>
	</fieldset>
	<fieldset>
	<legend>Account</legend>
	<p><a href="profiles/update">View and update profile info</a></p>
	<p><a href="profiles/change_group">Request a change to my family group</a></p>
	<p><a href="auth/change_password">Change my password</a></p>
	<p><a href="auth/change_email">Change my primary email address</a></p>
	</fieldset>
	
	
</div>