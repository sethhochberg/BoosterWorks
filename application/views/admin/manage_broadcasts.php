<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div>
	<h2>Broadcasts</h2>
	<p>A broadcast is a one-time, mass email message that can be sent to different groups of users to provide them important information about a particular event, policy changes at a venue, a cancelation or time change, etc. Broadcasts are stored in the database for reference, but <i>cannot</i> be edited once they have been saved. Saving a broadcast triggers the mass mailing, and it cannot be stopped. Be mindful of this when composing your messages; you do not want to have to send a correction if at all avoidable. </p>
	<? echo anchor('admin/broadcast/compose', 'Send a new broadcast message'); ?>
	
</div>