<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Model_Entity_Product
{
    /**
     * @var string
     */
    protected $sku;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $shortDescription;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $msrp;

    /**
     * Category paths
     * 
     * [
     *   [
     *     Category Level 1,
     *     Category Level 2,
     *     Category Level 3
     *   ],
     *   ..
     * ]
     * 
     * @var Nicovogelaar_Nedisimport_Model_Entity_Category[][]
     */
    protected $categories;

    /**
     * @var array
     */
    protected $stockQty;

    /**
     * @var integer
     */
    protected $isInStock;

    /**
     * Gets the value of sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Sets the value of sku
     *
     * @param string $sku the sku
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

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
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description
     *
     * @param string $description the description
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Sets the value of shortDescription
     *
     * @param string $shortDescription the short description
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Gets the value of price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets the value of price
     *
     * @param float $price the price
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Gets the value of msrp
     *
     * @return float
     */
    public function getMsrp()
    {
        return $this->msrp;
    }

    /**
     * Sets the value of msrp
     *
     * @param float $msrp the msrp
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setMsrp($msrp)
    {
        $this->msrp = $msrp;

        return $this;
    }

    /**
     * Gets the value of categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the value of categories
     *
     * @param Nicovogelaar_Nedisimport_Model_Entity_Category[][] $categories the categories
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Gets the value of stockQty
     *
     * @return array
     */
    public function getStockQty()
    {
        return $this->stockQty;
    }

    /**
     * Sets the value of stockQty
     *
     * @param array $stockQty the stock qty
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setStockQty(array $stockQty)
    {
        $this->stockQty = $stockQty;

        return $this;
    }

    /**
     * Gets the value of isInStock
     *
     * @return integer
     */
    public function getIsInStock()
    {
        return $this->isInStock;
    }

    /**
     * Sets the value of isInStock
     *
     * @param integer $isInStock the is in stock
     *
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    public function setIsInStock($isInStock)
    {
        $this->isInStock = $isInStock;

        return $this;
    }
}