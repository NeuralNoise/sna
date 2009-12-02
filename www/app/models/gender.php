<?php
class Gender extends AppModel {
	
	var $name = 'Gender';
	var $validate = array(
		'is_hidden' => array('numeric'),
		'label' => array('notempty'),
	);
	
	var $displayField = array('%s', '{n}.Gender.label');
	// TODO
	var $displayFieldDoTranslate = true;
	
	var $hasMany = array(
		'Profile' => array(
			'className' => 'Profile',
			'foreignKey' => 'gender_id',
			'dependent' => false,
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'gender_id',
			'dependent' => false,
		)
	);

}
?>