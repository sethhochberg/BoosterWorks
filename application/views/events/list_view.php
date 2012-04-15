<div id="contents">
	<h3><? echo $view['title']; ?></h3>
	<p><?php echo anchor('events/index', 'Prefer a calendar view?');?></p>
	<div id="filters">

	<?= form_open('events/listing'); ?>
	<?= form_dropdown('event_type', $event_types); ?>
	<?= form_dropdown('event_date', $date_ranges); ?>
	<?= form_submit('submit', 'Filter List'); ?>

	</div>
	<div id="body"><? echo $table; ?></div>
	<div id="links"><? echo $links; ?></div>
</div>