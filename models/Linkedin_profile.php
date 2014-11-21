<?php
/**
 * Created by PhpStorm.
 * User: vedmaka
 * Date: 21.11.2014
 * Time: 19:48
 */

class Model_Linkedin_profile extends Model {

	protected static $table = 'linkedin_data_profiles';

	protected $properties = array(
		'user_id' => 'int',
		'first_name'  => 'string',
		'last_name'  => 'string',
		'formatted_name' => 'string',
		'headline' => 'string',
		'industry' => 'string',
		'num_connections' => 'int',
		'summary' => 'string',
		'specialties' => 'string',
		'picture_url' => 'string',
		'linkedin_id' => 'string',
		'updated_at' => 'int',
		'created_at'  => 'int'
	);

}