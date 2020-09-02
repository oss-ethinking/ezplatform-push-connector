<?php

namespace Ethinking\PushConnectorBundle\Entity;

class History
{
    private $id;
    private $userName;
    private $status;
    private $time;

    /**
     * @param $id
     * @param $userName
     * @param $status
     * @param $time
     */
    public function __construct($id, $userName, $status, $time)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->status = $status;
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return json_encode($this->status);
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        if (empty($this->time)) {
            return null;
        }

        return date('d.m.Y H:i:s', $this->time / 1000);
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }
}
