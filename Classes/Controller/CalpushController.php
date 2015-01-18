<?php
require_once __DIR__ . '/LocalEntryController.php';
require_once __DIR__ . '/GoogleCalendarController.php';

/**
 * Class calpushController
 * @author Stefan Masztalerz <stefanmasz@hotmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
        $this->syncWithGoogleCalendar($localDates);

        if (false === $this->sendStatusMail()) {
            echo 'mailing failed';
        } else {
            echo 'status mail send successful';
        }
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
        $ignoredFormatingErrors = $this->getLocalDatesController()->getErrorLogMessages();
        if (false === empty($ignoredFormatingErrors)){
            $text .= 'Errors in ods-File - effected lines not imported - please fix' . "\r\n";
            foreach ($ignoredFormatingErrors as $error){
                $text .= $error . "\r\n";
            }
        }
        return mail(ADMIN_MAILS, 'Import successful', $text);
    }

    /**
     * @param array $localDates
     */
    private function syncWithGoogleCalendar($localDates)
    {
        $this->initEventCounter();
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
                        continue; //just a warning - go on
                    } else {
                        throw $e;
                    }
                }
                try {
                    $remoteDate = $this->findDate($localDate, $allEntries);
                } catch (Exception $e){
                    if ($e->getCode() === 1421616577){
                        //remoteDate not found, has to be a new one
                        $remoteDate = false;
                    } else {
                        throw $e;
                    }
                }
                if (false === $remoteDate) {
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
                        $this->getGoogleCalendarController()->deleteEvent($googleCalendarListEntry, $remoteDate);
                        $this->eventCounter['deleted']++;
                    } else {
                        //update
                        $this->getGoogleCalendarController()->updateEvent($localDate, $googleCalendarListEntry, $remoteDate);
                        $this->eventCounter['updated']++;
                    }
                }

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }
    }

    /**
     * @param LocalCalendarEntry $localDate
     * @param Google_Service_Calendar_Events $remoteDates
     * @return Google_Service_Calendar_Event
     * @throws Exception
     */
    private function findDate(LocalCalendarEntry $localDate, Google_Service_Calendar_Events $remoteDates){
        /** @var Google_Service_Calendar_Event $entry */
        foreach ($remoteDates as $entry) {
            $start = new DateTime($entry->getStart()['dateTime']);
            if ($start->format("Y-m-d") === $localDate->getDate() &&
                $entry->getSummary() === $localDate->getGroup()
            ) {
                return $entry;
            }
        }
        throw new Exception('Date not found, has to be new', 1421616577);
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
     * @return void
     */
    private function initEventCounter()
    {
        $this->eventCounter['new'] = 0;
        $this->eventCounter['updated'] = 0;
        $this->eventCounter['deleted'] = 0;
        $this->eventCounter['ignored'] = 0;
    }

}
