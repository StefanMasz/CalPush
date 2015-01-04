<?php
require_once(__DIR__ . '/../Classes/Model/CalendarEntry.php');


/**
 * Class test
 */
class CalendarEntryTest extends PHPUnit_Framework_TestCase{

    /**
     * @test
     */
    public function isKnownTest(){
        $entry = new CalendarEntry();
        $this->assertFalse($entry->isKnown(array()));
    }
}