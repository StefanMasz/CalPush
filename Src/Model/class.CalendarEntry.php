<?php

class CalendarEntry {

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
    private $discrption;

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
    public function getDiscrption()
    {
        return $this->discrption;
    }

    /**
     * @param string $discrption
     */
    public function setDiscrption($discrption)
    {
        $this->discrption = $discrption;
    }

}