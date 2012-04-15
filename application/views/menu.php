<div id="menu">
	<ul class="menu">
	<li><a href="<?=base_url() ?>index.php" target="_self">Home</a></li>  
	<?php if($this->tank_auth->is_logged_in())
	{
		echo('
			   <li><a href="' . base_url() . 'index.php/events" target="_self">Events</a></li>
			   <li><a href="' . base_url() . 'index.php/account" target="_self">My Account</a></li>'); 
	}?>
   <li><a href="<?=base_url() ?>index.php/help" target="_self">Help and Info</a></li>      
   <li><a href="<?=base_url() ?>index.php/contact" target="_self">Contact</a></li>
   </ul>
</div>