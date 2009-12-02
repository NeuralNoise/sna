<?php 
/* SVN FILE: $Id$ */
/* ProfilesController Test cases generated on: 2009-12-01 17:12:49 : 1259683849*/
App::import('Controller', 'Profiles');

class TestProfiles extends ProfilesController {
	var $autoRender = false;
}

class ProfilesControllerTest extends CakeTestCase {
	var $Profiles = null;

	function setUp() {
		$this->Profiles = new TestProfiles();
		$this->Profiles->constructClasses();
	}

	function testProfilesControllerInstance() {
		$this->assertTrue(is_a($this->Profiles, 'ProfilesController'));
	}

	function tearDown() {
		unset($this->Profiles);
	}
}
?>