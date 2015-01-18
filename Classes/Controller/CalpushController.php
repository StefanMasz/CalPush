<?php
require_once __DIR__ . '/LocalEntryController.php';
require_once __DIR__ . '/GoogleCalendarController.php';

/**
 * Class calpushController
 */
class calpushController
{

    /**
     * @var googleCalendarController
     */
    private $googleCalendarController = null;

    /**
     * @var localEntryController;
     */
    private $localDatesController = null;

    /**
     * @var array;
     */
    private $eventCounter = array();

    /**
     * updates the configured calendars
     */
    public function updateCalendar()
    {
        $localDates = $this->getLocalDatesController()->getDates();
        try {
            $this->syncWithGoogleCalendar($localDates);
        } catch (Exception $e) {
            //log error
            echo $e->getMessage();
        }
        if (false === $this->sendStatusMail()) {
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
        $this->eventCounter['new'] = 0;
        $this->eventCounter['updated'] = 0;
        $this->eventCounter['deleted'] = 0;
        $this->eventCounter['ignored'] = 0;
        /** @var LocalCalendarEntry $localDate */
        foreach ($localDates as $localDate) {
            try {
                if ($localDate->isPast()) {
                    $this->eventCounter['ignored']++;
                    continue;
                }
                try {
                    $googleCalendarListEntry = $this->getGoogleCalendarController()->findGoogleCalendarByTitle($localDate->getGroup());
                    $allEntries = $this->getGoogleCalendarController()->getEventList($googleCalendarListEntry);
                } catch (Exception $e) {
                    echo $e->getMessage(). "\n\r";
                    if ($e->getCode() === 1420368033){
                        continue; //just a warning
                    }
                    die();
                }
                $remoteEntry = $localDate->isKnown($allEntries);
                if (false === $remoteEntry) {
                    if ($localDate->isCanceled()) {
                        //ignore, because not known remote and already canceled
                        $this->eventCounter['ignored']++;
                        continue;
                    }
                    //new
                    $this->getGoogleCalendarController()->addEvent($localDate, $googleCalendarListEntry);
                    $this->eventCounter['new']++;
                } else {
                    if ($localDate->isCanceled()) {
                        //delete
                        $this->getGoogleCalendarController()->deleteEvent($googleCalendarListEntry, $remoteEntry);
                        $this->eventCounter['deleted']++;
                    } else {
                        //update
                        $this->getGoogleCalendarController()->updateEvent($localDate, $googleCalendarListEntry, $remoteEntry);
                        $this->eventCounter['updated']++;
                    }
                }

            } catch (Exception $e) {
                //@todo log error
                echo $e->getMessage() . "\n\r";
                continue;
            }

        }
    }

    /**
     * @return localEntryController
     */
    private function getLocalDatesController()
    {
        if (null === $this->localDatesController) {
            $this->localDatesController = new localEntryController();
        }
        return $this->localDatesController;
    }

    /**
     * @return googleCalendarController
     */
    private function getGoogleCalendarController()
    {
        if (null === $this->googleCalendarController) {
            $this->googleCalendarController = new googleCalendarController();
        }
        return $this->googleCalendarController;
    }

    /**
     * @return boolean
     */
    private function sendStatusMail()
    {
        $text = 'Import successful' . "\r\n";
        foreach ($this->eventCounter as $event => $value){
            $text .= $event .':' . $value . "\r\n";
        }
        return mail(ADMIN_MAILS, 'Import successful', $text);
    }

}
