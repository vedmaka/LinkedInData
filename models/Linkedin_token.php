<?php
/**
 * Created by PhpStorm.
 * User: vedmaka
 * Date: 21.11.2014
 * Time: 19:48
 */

class Model_Linkedin_token extends Model {

	protected static $table = 'linkedin_data_tokens';

	protected $properties = array(
		'user_id'  => 'int',
		'token'  => 'string',
		'updated_at' => 'int',
		'created_at'  => 'int'
	);

}