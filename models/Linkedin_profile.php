<?php

/**
 * Created by PhpStorm.
 * User: vedmaka
 * Date: 21.11.2014
 * Time: 19:48
 * @property mixed user_id
 * @property mixed first_name
 * @property mixed last_name
 * @property mixed industry
 * @property mixed formatted_name
 * @property mixed headline
 * @property mixed num_connections
 * @property mixed summary
 * @property mixed specialties
 * @property mixed linkedin_id
 * @property mixed picture_url
 * @property mixed created_at
 * @property mixed updated_at
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