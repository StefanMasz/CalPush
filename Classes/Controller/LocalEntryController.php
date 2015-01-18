<?php

require_once __DIR__ . '/../Model/LocalCalendarEntry.php';

use Kairos\SpreadsheetReader as Reader;

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
        $reader = $this->readFirstSheetFromlocalOds();
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
    private function readFirstSheetFromlocalOds()
    {
        $reader = new Reader\SpreadsheetReaderODS('local.ods');

        $reader->ChangeSheet(3);

        return $reader;
    }

    /**
     * @param Reader\SpreadsheetReaderODS $reader
     * @return array
     * @TODO make this configurable - this depends on structure of ods
     */
    private function reconstituteDates(Reader\SpreadsheetReaderODS $reader)
    {
        foreach ($reader as $line) {
            if ($line[0] !== 'ja') {
                continue;
            }
            if (!preg_match("/^([0-9]{2})\.([0-9]{2})\.([0-9]{2})$/", $line[2])) {
                $this->errorLogMessages[] = 'Datum invalide. ' . $line[2] . ' Muss: TT.MM.YY';
                continue;
            }
            if (!preg_match("/^([0-2][0-9]).([0-5][0-9]).*([0-2][0-9]).([0-5][0-9])$/", $line[3])) {
                $this->errorLogMessages[] = 'Uhrzeit invalide. ' . $line[3] . ' Muss: HH:MM - HH:MM';
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
