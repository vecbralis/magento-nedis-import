<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Model_Entity_Category
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $depth;

    /**
     * @var string
     */
    protected $name;

    /**
     * Gets the value of id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id
     *
     * @param integer $id the id
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Category
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of depth
     *
     * @return integer
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Sets the value of depth
     *
     * @param integer $depth the depth
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Category
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Gets the value of name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name
     *
     * @param string $name the name
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}