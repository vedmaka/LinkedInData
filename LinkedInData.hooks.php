<?php
/**
 * Hooks class declaration for mediawiki extension LinkedInData
 *
 * @file LinkedInData.hooks.php
 * @ingroup LinkedInData
 */

class LinkedInDataHooks {

    public static function getSchemaUpdates( $updater )
    {

        $dir = __DIR__;

        $updater->addExtensionTable( 'linkedin_data_tokens', $dir.'/schema/linkedin_data_tokens.sql' );
        $updater->addExtensionTable( 'linkedin_data_profiles', $dir.'/schema/linkedin_data_profiles.sql' );

        return true;
    }

}