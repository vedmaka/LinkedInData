<?php
/**
 * Initialization file for the LinkedInData extension.
 *
 * @file LinkedInData.php
 * @ingroup LinkedInData
 *
 * @licence GNU GPL v3
 * @author Wikivote llc < http://wikivote.ru >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( version_compare( $wgVersion, '1.17', '<' ) ) {
	die( '<b>Error:</b> This version of LinkedInData requires MediaWiki 1.17 or above.' );
}

global $wgLinkedInData;
$wgLinkedInDataDir = dirname( __FILE__ );

$wgLinkedInDataSettings = array(
    'client_id' => '',
    'client_secret' => ''
);

/* Credits page */
$wgExtensionCredits['specialpage'][] = array(
    'path' => __FILE__,
    'name' => 'LinkedInData',
    'version' => '0.1',
    'author' => 'Wikivote! ltd.',
    'url' => '',
    'descriptionmsg' => 'LinkedInData-credits',
);

/* Resource modules */
$wgResourceModules['ext.LinkedInData.main'] = array(
    'localBasePath' => dirname( __FILE__ ) . '/',
    'remoteExtPath' => 'LinkedInData/',
    'group' => 'ext.LinkedInData',
    'scripts' => '',
    'styles' => ''
);

/* Message Files */
$wgExtensionMessagesFiles['LinkedInData'] = dirname( __FILE__ ) . '/LinkedInData.i18n.php';

/* Autoload classes */
$wgAutoloadClasses['LinkedInData'] = dirname( __FILE__ ) . '/LinkedInData.class.php';
$wgAutoloadClasses['LinkedInDataSpecial'] = dirname( __FILE__ ) . '/LinkedInDataSpecial.php';
$wgAutoloadClasses['LinkedInEntrySpecial'] = dirname( __FILE__ ) . '/LinkedInDataEntry.php';
$wgAutoloadClasses['LinkedInDataHooks'] = dirname( __FILE__ ) . '/LinkedInData.hooks.php';
$wgAutoloadClasses['Model_Linkedin_token'] = dirname( __FILE__ ) . '/models/Linkedin_token.php';
$wgAutoloadClasses['Model_Linkedin_profile'] = dirname( __FILE__ ) . '/models/Linkedin_profile.php';
$wgAutoloadClasses['Model_Linkedin_connection'] = dirname( __FILE__ ) . '/models/Linkedin_connection.php';

/* ORM,MODELS */
#$wgAutoloadClasses['LinkedInData_Model_'] = dirname( __FILE__ ) . '/includes/LinkedInData_Model_.php';

/* ORM,PAGES */
#$wgAutoloadClasses['LinkedInDataSpecial'] = dirname( __FILE__ ) . '/pages/LinkedInDataSpecial/LinkedInDataSpecial.php';

/* Rights */
#$wgAvailableRights[] = 'example_rights';

/* Permissions */
#$wgGroupPermissions['sysop']['example_rights'] = true;

/* Special Pages */
$wgSpecialPages['LinkedInData'] = 'LinkedInDataSpecial';
$wgSpecialPages['LinkedInEntry'] = 'LinkedInEntrySpecial';

/* Hooks */
#$wgHooks['example_hook'][] = 'LinkedInDataHooks::onExampleHook';
$wgHooks['LoadExtensionSchemaUpdates'][] = 'LinkedInDataHooks::getSchemaUpdates';