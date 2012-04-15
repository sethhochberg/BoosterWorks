<div id="contents">
	<h2><? echo $event->event_name; ?></h2>
	<div id="body">
	<fieldset>
		<p><b>Type:</b> 
		<?php 
			if($event->event_type == '0')
				{echo "Tropicana Field Concessions";}
			elseif($event->event_type == '1')
				{echo "Raymond James Stadium Concessions";}
			elseif($event->event_type == '2')
				{echo "Student Tag Day";}
			elseif($event->event_type == '3')
				{echo "RenFest";}
			elseif($event->event_type == '4')
				{echo "Golf Parking";}				
		?></p>
		<p><b>Date:</b> <? echo $event->event_date; ?></p>
		<p><? echo $shifts ?></p>
		<p><b>Notes / Special Information:</b> <? if ($event->event_notes == ''){echo("The coordinator has not specified any special notes for this event.");}else{ echo $event->event_notes;} ?></p>
	</div>
	</fieldset>
</div>