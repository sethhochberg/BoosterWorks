<div id="contents">
	<p>All fields below required. Mark shift start and end times, and the number of people that can sign up for each shift.</p>
	<div id="body">
	<?php echo validation_errors(); ?>
		<?php 
			echo form_open("admin/events/shift_form/{$event_id}/{$shifts_count}"); 
			$counter = 1;
			while($counter <= $shifts_count)
			{
				echo("
							<fieldset>
								<p>
										<label for=\"shift_start\">Shift $counter Start</label>										
										<br /><input class=\"popupTimepicker\" id=\"shift_start_$counter\" type=\"text\" name=\"shift_start_$counter\" value=\"\" />
								</p>

								<p>
										<label for=\"shift_start\">Shift $counter End</label>										
										<br /><input class=\"popupTimepicker\" id=\"shift_end_$counter\" type=\"text\" name=\"shift_end_$counter\" value=\"\" />
								</p>
								
										<label for=\"shift_persons\">Shift $counter Total Persons Count</label>
								<p>
									<br /><input id=\"shift_persons_$counter\" type=\"text\" name=\"shift_persons_$counter\" value=\"\" />
								</p>
							</fieldset>	");
				$counter++;
			}
		?>
	</div>
	<div class="prepend-15">		
		<hr class="space"></hr>
		<?php echo form_submit('submit', 'Create these shifts'); ?>
		<?php echo form_close(); ?>

		<hr class="space"></hr>
	</div>
</div>