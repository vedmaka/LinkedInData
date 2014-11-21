<?php
/**
 * Created by PhpStorm.
 * User: vedmaka
 * Date: 12.11.2014
 * Time: 11:15
 */

class LinkedInDataSpecial extends SpecialPage {

	function __construct() {
		// TODO: Implement __construct() method.
		parent::__construct("LinkedInData");
	}

	public function execute( $subPage ) {

		global $wgLinkedInDataSettings, $wgServer;

		$out = $this->getOutput();

		if( $this->getUser()->isAnon() ) {
			$out->addHTML('You need to login to import LinkedIn data.');
			return;
		}

		if( $this->getRequest()->getVal('code') ) {

			//Auth callback


			$result = $this->getAccessToken( $this->getRequest()->getVal('code') );
			if( !$result || !array_key_exists('access_token', $result) ) {
				$out->addHTML('Unknown error occurred.');
				return;
			}

			$access_token = $result['access_token'];
			$expires_in = $result['expires_in'];

			//Proceed to import

			$info = $this->callApi($access_token, 'people', '~:(id,first-name,last-name,maiden-name,formatted-name,phonetic-first-name,phonetic-last-name,formatted-phonetic-name,headline,location,industry,distance,current-status,current-share,num-connections,num-connections-capped,summary,specialties,positions,picture-url,public-profile-url)');

			//Print out information

			$table = '<table class="wikitable">';
			$table .= '<tr>';
			$table .= '<td style="font-weight: bold;">LinkedIn id</td>';
			$table .= '<td>'.$info['id'].'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">First name</td>';
			$table .= '<td>'.$info['firstName'].'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Last name</td>';
			$table .= '<td>'.$info['lastName'].'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Formatted name</td>';
			$table .= '<td>'.$info['formattedName'].'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Headline</td>';
			$table .= '<td>'.$info['headline'].'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Industry</td>';
			$table .= '<td>'.$info['industry'].'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Location</td>';
			$table .= '<td>'.$info['location']['name'].'('.$info['location']['country']['code'].')'.'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Number of connections</td>';
			$table .= '<td>'.$info['numConnections'].'</td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Picture</td>';
			$table .= '<td><img src="'.$info['pictureUrl'].'" /></td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Profile url</td>';
			$table .= '<td><a href="'.$info['publicProfileUrl'].'">'.$info['publicProfileUrl'].'</a></td>';

			$table .= '</tr><tr>';

			$table .= '<td style="font-weight: bold;">Positions</td>';
			$table .= '<td>';
			if( $info['positions']['_total'] > 0 ) {
				foreach( $info['positions']['values'] as $pos ) {
					$table .= '<p>';
					$table .= '<ul>';
					$table .= '<li>ID: '.$pos['id'].'</li>';
					$table .= '<li>Company name: '.$pos['company']['name'].'</li>';
					$table .= '<li>Is current: '.$pos['isCurrent'].'</li>';
					$table .= '<li>Start year: '.$pos['startDate']['year'].'</li>';
					$table .= '<li>Summary: '.$pos['summary'].'</li>';
					$table .= '<li>Title: '.$pos['title'].'</li>';
					$table .= '</ul>';
					$table .= '</p>';
				}
			}
			$table .= '</td>';

			$table .= '</tr><tr>';

			if( $info['currentShare'] ) {
				$table .= '<td style="font-weight: bold;">Current share</td>';
				$table .= '<td>';
				$table .= '<p>';
				$table .= '<ul>';
				if( $info['currentShare']['author'] ) {
					$table .= '<li>Author name: ' . $info['currentShare']['author']['firstName'] . ' ' . $info['currentShare']['author']['lastName'] . '</li>';
				}
				$table .= '<li>Comment: ' . $info['currentShare']['comment'] . '</li>';
				$table .= '<li>ID: ' . $info['currentShare']['id'] . '</li>';
				$table .= '<li>Visibility: ' . $info['currentShare']['visibility']['code'] . '</li>';
				//$table .= '<li>Content: ' . $info['currentShare']['visibility']['code'] . '</li>';
				$table .= '</ul>';
				$table .= '</p>';
				$table .= '</td>';

			}

			$table .= '</tr>';
			$table .= '</table>';

			$out->addHTML($table);

			//Connections

			$info = $this->callApi($access_token, 'people', '~/connections:(id,headline,first-name,last-name,formatted-name,location,industry,distance,current-share,summary,specialties,positions,picture-url,public-profile-url)' );
			if( count($info) ) {

				$out->addHTML('<br><h2>Connections, total '.$info['_total'].':</h2>');

				$out->addHTML('<pre style="font-size: 11px;">');
				$out->addHTML(print_r($info['values'],1));
				$out->addHTML('</pre>');

			}

		}elseif( $this->getRequest()->getVal('error') ){

			$out->addHTML('Error occurred: '.$this->getRequest()->getVal('error'));

		}else{

			$form = '<div id="linkedin-form"><form action="https://www.linkedin.com/uas/oauth2/authorization" method="get"> ';
			$form .= '<input name="response_type" type="hidden" value="code" />';
			$form .= '<input name="client_id" type="hidden" value="'.$wgLinkedInDataSettings['client_id'].'" />';
			$form .= '<input name="redirect_uri" type="hidden" value="'.$wgServer.'/index.php/Special:LinkedInData'.'" />';
			$form .= '<input name="state" type="hidden" value="'.md5(microtime()).'" />';
			$form .= '<input name="scope" type="hidden" value="r_basicprofile,r_network" />';
			$form .= '<input name="submit" type="submit" value="Import data" />';
			$form .= '</form></div>';
			$out->addHTML($form);

		}

	}

	private function callApi( $token, $method, $params ) {

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

	private function getAccessToken( $code ) {

		global $wgLinkedInDataSettings, $wgServer;

		$postdata = http_build_query(
			array(
				'grant_type' => 'authorization_code',
				'code' => $this->getRequest()->getVal('code'),
				'redirect_uri' => $wgServer.'/index.php/Special:LinkedInData',
				'client_id' => $wgLinkedInDataSettings['client_id'],
				'client_secret' => $wgLinkedInDataSettings['client_secret']
			)
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://www.linkedin.com/uas/oauth2/accessToken');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$ret = curl_exec($curl);
		curl_close($curl);

		return json_decode($ret, true);
	}

}