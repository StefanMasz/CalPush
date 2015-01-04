<?php
require_once(__DIR__ . '/../Classes/Model/LocalCalendarEntry.php');


/**
 * Class test
 */
class CalendarEntryTest extends PHPUnit_Framework_TestCase{

    /**
     * @test
     */
    public function isKnownTest(){
        $entry = new LocalCalendarEntry();
        $this->assertFalse($entry->isKnown(array()));
    }
}