<?php
/**
 * Created by PhpStorm.
 * User: vedmaka
 * Date: 21.11.2014
 * Time: 19:48
 */

class Model_Linkedin_connection extends Model {

	protected static $table = 'linkedin_data_connections';

	protected $properties = array(
		'user_id'  => 'int',
		'first_name'  => 'string',
		'last_name' => 'string',
		'headline'  => 'string',
		'linkedin_id'  => 'string'
	);

}