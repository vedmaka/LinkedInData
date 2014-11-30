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

    public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
        global $wgUser,$wgTitle;

        $friendIds = array (1,2,3);

        $friends=[];
        foreach($friendIds as $friendId) {
            $friends[]=User::newFromId($friendId);
        }

        EchoEvent::create( array(
            'type' => 'linkedin-data-friend-joined',
            'title' => $wgTitle,
            'agent' => $wgUser,
            'extra' => array(
                'friends' => $friends,
            )
        ));

        EchoEvent::create( array(
            'type' => 'reverted',
            'title' => $wgTitle,
            'extra' => array(
                'revid' => "1",
                'reverted-user-id' => "1",
                'reverted-revision-id' => "12",
                'method' => 'rollback',
            ),
            'agent' => $wgUser,
        ) );
    }

    /**
     * Add user to be notified on echo event
     * @param $event EchoEvent
     * @param $users array
     * @return bool
     */
    public static function onEchoGetDefaultNotifiedUsers( $event, &$users ) {
        global $wgUser;

        //we send the message about the user connected to all his LinkedIn friends
        switch ($event->getType()){
            case 'linkedin-data-friend-joined':
            case 'linkedin-data-friend-edited':
                $extra = $event->getExtra();
                //he has no friends, too bad!
                if(!isset($extra['friends'])){
                    return true;
                }
                $users=$extra['friends'];
        }
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
    /**
     * Add LinkedIn data events to Echo
     *
     * @param $notifications array of Echo notifications
     * @param $notificationCategories array of Echo notification categories
     * @param $icons array of icon details
     * @return bool
     */
    public static function onBeforeCreateEchoEvent( &$notifications, &$notificationCategories, &$icons ) {
        $wgEchoNotificationCategories['linkedin-data'] = array(
            'priority' => 5,
            'no-dismiss' => 'web',
            'tooltip' => 'echo-linkedin-data-tooltip',

        );

        $notifications['linkedin-data-friend-joined'] = array(
            'primary-link' => array( 'message' => 'notification-link-text-view-edit', 'destination' => 'diff' ),
            'category' => 'linkedin-data',
            'group' => 'positive',
            'formatter-class' => 'EchoLinkedInDataFormatter',
            'title-message' => 'notification-linkedin-data-user-created',
            'title-params' => array( 'agent', 'title' ),
            'flyout-message' => 'notification-linkedin-data-user-created-flyout',
            'flyout-params' => array( 'agent', 'title' ),
            'payload' => array( 'summary' ),
            'email-subject-message' => 'notification-linkedin-data-email-subject',
            'email-subject-params' => array( 'agent' ),
            'email-body-batch-message' => 'notification-thanks-email-batch-body',
            'email-body-batch-params' => array( 'agent', 'title' ),
            'icon' => 'thanks',
        );

        $notifications['linkedin-data-friend-edited'] = array(
            'primary-link' => array( 'message' => 'notification-link-text-view-edit', 'destination' => 'diff' ),
            'category' => 'linkedin-data',
            'group' => 'positive',
            'formatter-class' => 'EchoLinkedInDataFormatter',
            'title-message' => 'notification-linkedin-data-user-created',
            'title-params' => array( 'agent', 'title' ),
            'flyout-message' => 'notification-linkedin-data-user-created-flyout',
            'flyout-params' => array( 'agent', 'title' ),
            'payload' => array( 'summary' ),
            'email-subject-message' => 'notification-linkedin-data-email-subject',
            'email-subject-params' => array( 'agent' ),
            'email-body-batch-message' => 'notification-thanks-email-batch-body',
            'email-body-batch-params' => array( 'agent', 'title' ),
            'icon' => 'thanks',
        );

        return true;
    }


}