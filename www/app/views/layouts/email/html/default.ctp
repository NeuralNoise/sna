<!DOCTYPE HTML PUBLIC
	"-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<?=$html->charset()?>
		<title>
			<?=$title_for_layout?>
		</title>
		<?php
			echo $html->css('html_email');
			echo $scripts_for_layout;
		?>
	</head>
	<body>
		<?=$content_for_layout?><br>
		<br>
		--<br>
		<?php
		$domainName = env('SERVER_NAME');
		if (strpos($domainName, 'www.') === 0) {
			$domainName = substr($domainName, 4);
		}
		?>
		<b><?=$domainName?></b>
		<br>
		<br>
		<?=__('This message has been generated. Do not reply to this message.', true)?><br>
		<?=__('If you require support, visit', true)?> <?=$html->link(__('http://' . env('SERVER_NAME') . '/pages/public/support', true),
				array('controller' => 'pages', 'action' => 'display', 'http://' . env('SERVER_NAME') . '/public/support'))?>
	</body>
</html>