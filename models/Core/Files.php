<?php

namespace Models\Core;

class Files extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;

    /**
     *
     * @var integer
     */
    protected $user_id;

    /**
     *
     * @var string
     */
    protected $original_name;

    /**
     *
     * @var integer
     */
    protected $directory_id;

    /**
     *
     * @var string
     */
    protected $temp_name;

    /**
     *
     * @var integer
     */
    protected $size;

    /**
     *
     * @var string
     */
    protected $type;

    /**
     *
     * @var integer
     */
    protected $public;

    /**
     *
     * @var string
     */
    protected $creation_time;

    /**
     *
     * @var string
     */
    protected $modification_time;


    public static function generateTemporaryName($user_id, $filename, $directory){
        return sha1(
            $user_id .
            $filename .
            $directory .
            \Helpers\Consts::appSecretKey .
            (new \DateTime())->format(\Helpers\Consts::mysqlDateTimeColumnFormat)
        );
    }


    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Method to set the value of field original_name
     *
     * @param string $original_name
     * @return $this
     */
    public function setOriginalName($original_name)
    {
        $this->original_name = $original_name;

        return $this;
    }

    /**
     * Method to set the value of field directory_id
     *
     * @param integer $directory_id
     * @return $this
     */
    public function setDirectoryId($directory_id)
    {
        $this->directory_id = $directory_id;

        return $this;
    }

    /**
     * Method to set the value of field temp_name
     *
     * @param string $temp_name
     * @return $this
     */
    public function setTempName($temp_name)
    {
        $this->temp_name = $temp_name;

        return $this;
    }

    /**
     * Method to set the value of field size
     *
     * @param integer $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Method to set the value of field type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Method to set the value of field public
     *
     * @param integer $type
     * @return $this
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }


    /**
     * Method to set the value of field creation_time
     *
     * @param string $creation_time
     * @return $this
     */
    public function setCreationTime($creation_time)
    {
        $this->creation_time = $creation_time;

        return $this;
    }

    /**
     * Method to set the value of field modification_time
     *
     * @param string $creation_time
     * @return $this
     */
    public function setModificationTime($modification_time)
    {
        $this->modification_time = $modification_time;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field original_name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->original_name;
    }

    /**
     * Returns the value of field directory_id
     *
     * @return integer
     */
    public function getDirectoryId()
    {
        return $this->directory_id;
    }

    /**
     * Returns the value of field temp_name
     *
     * @return string
     */
    public function getTempName()
    {
        return $this->temp_name;
    }

    /**
     * Returns the value of field size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Returns the value of field type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the value of field public
     *
     * @return integer
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Returns the value of field creation_time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->creation_time;
    }

    /**
     * Returns the value of field modification_time
     *
     * @return string
     */
    public function getModificationTime()
    {
        return $this->modification_time;
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'original_name' => 'original_name', 
            'directory_id' => 'directory_id', 
            'temp_name' => 'temp_name', 
            'size' => 'size',
            'type' => 'type',
            'public' => 'public',
            'creation_time' => 'creation_time',
            'modification_time' => 'modification_time'
        );
    }

}
