<?php
/**
 * Class declaration for mediawiki extension LinkedInData
 *
 * @file LinkedInData.class.php
 * @ingroup LinkedInData
 */

class LinkedInData {

	public static function requestUserToken()
	{

		global $wgUser, $wgOut, $wgLinkedInDataSettings;

		//Do nothing if user already have valid token
		if( self::haveValidToken($wgUser) ) {
			return true;
		}

		//Otherwise, start token requesting process
		$redirectUrl = 'https://www.linkedin.com/uas/oauth2/authorization'
			.'?response_type=code'
			.'&client_id='.$wgLinkedInDataSettings['client_id']
			.'&redirect_uri='.self::getRedirectUrl()
			.'&state='.md5(microtime())
			.'&scope=r_basicprofile,r_emailaddress,r_network';

		$wgOut->redirect( $redirectUrl );

	}

	public static function haveValidToken( User $user )
	{

		if( !$user->isAnon() ) {

			$token = Model_Linkedin_token::find(array(
				'user_id' => $user->getId()
			));

			if( !count($token) ) {
				return false;
			}

			$token = $token[0];

			//Calculate difference in time:
			$now = time();
			$token_touch_time = $token->updated_at; //created_at and updated_at are same initially

			//Check if token more than 59 days old
			if( ($now - $token_touch_time) > 60*60*24*59 ) {
				return false;
			}

			return true;

		}

		return false;

	}

	public static function saveToken( User $user, $accessToken )
	{

		//Delete all old tokens
		$tokens = Model_Linkedin_token::find(array(
			'user_id' => $user->getId()
		));

		if( count($tokens) ) {
			$newToken = $tokens[0];
		}else{
			$newToken = new Model_Linkedin_token();
		}

		$newToken->user_id = $user->getId();
		$newToken->token = $accessToken;
		if( !count($tokens) ) {
			$newToken->created_at = time();
		}
		$newToken->updated_at = time();
		$newToken->save();

		return true;

	}

	public static function saveProfile( User $user, $fields )
	{

		if( !count($fields) || !array_key_exists('id', $fields) ) {
			return false;
		}

		$profiles = Model_Linkedin_profile::find(array(
			'user_id' => $user->getId()
		));

		if( count($profiles) ) {
			$profile = $profiles[0];
		}else{
			$profile = new Model_Linkedin_profile();
		}

		$profile->user_id = $user->getId();
		$profile->first_name = $fields['firstName'];
		$profile->last_name = $fields['lastName'];
		$profile->formatted_name = $fields['formattedName'];
		$profile->headline = $fields['headline'];
		$profile->industry = $fields['industry'];
		$profile->num_connections = $fields['numConnections'];
		$profile->summary = $fields['summary'];
		$profile->specialties = $fields['specialties'];
		$profile->picture_url = $fields['pictureUrl'];
		$profile->linkedin_id = $fields['id'];

		if( !count($profiles) ) {
			$profile->created_at = time();
		}

		$profile->updated_at = time();
		$profile->save();

		return true;

	}

	public static function updateConnections( User $user ) {

		if( !self::haveValidToken($user) ) {
			return false;
		}

		$token = self::getAccessToken( $user );

		$result = self::callApi( $token, 'people',
			'~/connections:(id,headline,first-name,last-name)'
		);

		if( !count($result) ) {
			return false;
		}

		if( array_key_exists('errorCode', $result) ) {
			//Possibly, not enough rights to fetch connections
			//TODO: handle this situation
			return false;
		}

		//Delete all user connections
		$connections = Model_Linkedin_connection::find(array(
			'user_id' => $user->getId()
		));

		foreach($connections as $connection) {
			$connection->delete();
		}

		//Store new connections
		if( count($result['values']) ) {
			foreach( $result['values'] as $value ) {
				$connection = new Model_Linkedin_connection();
				$connection->user_id = $user->getId();
				$connection->first_name = $value['firstName'];
				$connection->last_name = $value['lastName'];
				$connection->headline = $value['headline'];
				$connection->linkedin_id = $value['id'];
				$connection->save();
			}
		}

		return true;

	}

	public static function getRedirectUrl()
	{
		global $wgServer, $wgScriptPath;
		return $wgServer.$wgScriptPath.'/'.'index.php/Special:LinkedInEntry';
	}

	public static function getAccessToken( $user ) {

		if( self::haveValidToken($user) ) {

			$token = Model_Linkedin_token::find(array(
				'user_id' => $user->getId()
			));

			if(!count($token)) {
				return false;
			}

			$token = $token[0];

			return $token->token;

		}

		return false;

	}

	public static function getUserProfile( User $user )
	{

		$profiles = Model_Linkedin_profile::find(array(
			'user_id' => $user->getId()
		));

		if( !count($profiles) ) {
			return false;
		}

		return $profiles[0];

	}

	public static function acquireAccessToken( $code ) {

		global $wgLinkedInDataSettings;

		$postdata = http_build_query( array(
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => self::getRedirectUrl(),
			'client_id' => $wgLinkedInDataSettings['client_id'],
			'client_secret' => $wgLinkedInDataSettings['client_secret']
		) );

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, 'https://www.linkedin.com/uas/oauth2/accessToken' );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $postdata );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		$ret = curl_exec( $curl );
		curl_close( $curl );

		return json_decode( $ret, true );
	}

	public static function callApi( $token, $method, $params ) {

		$url = 'https://api.linkedin.com/v1/'.$method.'/'.$params;
		$url .= '?oauth2_access_token='.$token;
		$url .= '&format=json';

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$ret = curl_exec($curl);
		curl_close($curl);

		return json_decode($ret, true);

	}

}