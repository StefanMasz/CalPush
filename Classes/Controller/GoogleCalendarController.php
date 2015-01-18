<?php

class googleCalendarController
{

    /**
     * @var array
     */
    private $eventCache = array();

    /*
     * @var Google_Service_Calendar_CalendarList
     */
    private $calendarList = null;

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
    public function __construct()
    {
        $this->calendarService = new Google_Service_Calendar($this->getGoogleClient());
    }

    /**
     * @param string $title
     * @return Google_Service_Calendar_CalendarListEntry
     * @throws Exception
     */
    public function findGoogleCalendarByTitle($title)
    {
        /** @var Google_Service_Calendar_CalendarListEntry $calendar */
        foreach ($this->getCalendarList()->getItems() as $calendar) {
            if ($title === $calendar->summary) {
                return $calendar;
            }
        }
        throw new Exception('calendar ' . $title . ' not found on remotesite (google)', 1420368033);
    }

    /**
     * @param Google_Service_Calendar_CalendarListEntry $calendarListEntry
     * @return Google_Service_Calendar_Events
     */
    public function getEventList(Google_Service_Calendar_CalendarListEntry $calendarListEntry)
    {
        if (false === array_key_exists($calendarListEntry->getId(), $this->eventCache)) {
            $this->eventCache[$calendarListEntry->getId()] = $this->calendarService->events->listEvents($calendarListEntry->getId());
        }
        return $this->eventCache[$calendarListEntry->getId()];
    }

    /**
     * @param LocalCalendarEntry $localEvent
     * @param Google_Service_Calendar_CalendarListEntry $googleCalendarListEntry
     */
    public function addEvent(LocalCalendarEntry $localEvent, Google_Service_Calendar_CalendarListEntry $googleCalendarListEntry)
    {
        $event = new Google_Service_Calendar_Event();
        $event->setSummary($localEvent->getGroup());

        $googleStart = new Google_Service_Calendar_EventDateTime();
        $googleStart->setDateTime($localEvent->getDate() . 'T' . $localEvent->getStart() . ':00');
        $googleStart->setTimeZone($localEvent->getTimeZone()->getName());
        $googleEnd = new Google_Service_Calendar_EventDateTime();
        $googleEnd->setDateTime($localEvent->getDate() . 'T' . $localEvent->getEnd() . ':00');
        $googleEnd->setTimeZone($localEvent->getTimeZone()->getName());

        $event->setStart($googleStart);
        $event->setEnd($googleEnd);

        try {
            $this->calendarService->events->insert($googleCalendarListEntry->getId(), $event);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @return Google_Service_Calendar_CalendarList
     */
    private function getCalendarList()
    {
        if (null === $this->calendarList) {
            $this->calendarList = $this->calendarService->calendarList->listCalendarList();
        }
        return $this->calendarList;
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