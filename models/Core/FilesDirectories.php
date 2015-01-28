<?php

namespace Models\Core;

class FilesDirectories extends \Phalcon\Mvc\Model
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
    protected $name;

    /**
     *
     * @var integer
     */
    protected $parent;

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
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field parent
     *
     * @param integer $parent
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

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
     * @param string $modification_time
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
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field parent
     *
     * @return integer
     */
    public function getParent()
    {
        return $this->parent;
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
            'name' => 'name', 
            'parent' => 'parent', 
            'creation_time' => 'creation_time', 
            'modification_time' => 'modification_time'
        );
    }

}
