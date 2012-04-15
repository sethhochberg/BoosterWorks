<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="contents">
	<h2><? echo $view['title']; ?></h2>
	<div id="body">
				<fieldset>
					<legend>Events</legend>
						<a href="<?=base_url()?>index.php/admin/events/create">Create a new event</a><hr class='space'></hr>
						<a href="<?=base_url()?>index.php/admin/events/listing">View or Edit an existing event</a><hr class='space'></hr>
				</fieldset>
				<fieldset>
					<legend>Users</legend>
						<a href="<?=base_url()?>index.php/admin/profiles/create">Create a new user profile</a><hr class='space'></hr>
						<a href="<?=base_url()?>index.php/admin/profiles/listing">View or Edit an existing user profile</a><hr class='space'></hr>
				</fieldset>
				<fieldset>
					<legend>Email and Reporting</legend>
						<p>Select a group to send mail to:</p>
						<?php echo $mail_form_open; echo $mail_target; ?>
						<input id="mail" name="mail" type="submit" value="Compose Message"></input>
						</form>
						
						<hr class="space"></hr>
						
						<p>Select a report to generate:</p>
						<p class="small">Event reporting features have been moved to the 'event details' page.</p>
						<?php //echo $report_form_open; //echo $report_selection; ?>
						<!--<input id="report" name="report" type="submit" value="Continue"></input>-->
						</form>
				</fieldset>	
				<fieldset>
					<legend>Site</legend>
						<a href="<?=base_url()?>index.php/admin/site/homepage">Edit Homepage</a><hr class='space'></hr>
						<a href="<?=base_url()?>index.php/admin/site/help">Edit Help and Info Page</a><hr class='space'></hr>
				</fieldset>
		</div>
</div>