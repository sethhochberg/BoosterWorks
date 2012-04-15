
<h2><? echo($event->event_name); ?> - <? echo date("F j, Y", strtotime($event->event_date)); ?></h2>

<?php echo validation_errors(); ?>
<?php echo $form_open; ?>
<fieldset id="volunteers">
	<legend>Confirmed Volunteers - <? echo $slots_available; ?> Slots Remain</legend>
	<?php echo $confirmed_table; ?> 
	<?php echo $attendance_submit; ?>
</fieldset>

<fieldset>
	<legend>Waiting List</legend>
	<?php echo $waitlist_table; ?>
</fieldset>

<fieldset>
	<legend>Event Options</legend>
	<?php echo $report_submit; ?>
	<?php echo $add_slot; ?>
	<?php echo $remove_slot; ?>	
</fieldset>



</form>
<br />
<? echo anchor('admin/events/listing', 'Back to events list'); ?>