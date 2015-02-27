<?php
/**
 * Created by PhpStorm.
 * User: vedmaka
 * Date: 27.11.2014
 * Time: 18:49
 */

class LinkedInDataUserPage extends UnlistedSpecialPage {

	function __construct() {
		parent::__construct( 'LinkedInDataUserPage' );
	}

	function execute( User $user ) {

		if( !$user ) {
			return true;
		}

		$this->setHeaders();

		if( LinkedInData::getUserProfile($user) === false ) {
			$this->getOutput()->addHTML('This profile was not connected to LinkedIn network.');
			return true;
		}

		$data = array(
			'wiki' => array(
				'login' => $user->getName(),
				'realname' => $user->getRealName(),
				'email' => $user->getEmail()
			),
			'linkedin' => LinkedInData::getUserProfile($user),
			'connections' => LinkedInData::getUserConnections($user)
		);

		$this->getOutput()->addModules('ext.LinkedInData.main');

		$this->getOutput()->addHTML( Views::forge('UserPage', $data) );

	}

}