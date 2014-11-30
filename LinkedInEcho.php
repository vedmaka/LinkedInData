<?php

$wgHooks['LinkedInData_profile_created'][] = 'LinkedInEcho::onProfileCreated';


class EchoLinkedInDataFormatter extends EchoBasicFormatter {
}

class LinkedInEcho {
//wfRunHooks('LinkedInData_profile_created', array( $profile, $friends ));

    /**
     * @param Model_Linkedin_profile $profile that have been created
     * @param $friends int[] - wiki userids. List of users (friends) to notify about the creation of the new profile.
     */
    public static function onProfileCreated(Model_Linkedin_profile $profile, $friends) {
        global $wgEchoNotificationCategories;


        EchoEvent::create( array(
            'type' => 'linkedin-data',
            'title' => $profile->first_name . $profile->last_name . "has connected to a wiki",
            'agent' => User::newFromId($profile->user_id),
        ) );
        $profile->user_id;

    }

    /**
     * Add user to be notified on echo event
     * @param $event EchoEvent
     * @param $users array
     * @return bool
     */
    public static function onEchoGetDefaultNotifiedUsers( $event, &$users ) {
        switch ( $event->getType() ) {
            case 'edit-thank':
            case 'flow-thank':
                $extra = $event->getExtra();
                if ( !$extra || !isset( $extra['thanked-user-id'] ) ) {
                    break;
                }
                $recipientId = $extra['thanked-user-id'];
                $recipient = User::newFromId( $recipientId );
                $users[$recipientId] = $recipient;
                break;
        }
        return true;
    }


} 