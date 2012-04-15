<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('admin/events/create', $attributes); ?>

<fieldset>
	<legend>Event Essentials</legend>
		<p>
				<label for="event_type">Select the Event Type<span class="required">*</span></label>
				<?php echo form_error('event_type'); ?>
				
				<?php // Change the values in this array to populate your dropdown as required ?>
				<?php $options = array(
														  ''  => 'Please Select',
														  '0'    => 'Tropicana Field Concessions',
														  '1'    => 'Raymond James Stadium Concessions',
														  '2'    => 'Student Tag Day',
														  '3'	 => 'Renaissance Festival',
														  '4' 	 => 'Golf Parking',
														  '5'	 => 'Other'
														); ?>

				<br /><?=form_dropdown('event_type', $options, set_value('event_type'))?>
		</p>

		<p>
				<label for="event_name">Event Name <span class="required">*</span></label>
				<?php echo form_error('event_name'); ?>
				<br /><input id="event_name" type="text" name="event_name" maxlength="255" value="<?php echo set_value('event_name'); ?>"  />
		</p>

		<p>
				<label for="event_date">Event Date - MM/DD/YYYY<span class="required">*</span></label>
				<?php echo form_error('event_date'); ?>
				<br /><input id="event_date" type="text" name="event_date" maxlength="55" value="<?php echo set_value('event_date'); ?>"  />
		</p>

		<p>
				<label for="event_location">Event Location <span class="required">*</span></label>
				<?php echo form_error('event_location'); ?>
				<br /><input id="event_location" type="text" name="event_location" maxlength="255" value="<?php echo set_value('event_location'); ?>"  />
		</p>
</fieldset>

<fieldset>
	<legend>Volunteer Details</legend>
		<p>
				<label for="shifts_count">Number of shifts (ex: 8am - 12pm and 12pm - 4pm would be 2 shifts) <span class="required">*</span></label> 
				<?php echo form_error('shifts_count'); ?>
				<br /><input id="shifts_count" type="text" name="shifts_count" maxlength="11" value="<?php echo set_value('shifts_count'); ?>"  />
		</p>
</fieldset>

<fieldset>
	<legend>Other Info</legend>
		<p>
        <label for="event_notes">Event Notes - Any extra important information about this event goes here</label>
	<?php echo form_error('event_notes'); ?>
	<br />
							
	<?=form_textarea( array( 'name' => 'event_notes', 'rows' => '16', 'cols' => '30', 'value' => set_value('event_notes') ) )?>
</p>

</fieldset>

<div class="prepend-15">		
		<hr class="space"></hr>
		<?php echo form_submit( 'submit', 'Create this event!'); ?>
		<hr class="space"></hr>
</div>

<?php echo form_close(); ?>
