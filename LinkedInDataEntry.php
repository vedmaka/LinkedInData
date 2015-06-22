<?php
/**
 * Created by PhpStorm.
 * User: vedmaka
 * Date: 12.11.2014
 * Time: 11:15
 */

class LinkedInEntrySpecial extends SpecialPage {

	function __construct() {
		// TODO: Implement __construct() method.
		parent::__construct("LinkedInEntry");
	}

	public function execute( $subPage ) {

		global $wgServer, $wgScriptPath;

		$out = $this->getOutput();

		$code = $this->getRequest()->getVal('code');

		$returnto = $this->getRequest()->getVal('state');

		if( !$code ) {
			$out->addHTML('This page should not be called directly.');
			return false;
		}

		if( !empty( $code ) ) {

			//Do actions
			$result = LinkedInData::acquireAccessToken( $code );
			if( !$result || !array_key_exists('access_token', $result) ) {
				$out->addHTML('Unable to retrieve linkedin access token.');
				return false;
			}

			$access_token = $result['access_token'];
			$expires_in = $result['expires_in'];

			//Store token
			LinkedInData::saveToken( $this->getUser(), $access_token );

			//Save profile fields
			$profile = LinkedInData::callApi( $access_token,
				'people',
				'~:(id,first-name,last-name,maiden-name,formatted-name,phonetic-first-name,phonetic-last-name,formatted-phonetic-name,headline,location,industry,distance,current-status,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,public-profile-url)'
			);
			LinkedInData::saveProfile( $this->getUser(), $profile );
			LinkedInData::updateConnections( $this->getUser() );

			if( $returnto ) {
				$title = Title::newFromText( $returnto );
				if ( $title & $title->exists() ) {
					$out->redirect( Title::newFromText( $returnto )->getFullURL() );
					return true;
				}
			}
			$out->redirect( Title::newMainPage()->getFullUrl() );
		}

	}

}