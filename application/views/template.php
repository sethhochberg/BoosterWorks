<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<!--This is a Seth Hochberg project. www.sethhochberg.com -->
    <title><?php echo("Tarpon BoosterWorks - " . ucwords($this->router->class));?></title>

    <!-- Framework CSS -->
    <link rel="stylesheet" href="<?=base_url()?>assets/styles/screen.css" type="text/css" media="screen, projection"/>
	<link rel="stylesheet" href="<?=base_url()?>assets/styles/form.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" href="<?=base_url()?>assets/styles/print.css" type="text/css" media="print"/>
	<script src="<?=base_url()?>assets/js/jquery-1.5.1.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-20896088-6']);
		  _gaq.push(['_setDomainName', 'tarponspringsband.com']);
		  _gaq.push(['_setAllowHash', 'false']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
	<?php echo @$tablesortjs; ?>
	<?php echo @$datepickerjs; ?>

    <!--[if IE ]><link rel="stylesheet" href="<?=base_url()?>assets/styles/ie.css" type="text/css" media="screen, projection"/><![endif]-->
 </head>
 
 <body>		
		<div id="container" class="container">
			<noscript><div class="notice">It seems your browser either does not support Javascript or you do not have it turned on. The site should still work fine, however, you'll loose nice features like sorting data and easy selection of some items. We suggest you either use a modern browser (like Mozilla Firefox, Google Chrome, or Internet Explorer versions 7 or later) or simply enable Javascript in your browser's settings.</div></noscript>
			<div id="top" class="top">
			<div id="user" class="prepend-19 span-5">
						<?php 
						if (!$this->tank_auth->is_logged_in()) 
							{	
								echo("<img height=\"16\" width=\"16\" src=\"http://cdn.dustball.com/accept.png\" />"); echo(anchor('auth/login/',' Login',array('class'=>'text'))); echo(' or '); echo("<img height=\"16\" width=\"16\" src=\"http://cdn.dustball.com/application_form_edit.png\" />"); echo(anchor('auth/register/',' Register',array('class'=>'text')));							
							}
							else
							{
								$user_id = $this->tank_auth->get_user_id();
								$profile = $this->Profiles_model->get_by_user($user_id)->row(); echo("Welcome, "); echo($profile->first_name); echo("! ");echo("<img height=\"16\" width=\"16\" src=\"http://cdn.dustball.com/arrow_left.png\" />");echo(anchor('auth/logout/',' Logout',array('class'=>'text')));
							}
						?>
			</div>
			
			<hr  class="space"/>
				<div id="header_nav">
						<a href="<?=base_url()?>"><span style="font-size: 3em; line-height: 1; margin-bottom: 0.5em; color: #660000">Tarpon BoosterWorks</span></a>

						<?php $this->load->view('menu');	?>
				</div>
			</div>
			<hr class="space" />
			<?php echo @$this->session->flashdata('notice');?>
			
			<div id="body_contents">
				<?php echo $contents;?>
			</div>
			<div id="footer" align="center">				
			<hr />
			<p class="small">Page generated in {elapsed_time} seconds with <?php echo $this->db->total_queries(); ?>  queries and using {memory_usage} of memory at <?php $now = time(); $timezone = 'UM5'; $daylight_saving = TRUE; $now = gmt_to_local($now, $timezone, $daylight_saving); echo unix_to_human($now, TRUE, 'us'); ?></p>
			<p class="small">This is BoosterWorks v0.61, released Aug 27th 2011</p>
			<? if($this->Profiles_model->is_admin() == TRUE)
					{ 
						echo('<p><a href="' . base_url() . 'index.php/admin/dashboard">Administrator Control Panel</a></p>');
					}
			?>
						

			</div>
		</div>
	</body>
</html>
		