<div id="contents">
	<h2><? echo $event->event_name; echo(" Wait List"); ?></h2>
	<div id="body">
	<?php echo form_open("waitlist/signup/{$shift_id}"); ?>
		<p>Select which user in your family group you would like to place on the waiting list.</p>
		<p>
			<label for="selected_user">Who are you signing up?</label>
			<?php echo form_error('selected_user'); ?>
			<?php echo '<br />' . form_dropdown('selected_user', $user_list, $current_uid); ?>
		</p>
		<?php
			if ($is_tagday == '1')
			{ 
				echo("Since this is a tag day, you cannot select which account funds will be designated for. Tag days are for the general fund.");
			}
			else
			{
				echo('<p><label for="shift_for">Which student account should funds from this shift go to if this person is moved off the waiting list to an actual slot? Please enter the student\'s name.</label>' . form_error('shift_for') . '<br />
					<input id="shift_for" type="text" name="shift_for" maxlength="255" value="' . set_value('shift_for') . '"  />
				</p>');
			}?>
		</p>
		<p>
			<label for="transportation">How will this person be getting to this shift if they are moved off the waitlist?</label>
			<?php echo form_error('transportation'); ?>
			<?php echo '<br />' . form_dropdown('transportation', $transportation_list); ?>
		</p>
		<?php echo form_submit('submit', 'Sign up!'); ?>
				
	</div>
</div>