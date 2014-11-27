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

    public static function onPageContentSaveComplete( $article, $user, $content, $summary, $isMinor,
        $isWatch, $section, $flags, $revision, $status, $baseRevId )
    {

        if( $article && $status && $status->ok && !$user->isAnon() ) {
            $profile = LinkedInData::getUserProfile( $user );
            if( $profile ) {

                $friends = LinkedInData::findFriends( $user );
                if( count($friends) ) {
                    wfRunHooks( 'LinkedInData_edit_notify', array( $article, $user->getId(), $friends ) );
                }

            }
        }

        return true;

    }

    public static function onOutputPageBeforeHTML( OutputPage &$out, &$text )
    {
        if( $out->getTitle() && $out->getTitle()->getNamespace() == NS_USER ) {

            $user = $out->getUser();

            if( $out->getUser()->getUserPage()->getBaseText() == $out->getTitle()->getBaseText() ) {
                //We are on current user page
            }else{
                //We are on other user page
                $user = User::newFromName( $out->getTitle()->getBaseText() );
            }

            if( $user->getId() == 0 ) {
                return false;
            }

            //Proceed
            $out->clearHTML();
            $page = new LinkedInDataUserPage();
            $page->execute( $user );
            $text = '';

        }

        return true;
    }

}