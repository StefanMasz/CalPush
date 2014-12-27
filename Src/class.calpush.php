<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../Res/constants.php';

class calpush {

    /**
     * @var Google_Client
     */
    private $googleClient;
    
    /**
     * constructor
     */
    public function __construct(){
        
        $this->googleClient = new Google_Client();
        $this->googleClient->setDeveloperKey(DEVELOPERKEY);
        $this->googleClient->setApplicationName(APPLICATIONNAME);
        $this->googleClient->setClientId(CLIENTID);
        $this->googleClient->setClientSecret(CLIENTSECRET);
        $this->googleClient->addScope("https://www.googleapis.com/auth/calendar");

        // This file location should point to the private key file.
        $key = file_get_contents(__DIR__.PATH_TO_CREDANTIALFILE);
        $cred = new Google_Auth_AssertionCredentials(
        // Replace this with the email address from the client.
            CLIENTMAIL,
            // Replace this with the scopes you are requesting.
            array('https://www.googleapis.com/auth/calendar'),
            $key
        );
        $this->googleClient->setAssertionCredentials($cred);        
    }
    /**
     *
     */
    public function pushcalendar(){

        $service = new Google_Service_Calendar($this->googleClient);
        var_dump($service->calendarList->listCalendarList());


    }

}