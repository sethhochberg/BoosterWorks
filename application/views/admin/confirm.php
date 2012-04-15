
<h2>Confirm action</h2>
<p>Are you sure you wish to <?php echo $action ?> the selected item? <b>This action cannot be undone!</b> You must notify any affected users that you have performed this action, notification will not be sent automatically.</p>

<hr class="space"></hr>
<a href="#" onClick="history.go(-1)">Cancel and go back</a> 

<hr class="space"></hr>
<?php echo anchor($url, 'I understand - continue'); ?>

<hr class="space"></hr>


