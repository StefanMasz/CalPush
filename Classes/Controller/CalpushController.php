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
     * updates the configured calendars
     */
    public function updateCalendar()
    {
        $localDates = $this->getLocalDatesController()->getDates();
        try {
            $this->syncWithGoogleCalendar($localDates);
        } catch (Exception $e){
            //log error
            echo $e->getMessage();
        }
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

        /** @var CalendarEntry $localDate */
        foreach ($localDates as $localDate){
            try {
                $googleCalendarListEntry = $this->getGoogleCalendarController()->findGoogleCalendarByTitle($localDate->getGroup());
                $allEntries = $this->getGoogleCalendarController()->getAllCalendarEntries($googleCalendarListEntry);
                if (false === $localDate->isKnown($allEntries)){
                    if ($localDate->isCanceled()){
                        //ignore
                        continue;
                    }
                    //new
                } else {
                    //update
                    if ($localDate->isCanceled()){
                        //remove
                    }
                }

            } catch (Exception $e){
                //@todo log error
                echo $e->getMessage() . "\n\r";
                continue;
            }



        }
    }

    /**
     * @return localEntryController
     */
    private function getLocalDatesController(){
        if (null === $this->localDatesController){
            $this->localDatesController = new localEntryController();
        }
        return $this->localDatesController;
    }

    /**
     * @return googleCalendarController
     */
    private function getGoogleCalendarController(){
        if (null === $this->googleCalendarController){
            $this->googleCalendarController = new googleCalendarController();
        }
        return $this->googleCalendarController;
    }

    /**
     * @return boolean
     */
    private function sendStatusMail()
    {
        return mail('stefanmasz@hotmail.com','Debug','Text folgt');
    }

}
