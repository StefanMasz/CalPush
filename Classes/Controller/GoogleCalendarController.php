<?php

class googleCalendarController {

    /**
     * @var Google_Service_Calendar
     */
    private $calendarService = null;

    /**
     * @var Google_Client
     */
    private $googleClient = null;

    /**
     * Constructor - init calendarService
     */
    public function __construct(){
        $this->calendarService = new Google_Service_Calendar($this->getGoogleClient());
    }

    /**
     * @param string $title
     * @return Google_Service_Calendar_CalendarListEntry
     * @throws Exception
     */
    public function findGoogleCalendarByTitle($title){
        $calendarList = $this->calendarService->calendarList->listCalendarList();

        /** @var Google_Service_Calendar_CalendarListEntry $calendar */
        foreach ($calendarList->getItems() as $calendar){
            if ($title === $calendar->summary){
                return $calendar;
            }
        }
        throw new Exception('calendar '.$title.' not found at remotesite (google)', 1420368033);
    }

    /**
     * @param Google_Service_Calendar_CalendarListEntry $CalendarListEntry
     * @return array
     */
    public function getAllCalendarEntries(Google_Service_Calendar_CalendarListEntry $CalendarListEntry){
        return array();
    }

    /**
     * Initialize Google Client from constants
     * @return Google_Client
     */
    private function getGoogleClient()
    {
        if (null === $this->googleClient) {
            $this->googleClient = new Google_Client();
            $this->googleClient->setDeveloperKey(DEVELOPERKEY);
            $this->googleClient->setApplicationName(APPLICATIONNAME);
            $this->googleClient->setClientId(CLIENTID);
            $this->googleClient->setClientSecret(CLIENTSECRET);
            $this->googleClient->addScope("https://www.googleapis.com/auth/calendar");

            // This file location should point to the private key file.
            $key = file_get_contents(__DIR__ . PATH_TO_CREDANTIALFILE);
            $cred = new Google_Auth_AssertionCredentials(
            // Replace this with the email address from the client.
                CLIENTMAIL,
                // Replace this with the scopes you are requesting.
                array('https://www.googleapis.com/auth/calendar'),
                $key
            );
            $this->googleClient->setAssertionCredentials($cred);
        }
        return $this->googleClient;
    }

}