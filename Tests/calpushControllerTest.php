<?php
require_once(__DIR__ . '/../Classes/Controller/CalpushController.php');


/**
 * Class test
 */
class calpushControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var calpushController
     */
    private $calpushController;

    /**
     * test setup
     */
    public function setUp()
    {
        $googleClientMock = $this->getMock('Google_Client');

        $this->calpushController = $this->getMock('calpushController', array('getGoogleClient', 'sendStatusMail'));
        $this->calpushController->expects($this->once())->method('getGoogleClient')->willReturn($googleClientMock);

        $this->calpushController->expects($this->once())->method('sendStatusMail')->willReturn(true);
    }

    /**
     * @test
     */
    public function updateCalendarTest()
    {
        $this->calpushController->updateCalendar();
    }
}