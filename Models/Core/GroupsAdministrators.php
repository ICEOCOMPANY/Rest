<?php

namespace Models\Core;

class GroupsAdministrators extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $group_id;

    /**
     *
     * @var integer
     */
    protected $user_id;

    public function initialize()
    {
        $this->setSource('groups_administrators');
        $this->belongsTo('group_id', 'Core\Models\Groups', 'id',
            array('alias' => 'group')
        );
    }

    /**
     * Method to set the value of field group_id
     *
     * @param integer $group_id
     * @return $this
     */
    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;

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
     * Returns the value of field group_id
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->group_id;
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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'group_id' => 'group_id', 
            'user_id' => 'user_id'
        );
    }

}
