<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
 // Change the css classes to suit your needs    

echo form_open('profiles/update'); ?>

<h3>Update your account</h3>
<p>Fields marked with a " * " symbol are required to save changes. </p>
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
				<label for="date_of_birth">Date of Birth <span class="required">*</span></label>
				<?php echo form_error('date_of_birth'); ?>
				<br /><input class="popupDatepicker" id="date_of_birth" type="text" name="date_of_birth" maxlength="255" value="<?php echo set_value('date_of_birth', $date_of_birth); ?>"  />
		</p>
		
		<p>
				<label for="is_student">Are you a student in the program? <span class="required">*</span></label>
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

				<br /><?=form_dropdown('carpool', $options, set_value('carpool', $carpool))?>
		</p>-->
</fieldset>

<fieldset>
	<legend>Contact and Account</legend>
		<p>
				You can change your primary email address and account password from your account page. Since they are tied to your login, they can't be updated here. <?php echo anchor('account', 'Go to my account page'); ?>
		</p>

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
        <?php echo form_submit('submit', 'Update my profile'); ?>
</p>

<?php echo form_close(); ?>