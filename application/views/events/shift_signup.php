<div id="contents">
	<h2><? echo $event->event_name; echo(" Signup"); ?></h2>
	<div id="body">
	<?php echo form_open("shifts/signup/{$shift_id}"); ?>
		<p>Select which user in your family group you are signing up to work this shift. Family members that are already signed up will not show in the selection.</p>
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
				echo('<p><label for="shift_for">Which student account should funds from this shift go to? Please enter the student\'s name.</label>' . form_error('shift_for') . '<br />
					<input id="shift_for" type="text" name="shift_for" maxlength="255" value="' . set_value('shift_for') . '"  />
				</p>');
			}?>
		</p>
		<p>
			<label for="transportation">How is this person getting to this shift?</label>
			<?php echo form_error('transportation'); ?>
			<?php echo '<br />' . form_dropdown('transportation', $transportation_list); ?>
		</p>
		<?php echo form_submit('submit', 'Sign up!'); ?>
				
	</div>
</div>