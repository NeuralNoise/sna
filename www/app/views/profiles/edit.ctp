<div class="profiles form">
<?php echo $form->create('Profile');?>
	<fieldset>
 		<legend><?php __('Edit Profile');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('nickname');
		echo $form->input('birthday', array(
				'dateFormat' => 'DMY',
				'timeFormat' => 'none',
				'minYear' => date('Y') - 70,
				'maxYear' => date('Y') - 18 ,
				'selected' => array(
					'year' => date('Y') - 25,
					'month' => '06',
					'day' => '15')));
		echo $form->input('location');
		echo $form->input('is_hidden');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>