<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Res/constants.php';
require_once __DIR__ . '/class.localDatesController.php';

/**
 * Class calpushController
 */
class calpushController
{

    /**
     * @var Google_Client
     */
    private $googleClient = null;

    /**
     * @var localDatesController;
     */
    private $localDatesController = null;

    /**
     * updates the configured calendars
     */
    public function updateCalendar()
    {
        $localDates = $this->getLocalDatesController()->getDates();
        $this->syncWithGoogleCalendar($localDates);
        if (false === $this->sendStatusMail()){
            echo 'mailversand fehlgeschlagen';
        } else {
            echo 'alles duffte';
        }
    }

    /**
     * @param array $localDates
     */
    private function syncWithGoogleCalendar($localDates)
    {
        $service = new Google_Service_Calendar($this->getGoogleClient());
        $calendarList = $service->calendarList->listCalendarList();
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

    /**
     * @return localDatesController
     */
    private function getLocalDatesController(){
        if (null === $this->localDatesController){
            $this->localDatesController = new localDatesController();
        }
        return $this->localDatesController;
    }

    /**
     * @return boolean
     */
    private function sendStatusMail()
    {
        return mail('stefanmasz@hotmail.com','Debug','Text folgt');
    }

}
