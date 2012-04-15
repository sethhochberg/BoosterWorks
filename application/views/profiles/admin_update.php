<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
 // Change the css classes to suit your needs    

echo $form_open ?>

<h3>Update an Account</h3>
<p>Fields marked with a " * " symbol are required.</p>
<fieldset>
	<legend>Personal</legend>
		<p>
				<label for="first_name">First Name <span class="required">*</span></label>
				<?php echo form_error('first_name'); ?>
				<br /><input id="first_name" type="text" name="first_name" maxlength="50" value="<?php echo set_value('first_name', $first_name); ?>"  />
		</p>

		<p>
				<label for="last_name">Last Name <span class="required">*</span></label>
				<?php echo form_error('last_name'); ?>
				<br /><input id="last_name" type="text" name="last_name" maxlength="50" value="<?php echo set_value('last_name', $last_name); ?>"  />
		</p>

		<p>
				<label for="date_of_birth">Date of Birth - MM/DD/YYYY<span class="required">*</span></label>
				<?php echo form_error('date_of_birth'); ?>
				<br /><input class="datepicker" id="date_of_birth" type="text" name="date_of_birth" maxlength="255" value="<?php echo set_value('date_of_birth', $date_of_birth); ?>"  />
		</p>
		
		<p>
				<label for="is_student">Is this person a student in the program? <span class="required">*</span></label>
				<?php echo form_error('is_student'); ?>
				<br />
						<?php $student_options = array(
														  ''  => 'Please Select',
														  '0'    => 'No',
														  '1'    => 'Yes'
														); ?>
						<?=form_dropdown('is_student', $student_options, set_value('is_student', $is_student))?>
		</p>
		
		<!--<p>
				<label for="carpool">Please select your carpool preference: <span class="required">*</span></label>
				<?php echo form_error('carpool'); ?>
				
				<?php // Change the values in this array to populate your dropdown as required ?>
				<?php $options = array(
														  ''  => 'Please Select',
														  '0'    => 'I am not interested in carpooling to events.',
														  '1'    => 'I am interested in carpools, but cannot or do not wish to drive.',
														  '2'    => 'I am interested in carpools, and am able/willing to drive when needed.'
														); ?>

				<br /><?=form_dropdown('carpool', $options, set_value('carpool'))?>
		</p>-->
		
		<p>
			<label for="family_group">Family/Group Name - This is the last name of the <i>student</i> that is used to manage this family's work.</label><span class="required">*</span>
			<?php echo form_error('family_group'); ?>
			<br /><input id="family_group" type="text" name="family_group" maxlength="50" value="<?php echo set_value('family_group', $family_group); ?>"  />
		</p>
</fieldset>

<fieldset>
	<legend>Contact and Account</legend>
		<p>
				<label for="secondary_email">Secondary Email</label>
				<?php echo form_error('secondary_email'); ?>
				<br /><input id="secondary_email" type="text" name="secondary_email" maxlength="255" value="<?php echo set_value('secondary_email', $secondary_email); ?>"  />
		</p>
		
		<p>
			<label for="primary_phone">Primary Phone <span class="required">*</span></label>
			<?php echo form_error('primary_phone'); ?>
			<br /><input id="primary_phone" type="text" name="primary_phone" maxlength="20" value="<?php echo set_value('primary_phone', $primary_phone); ?>"  />
		</p>

		<p>
				<label for="secondary_phone">Secondary Phone</label>
				<?php echo form_error('secondary_phone'); ?>
				<br /><input id="secondary_phone" type="text" name="secondary_phone" maxlength="20" value="<?php echo set_value('secondary_phone', $secondary_phone); ?>"  />
		</p>
</fieldset>

<hr class="space"></hr>

<p>
        <?php echo form_submit('submit', 'Save this profile'); ?>
</p>

<?php echo form_close(); ?>