<?php
class ProfilesController extends AppController {
	
	var $name = 'Profiles';
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('view'));
		// SecurityComponent setup
		$this->Security->requirePost('shout_hide', 'shout_unhide', 'shout_delete', 'shout_to');
		if(!empty($this->data)) {
			$this->Security->requirePut('view', 'shout_to'); // shout_to is included by view
			$this->Security->requirePost('edit');
		}
	}
	
	function self() {
		$this->redirect(array('action' => 'view',
			$this->Profile->getAuthedId($this->Auth->user())));
	}
	
	function search() {
		// TODO
		$this->set('profiles', $this->paginate());
	}
	
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Profile.', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			$this->shout_to($id);
		}
		$this->set('profile', $this->Profile->read(null, $id));
		$this->set('shouts', $this->shouts($id));
	}
	
	function edit() {
		$this->layout = 'settings';
		// TODO, on first edit, default $this->data hidden to 0 
		$id = $this->Profile->getAuthedId($this->Auth->user());
		if (!empty($this->data)) {
			$this->data['Profile']['user_id'] = $this->Auth->user('id');
			if ($this->Profile->save($this->data, true, array(
						'is_hidden', 'nickname', 'birthday', 'location'))) {
				$this->Session->setFlash(___('The Profile has been saved'), '_flash_success');
				$this->redirect($this->referer());
			} else {
				$this->Session->setFlash(__('The Profile could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Profile->read(null, $id);
		}
		$this->set(compact('users'));
	}
	
	// Below: integrated shouts actions, because of integrated views (profile view with shouts)
	function shout_to($toProfileId = null) {
		$shouted = false;
		if($this->Auth->isAuthorized() and $toProfileId != null and !empty($this->data)) {
			$this->Profile->Shout->create();
			$this->Profile->Shout->set(array(
				'user_id' => $this->Auth->user('id'),
				'profile_id' => $toProfileId,
				'from_profile_id' => $this->Profile->getAuthedId($this->Auth->user())));
			if ($this->Profile->Shout->save($this->data, true,
					array('user_id', 'profile_id', 'from_profile_id', 'body'))) {
				$this->Session->setFlash(___('The Shout has been saved'), '_flash_success');
				$shouted = true;
			} else {
				$this->Session->setFlash(___('The Shout could not be saved. Please, try again.'));
			}
		}
		if ($shouted) {
			unset($this->data['Shout']);
			if ($this->action == 'shout_to') {
				$this->redirect(array('action' => 'view', $toProfileId, '#' => 'shouts'));
			}
		}
	}
	
	function shouts($profileId) {
		$numberOfShoutsPerPage = 10;
		if (!$profileId) {
			$this->Session->setFlash(__('Invalid Profile.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->paginate = array(
			'fields' => array(
				'Profile.id',
				'Shout.id',
				'Shout.created',
				'Shout.body',
				'Shout.is_hidden',
				'Shout.is_deleted_by_shouter',
				'Shout.is_deleted',
				'Shout.from_profile_id',
				'FromProfile.id',
				'FromProfile.nickname',
			),
			'joins' => array(
				array(
					'type' => 'LEFT', 
					'table' => $this->Profile->Shout->useTable,
					'alias' => $this->Profile->Shout->alias,
					'foreignKey' => $this->Profile->Shout->primaryKey,
					'conditions' => $this->Profile->escapeField($this->Profile->primaryKey) . ' = '
						. $this->Profile->Shout->escapeField('profile_id'),
				),
				array(
					'type' => 'LEFT', 
					'table' => $this->Profile->Shout->FromProfile->useTable,
					'alias' => $this->Profile->Shout->FromProfile->alias,
					'foreignKey' => $this->Profile->Shout->FromProfile->primaryKey,
					'conditions' => $this->Profile->Shout->escapeField('from_profile_id') . ' = '
						. $this->Profile->Shout->FromProfile->escapeField(
							$this->Profile->Shout->FromProfile->primaryKey),
				),
			),
			'conditions' => array(
				'Profile.id' => $profileId,
				'Shout.is_deleted' => 0,
				'OR' => array(
					// Shout not hidden by shouter... OR
					'Shout.is_deleted_by_shouter' => 0,
					// Shout is on selves profile thus hidden can be seen by you... OR
					array(
						'Shout.profile_id' => $this->Profile->getAuthedId($this->Auth->user()),
						'Shout.is_deleted_by_shouter' => 1,
					),
					/*
					// Shout is by yourself and thus can be seen by you.
					array(
						'Shout.from_profile_id' => $this->Profile->getAuthedId($this->Auth->user()),
						'Shout.is_deleted_by_shouter' => 1,
					),
					*/
				),
			),
			'order' => 'Shout.created DESC',
			'limit' => $numberOfShoutsPerPage,
		);
		$shouts = $this->paginate();
		$this->set('shouts', $shouts);
		return $shouts;
	}
	
	function shout_hide($id = null) {
		$this->_shout_toggle_hidden($id, 1);
		$this->redirect(array('action' => 'self'));
	}
	
	function shout_unhide($id = null) {
		$this->_shout_toggle_hidden($id, 0);
		$this->redirect(array('action' => 'self'));
	}
	
	function _shout_toggle_hidden($id, $flag) {
		if (($currentState = $this->Profile->Shout->getFieldIfExists($id, 'is_hidden')) === false) {
			$this->Session->setFlash(___('Invalid shout.'));
		} else {
			// Setup possible language strings
			if ($currentState == 1) {
				$impossibility = __('Shout not hidden.', true);
				$success = __('Shout has been unhidden.', true);
				$failure = __('Shout count not be unhidden.', true);
			} else if ($currentState == 0) {
				$impossibility = __('Shout already hidden.', true);
				$success = __('Shout has been hidden.', true);
				$failure = __('Shout could not be hidden.', true);
			}
			// Try to toggle
			if ($currentState == $flag) {
				$this->Session->setFlash($impossibility);
			} else {
				$this->Profile->Shout->id = $id;
				if ($this->Profile->Shout->saveField('is_hidden', $flag)) {
					$this->Session->setFlash($success, '_flash_success');
				} else {
					$this->Session->setFlash($failure);
				}
			}
		}
	}
	
	function shout_delete($id = null) {
		$data = $this->Profile->Shout->find('first',
			array(
				'conditions' => array('id' => $id, 'is_deleted' => 0),
				'fields' => array('profile_id', 'from_profile_id', 'is_deleted'),
			)
		);
		$success = false;
		if ($data !== false) {
			$authedProfileId = $this->Profile->getAuthedId($this->Auth->user());
			// Shout on own profile
			$this->Profile->Shout->id = $id;
			if ($data['Shout']['profile_id'] == $authedProfileId) {
				if ($this->Profile->Shout->saveField('is_deleted', 1)) {
					$success = true;
				}
			// Shout on other's profile
			} else if ($data['Shout']['from_profile_id'] == $authedProfileId) {
				if ($this->Profile->Shout->saveField('is_deleted_by_shouter', 1)) {
					$success = true;
				}
			}
		}
		if ($success) {
			$this->Session->setFlash(___('Shout deleted.'), '_flash_success');
		} else {
			$this->Session->setFlash(___('Shout could not be deleted.'));
		}
		$this->redirect(array('action' => 'self'));
	}
	
}
?>