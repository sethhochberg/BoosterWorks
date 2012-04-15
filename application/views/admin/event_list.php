<div id="contents">
	<h3><? echo $view['title']; ?></h3>
	<div id="filters">

	<?= form_open('admin/events/listing'); ?>
	<?= form_dropdown('event_type', $event_types); ?>
	<?= form_dropdown('event_date', $date_ranges); ?>
	<?= form_submit('submit', 'Filter List'); ?>


	</div>
	<div id="body"><? echo $table; ?></div>
</div>