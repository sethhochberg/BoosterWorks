<div id="contents">
	<h3>User Profiles</h3>
	<div id="filters">

	<?= form_open('admin/profiles/listing'); ?>
	<?= form_dropdown('is_student', $is_student_selection); ?>
	<?= form_input('name_search', 'Name'); ?>
	<?= form_submit('submit', 'Filter List'); ?>

	</div>
	<div id="body"><? echo $table; ?></div>
</div>