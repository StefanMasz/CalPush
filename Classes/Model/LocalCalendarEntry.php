<?php

class LocalCalendarEntry
{

    /**
     * @var boolean
     */
    private $canceled;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $start;

    /**
     * @var string
     */
    private $end;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $description;

    /**
     * @var DateTimeZone
     */
    private $timeZone;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->timeZone = new DateTimeZone('Europe/Berlin');
    }

    /**
     * @return DateTimeZone
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @return boolean
     */
    public function isCanceled()
    {
        return $this->canceled;
    }

    /**
     * @param boolean $canceled
     */
    public function setCanceled($canceled)
    {
        $this->canceled = $canceled;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param string $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return string
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param string $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param Google_Service_Calendar_Events $remoteEntries
     * @return boolean|Google_Service_Calendar_Event
     * @todo codesmell: mixed return values
     */
    public function isKnown($remoteEntries)
    {
        /** @var Google_Service_Calendar_Event $entry */
        foreach ($remoteEntries as $entry) {
            $start = new DateTime($entry->getStart()['dateTime']);
            if ($start->format("Y-m-d") === $this->getDate() &&
                $entry->getSummary() === $this->getGroup()
            ) {
                return $entry;
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isPast()
    {
        $yesterday = new DateTime();
        $yesterday->modify("-1 day");
        if ($yesterday > $this->getDateTimeStart()) {
            return true;
        }
        return false;
    }

    /**
     * @return DateTime
     */
    public function getDateTimeStart()
    {
        return new DateTime($this->getDate() . ' ' . $this->getStart());
    }

}
