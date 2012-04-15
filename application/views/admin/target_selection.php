	<p>
				<label for="target">Please select your destination group: <span class="required">*</span></label>
				<?php echo form_error('target'); ?>
				
				<?php // Change the values in this array to populate your dropdown as required ?>
				<?php $options = array(
														  ''  => 'Please Select',
														  '0'    => 'All registered users',
														  '1'    => 'Tropicana Field volunteers',
														  '2'    => 'Raymond James Stadium volunteers',
														  '3'    => 'RenFest volunteers',
														  '4'    => 'Golf parking volunteers',
														  '5'    => 'All users for a specific event (to be selected on the next screen)'
														); ?>

				<br /><?=form_dropdown('target', $options, set_value('target'))?>
		</p>