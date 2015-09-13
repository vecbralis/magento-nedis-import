<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Model_Reader_Xml_CatalogReader implements \Iterator
{
    /**
     * Xml Reader
     * 
     * @var XMLReader
     */
    protected $reader;

    /**
     * File to read
     * 
     * @var string
     */
    protected $file;

    /**
     * Position
     * 
     * @var integer
     */
    protected $position;

    /**
     * Constructor
     * 
     * @param string $file File to read
     */
    public function __construct($file)
    {
        $this->file = $file;

        if (!file_exists($this->file)) {
            throw new RuntimeException('File not exist');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;

        $this->reader = new XMLReader();
        $this->reader->open($this->file);

        $this->reader->read();
        $this->reader->next();
        $this->reader->read();
        $this->reader->next();
        $this->reader->next();

        while ($this->reader->read() && $this->reader->name !== 'product');
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $node = new SimpleXMLElement($this->reader->readOuterXML());

        return $this->createModelInstance($node);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;

        $this->reader->next('product');
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return 'product' === $this->reader->name;
    }

    /**
     * Extract the XML node and create a new model instance
     * 
     * @param SimpleXMLElement $node XML node
     * 
     * @return Nicovogelaar_Nedisimport_Model_Entity_Product
     */
    protected function createModelInstance(SimpleXMLElement $node)
    {
        $sku = (string) $node->nedisPartnr;
        $name = (string) $node->headerText;
        $shortDescription = (string) $node->internetText;
        $description = (string) $node->generalText;
        $price = (float) $node->goingPriceInclVAT;
        $categories = $this->getCategories($node->categories);
        $qty = (int) $node->stock->inStockLocal;
        $inStock = $qty > 0 ? 1 : 0;

        $product = new Nicovogelaar_Nedisimport_Model_Entity_Product();
        $product->setSku($sku)
            ->setName($name)
            ->setDescription($description)
            ->setShortDescription($shortDescription)
            ->setPrice($price)
            ->setMsrp($price)
            ->setCategories($categories)
            ->setStockQty($qty)
            ->setIsInStock($inStock);

        return $product;
    }

    /**
     * Get the categories
     * 
     * @param SimpleXMLElement $node XML node
     * 
     * @return array
     */
    protected function getCategories($node)
    {
        $categories = array();

        foreach ($node->tree as $tree) {
            $path = array();

            foreach ($tree->entry as $entry) {
                $attributes = $entry->attributes();

                $id = (int) $attributes['id'];
                $depth = (int) $attributes['depth'];
                $name = (string) $entry;

                $category = new Nicovogelaar_Nedisimport_Model_Entity_Category();
                $category->setId($id);
                $category->setDepth($depth);
                $category->setName($name);

                $path[] = $category;
            }

            $categories[] = $path;
        }

        return $categories;
    }
}