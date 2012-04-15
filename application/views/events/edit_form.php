<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open("admin/events/edit/{$id}", $attributes); ?>

<p>You are editing an existing event - changes will go live immediatly. </p>

<fieldset>
	<legend>Event Essentials</legend>
		<p>
				<label for="event_type">Event Type <span class="required">*</span></label>
				<p>0 = Tropicana Field, 1 = Raymond James, 2 = Tag Day, 3 = Ren Fest, 4 = Golf Parking, 5 = Other</p>
				<?php echo form_error('event_type'); ?>
				<br /><input id="event_type" type="text" name="event_type" maxlength="255" value="<?php echo set_value('event_type', $event['event_type']); ?>"  />
		</p>

		<p>
				<label for="event_name">Event Name <span class="required">*</span></label>
				<?php echo form_error('event_name'); ?>
				<br /><input id="event_name" type="text" name="event_name" maxlength="255" value="<?php echo set_value('event_name', $event['event_name']); ?>"  />
		</p>

		<p>
				<label for="event_date">Event Date <span class="required">*</span></label>
				<?php echo form_error('event_date'); ?>
				<br /><input class="popupDatepicker" id="event_date" type="text" name="event_date" maxlength="55" value="<?php echo set_value('event_date', $event['event_date']); ?>"  />
		</p>

		<p>
				<label for="event_location">Event Location <span class="required">*</span></label>
				<?php echo form_error('event_location'); ?>
				<br /><input id="event_location" type="text" name="event_location" maxlength="255" value="<?php echo set_value('event_location', $event['event_location']); ?>"  />
		</p>
</fieldset>

<!--fieldset>
	<legend>Volunteer Details</legend>
		<p>
				<label for="total_slots">Total Slots - The absolute maximum number of people that can work any given shift<span class="required">*</span></label>
				<?php echo form_error('total_slots'); ?>
				<br /><input id="total_slots" type="text" name="total_slots" maxlength="11" value="<?php //echo set_value('total_slots'); ?>"  />
		</p>

		<p>
				<label for="available_slots">Initial Available Slots  - The number of people not already spoken for (counting coordinaters, etc)<span class="required">*</span></label>
				<?php echo form_error('available_slots'); ?>
				<br /><input  id="available_slots" type="text" name="available_slots" maxlength="11" value="<?php //echo set_value('available_slots'); ?>"  />
		</p>

		<p>
				<label for="shifts_count">Number of shifts (ex: 8am - 12pm and 12pm - 4pm would be 2 shifts) <span class="required">*</span></label> 
				<?php echo form_error('shifts_count'); ?>
				<br /><input id="shifts_count" type="text" name="shifts_count" maxlength="11" value="<?php //echo set_value('shifts_count'); ?>"  />
		</p>
</fieldset-->

<fieldset>
	<legend>Other Info</legend>
		<p>
        <label for="event_notes">Event Notes - Any extra important information about this event goes here</label>
	<?php echo form_error('event_notes'); ?>
	<br />
							
	<?=form_textarea( array( 'name' => 'event_notes', 'rows' => '16', 'cols' => '30', 'value' => set_value('event_notes', $event['event_notes']) ) )?>
</p>

</fieldset>

<div class="prepend-15">		
		<hr class="space"></hr>
		<?php echo form_submit( 'submit', 'Update this event!'); ?>
		<hr class="space"></hr>
</div>

<?php echo form_close(); ?>
