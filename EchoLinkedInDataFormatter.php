<?php
/**
 * Created by PhpStorm.
 * User: katkov
 * Date: 28/11/14
 * Time: 09:35
 */

class EchoLinkedInDataFormatter extends EchoBasicFormatter {
    public function formatFragment( $details, $event, $user ) {
        $message = $this->getMessage( $details['message'] );

        $editor = $event->getAgent()->getRealName();
        $page = $event->getTitle()->getText();
        $message->params([$editor, $page]);


        return $message;
    }



}