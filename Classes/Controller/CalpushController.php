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
        /*if (false === $this->sendStatusMail()) {
            echo 'mailversand fehlgeschlagen';
        } else {
            echo 'alles duffte';
        }*/
        echo 'ende';
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
                    echo $e->getMessage();
                    die();
                }
                if (false === $localDate->isKnown($allEntries)) {
                    if ($localDate->isCanceled()) {
                        //ignore
                        $this->eventCounter['ignored']++;
                        continue;
                    }
                    //new
                    $this->getGoogleCalendarController()->addEvent($localDate, $googleCalendarListEntry);
                    $this->eventCounter['new']++;
                } else {
                    if ($localDate->isCanceled()) {
                        //remove
                    } else {
                        //update
                        $this->eventCounter['updated']++;
                    }
                }

            } catch (Exception $e) {
                //@todo log error
                echo $e->getMessage() . "\n\r";
                continue;
            }

        }
        var_dump($this->eventCounter);
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
        return mail('stefanmasz@hotmail.com', 'Debug', 'Text folgt');
    }

}
