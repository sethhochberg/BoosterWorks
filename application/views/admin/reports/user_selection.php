<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="contents">
	<h2>Search or browse for a user for whom to generate a report</h2>
	<div id="body">
				<fieldset>
					<legend>Search</legend>
						<?php echo $search_form_open; ?>
						<input id="search" name="search" type="submit" value="Search"></input>
						</form>
				</fieldset>
				<fieldset>
					<legend>Listing</legend>
						<?php echo $list_form_open; ?>
						<input id="list" name="list" type="submit" value="Generate"></input>
						</form>
				</fieldset>
		</div>
</div>