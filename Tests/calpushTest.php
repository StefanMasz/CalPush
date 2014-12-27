<?php
require_once (__DIR__.'/../Src/class.calpush.php');


/**
 * Class test
 */
class calpushTest extends PHPUnit_Framework_TestCase{

    /**
     * test setup
     */
    public function setUp(){
        $this->calpush = new calpush();
    }

    /**
     * @test
     */
    public function pushcalendarTest(){

    }
}