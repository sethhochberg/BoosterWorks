<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
 // Change the css classes to suit your needs    

echo $form_open;?>

<h3>Create an account!</h3>
<p>Don't worry - you're able to edit this information later at any time. Fields marked with a " * " symbol are required for registration.</p>
<fieldset>
	<legend>Personal</legend>
		<p>
				<label for="first_name">First Name <span class="required">*</span></label>
				<?php echo form_error('first_name'); ?>
				<br /><input id="first_name" type="text" name="first_name" maxlength="50" value="<?php echo set_value('first_name'); ?>"  />
		</p>

		<p>
				<label for="last_name">Last Name <span class="required">*</span></label>
				<?php echo form_error('last_name'); ?>
				<br /><input id="last_name" type="text" name="last_name" maxlength="50" value="<?php echo set_value('last_name'); ?>"  />
		</p>

		<p>
				<label for="date_of_birth">Date of Birth - MM/DD/YYYY<span class="required">*</span></label>
				<?php echo form_error('date_of_birth'); ?>
				<br /><input class="datepicker" id="date_of_birth" type="text" name="date_of_birth" maxlength="255" value="<?php echo set_value('date_of_birth'); ?>"  />
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
						<?=form_dropdown('is_student', $student_options, set_value('is_student'))?>
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
			<label for="family_group">Family/Group Name - This is the last name of the <i>student</i> you will be primarily working to raise funds for. The family/group name is used to determine the other members of your family that you'll be able to sign up to work events with you. If you have multiple students in the program with different last names, choose one name under which you manage your family's work.</label><span class="required">*</span>
			<?php echo form_error('family_group'); ?>
			<br /><input id="family_group" type="text" name="family_group" maxlength="50" value="<?php echo set_value('family_group'); ?>"  />
		</p>
</fieldset>

<fieldset>
	<legend>Contact and Account</legend>
		<p>
				<label for="email">Primary Email - Must be unique and a valid email address<span class="required">*</span></label>
				<?php if(isset($errors['email']))
										{
											echo '<br />';
											echo( '<div class="error">' . $errors['email'] . '</div'); 
										}	?>
				<?php echo form_error('email'); ?>
				<br /><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  />
		</p>

		<p>
				<label for="secondary_email">Secondary Email</label>
				<?php echo form_error('secondary_email'); ?>
				<br /><input id="secondary_email" type="text" name="secondary_email" maxlength="255" value="<?php echo set_value('secondary_email'); ?>"  />
		</p>
		
		<p>
			<label for="primary_phone">Primary Phone <span class="required">*</span></label>
			<?php echo form_error('primary_phone'); ?>
			<br /><input id="primary_phone" type="text" name="primary_phone" maxlength="20" value="<?php echo set_value('primary_phone'); ?>"  />
		</p>

		<p>
				<label for="secondary_phone">Secondary Phone</label>
				<?php echo form_error('secondary_phone'); ?>
				<br /><input id="secondary_phone" type="text" name="secondary_phone" maxlength="20" value="<?php echo set_value('secondary_phone'); ?>"  />
		</p>

		<p>
				<label for="password">Account Password <span class="required">*</span></label>
				<?php echo form_error('password'); ?>
				<br /><input id="password" type="password" name="password" maxlength="20" value="<?php echo set_value('password'); ?>"  />
		</p>

		<p>
				<label for="confirm_password">Confirm Password <span class="required">*</span></label>
				<?php echo form_error('confirm_password'); ?>
				<br /><input id="confirm_password" type="password" name="confirm_password" maxlength="20" value="<?php echo set_value('confirm_password'); ?>"  />
		</p>
</fieldset>

<hr class="space"></hr>

<p>
        <?php echo form_submit('submit', 'Click here to complete your registration'); ?>
</p>

<?php echo form_close(); ?>