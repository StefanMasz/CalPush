<?php

require_once __DIR__ . '/../Model/LocalCalendarEntry.php';

use Kairos\SpreadsheetReader as Reader;


/**
 * Class localEntryController
 * @author Stefan Masztalerz <stefanmasz@hotmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class localEntryController
{

    /**
     * @var array
     */
    private $errorLogMessages = array();

    /**
     * @return array
     */
    public function getDates()
    {
        $this->copyToLocal();
        $reader = $this->readSheetFromlocalOds();
        $dates = $this->reconstituteDates($reader);
        return $dates;
    }

    /**
     * @return array
     */
    public function getErrorLogMessages()
    {
        return $this->errorLogMessages;
    }

    /**
     * copies ods-file from Dropbox to local
     * @return boolean
     */
    private function copyToLocal()
    {
        $ExtFileLine = file(ODS_DROPBOX_LINK);
        if (false === $ExtFileLine) {
            die('Read from Dropbox failed');
        }
        $ExtFile = '';
        foreach ($ExtFileLine as $line) {
            $ExtFile .= $line;
        }
        $IntFile = fopen("local.ods", "wr+");
        fwrite($IntFile, $ExtFile);
        fclose($IntFile);
        return true;
    }

    /**
     * @return array
     */
    private function readSheetFromlocalOds()
    {
        $reader = new Reader\SpreadsheetReaderODS('local.ods');

        $reader->ChangeSheet(ACTIVE_SHEET);

        return $reader;
    }

    /**
     * this depends highly on the structure of the ods-file
     * @param Reader\SpreadsheetReaderODS $reader
     * @return array
     */
    private function reconstituteDates(Reader\SpreadsheetReaderODS $reader)
    {
        foreach ($reader as $line) {
            if ($line[0] !== 'ja') {
                continue;
            }
            if (!preg_match("/^([0-9]{2})\.([0-9]{2})\.([0-9]{2})$/", $line[2])) {
                $this->errorLogMessages[] = 'Date invalid. ' . $line[2] . ' Should: DD.MM.YY';
                continue;
            }
            if (!preg_match("/^([0-2][0-9]).([0-5][0-9]).*([0-2][0-9]).([0-5][0-9])$/", $line[3])) {
                $this->errorLogMessages[] = 'Time invalid. ' . $line[3] . ' Should: HH:MM - HH:MM';
                continue;
            }

            $dateParts = explode('.', $line[2]);
            $date = '20' . $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];

            $hour1 = substr($line[3], 0, 2);
            $minute1 = substr($line[3], 3, 2);
            $minute2 = substr($line[3], -2);
            $hour2 = substr($line[3], -5, 2);

            $entry = new LocalCalendarEntry();
            if ($line[1] === 'ja') {
                $entry->setCanceled(true);
            } else {
                $entry->setCanceled(false);
            }
            $entry->setDate($date);
            $entry->setStart($hour1 . ':' . $minute1);
            $entry->setEnd($hour2 . ':' . $minute2);
            $entry->setGroup($line[4]);
            $entry->setDescription($line[9]);
            $entry->setLocation($line[6]);

            $dates[] = $entry;

        }
        return $dates;
    }

}
