<div class="users form">
<?php echo $form->create('User');?>
	<fieldset>
 		<legend><?php __('Edit User');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('has_accepted_tos');
		echo $form->input('is_disabled');
		echo $form->input('is_active');
		echo $form->input('username');
		echo $form->input('password');
		echo $form->input('nickname');
		echo $form->input('email');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('User.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('User.id'))); ?></li>
		<li><?php echo $html->link(__('List Users', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Messages', true), array('controller'=> 'messages', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Message', true), array('controller'=> 'messages', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Shouts', true), array('controller'=> 'shouts', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Shout', true), array('controller'=> 'shouts', 'action'=>'add')); ?> </li>
	</ul>
</div>