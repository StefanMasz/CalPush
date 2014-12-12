<?php
require_once 'vendor/autoload.php';


class calpush {


    public function pushcalendar(){

        $googleApi = new Google_Client();
        $googleApi->setApplicationName();

    }

    public function maindummy(){

        return "Hallo Welt";

    }

}