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


    public static function onPageContentSaveComplete(WikiPage $article, User $user, Content $content, $summary, $isMinor,
                                                      $isWatch, $section, $flags,Revision $revision,Status $status, $baseRevId)
    {

        if ($article && $status && $status->ok && !$user->isAnon()) {
            $profile = LinkedInData::getUserProfile($user);
            if ($profile) {

                $friendIds = LinkedInData::findFriends($user);
                if (count($friendIds)) {

                    $friends = [];
                    foreach ($friendIds as $friendId) {
                        $friends[] = User::newFromId($friendId);
                    }
                }
                EchoEvent::create( array(
                    'type' => 'linkedin-data-friend-edited',
                    'title' => $article->getTitle(),
                    'agent' => $user,
                    'extra' => array(
                        'friends' => $friends
                    )
                ));

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
        $notificationCategories['linkedin-data'] = array(
            'priority' => 9,
            'tooltip' => 'echo-linkedin-data-tooltip',

        );

        $notifications['linkedin-data-friend-joined'] = array(
            'primary-link' => array( 'message' => 'notification-link-text-view-edit', 'destination' => 'diff' ),
            'category' => 'linkedin-data',
            'group' => 'positive',
            'formatter-class' => 'BasicFormatter',
            'title-message' => 'notification-linkedin-data-friend-joined',
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
            'title-message' => 'notification-linkedin-data-friend-edited',
            'title-params' => array( 'agent', 'title' ),
//            'flyout-message' => 'notification-linkedin-data-friend-edited-flyout',
//            'flyout-params' => array( 'agent', 'title' ),
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