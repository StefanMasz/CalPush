<?php

require_once __DIR__ . '/../Service/GoogleClientService.php';

/**
 * Class googleCalendarController
 * @author Stefan Masztalerz <stefanmasz@hotmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class googleCalendarController
{

    /**
     * @var GoogleClientService
     */
    private $googleClientService = null;

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
     * Constructor - init calendarService
     */
    public function __construct()
    {
        $this->calendarService = new Google_Service_Calendar($this->getGoogleClientService()->getGoogleClient());
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
    public function addEvent(
        LocalCalendarEntry $localEvent,
        Google_Service_Calendar_CalendarListEntry $googleCalendarListEntry)
    {
        $event = new Google_Service_Calendar_Event();
        $event->setSummary($localEvent->getGroup());

        $offset = $localEvent->getOffsetInHoursWithLeadingZero();
        //@TODO expecting positiv offset because only expecting GTM +1 or +2 (germany)

        $googleStart = new Google_Service_Calendar_EventDateTime();
        $googleStart->setDateTime($localEvent->getDate() . 'T' . $localEvent->getStart() . ':00+' . $offset);
        $googleStart->setTimeZone($localEvent->getTimeZone()->getName());
        $googleEnd = new Google_Service_Calendar_EventDateTime();
        $googleEnd->setDateTime($localEvent->getDate() . 'T' . $localEvent->getEnd() . ':00+' . $offset);
        $googleEnd->setTimeZone($localEvent->getTimeZone()->getName());

        $event->setStart($googleStart);
        $event->setEnd($googleEnd);

        $event->setLocation($localEvent->getLocation());
        $event->setDescription($localEvent->getDescription());

        try {
            $this->calendarService->events->insert($googleCalendarListEntry->getId(), $event);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @param LocalCalendarEntry $localEvent
     * @param Google_Service_Calendar_CalendarListEntry $googleCalendarListEntry
     * @param Google_Service_Calendar_Event $remoteEvent
     */
    public function updateEvent(
        LocalCalendarEntry $localEvent,
        Google_Service_Calendar_CalendarListEntry $googleCalendarListEntry,
        Google_Service_Calendar_Event $remoteEvent)
    {

        $offset = $localEvent->getOffsetInHoursWithLeadingZero();
        //@TODO expecting positiv offset because only expecting GTM +1 or +2 (germany)

        $googleStart = new Google_Service_Calendar_EventDateTime();
        $googleStart->setDateTime($localEvent->getDate() . 'T' . $localEvent->getStart() . ':00+' . $offset);
        $googleStart->setTimeZone($localEvent->getTimeZone()->getName());
        $googleEnd = new Google_Service_Calendar_EventDateTime();
        $googleEnd->setDateTime($localEvent->getDate() . 'T' . $localEvent->getEnd() . ':00+' . $offset);
        $googleEnd->setTimeZone($localEvent->getTimeZone()->getName());

        $remoteEvent->setStart($googleStart);
        $remoteEvent->setEnd($googleEnd);

        $remoteEvent->setLocation($localEvent->getLocation());
        $remoteEvent->setDescription($localEvent->getDescription());
        try {
            $this->calendarService->events->update($googleCalendarListEntry->getId(), $remoteEvent->getId(), $remoteEvent);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @param Google_Service_Calendar_CalendarListEntry $googleCalendarListEntry
     * @param Google_Service_Calendar_Event $remoteEvent
     */
    public function deleteEvent(
        Google_Service_Calendar_CalendarListEntry $googleCalendarListEntry,
        Google_Service_Calendar_Event $remoteEvent)
    {
        try {
            $this->calendarService->events->delete($googleCalendarListEntry->getId(), $remoteEvent->getId());
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
     * @return GoogleClientService
     */
    private function getGoogleClientService()
    {
        if (null === $this->googleClientService) {
            $this->googleClientService = new GoogleClientService();
        }
        return $this->googleClientService;
    }

}