<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Model_Processor_ProductProcessor
{
    const ROOT_CATEGORY_NAME = 'Nedis Category';
    const ATTRIBUTE_SET_NAME = 'Nedis';

    /**
     * Root category
     * 
     * @var Mage_Catalog_Model_Category
     */
    protected $rootCategory;

    /**
     * Attribute set Id
     * 
     * @var integer
     */
    protected $attributeSetId;

    /**
     * Catalog product entity type Id
     * 
     * @var integer
     */
    protected $entityTypeId;

    /**
     * All category url key paths
     * 
     * @var array
     */
    protected $categoryUrlKeyPaths;

    /**
     * Process product data
     * 
     * @param Nicovogelaar_Nedisimport_Model_Entity_Product $product Product model
     * 
     * @return void
     */
    public function process(Nicovogelaar_Nedisimport_Model_Entity_Product $product)
    {
        $magentoProduct = $this->getProduct($product);

        if ($magentoProduct) {
            $this->updateProduct($magentoProduct, $product);
        } else {
            $this->createProduct($product);
        }
    }

    /**
     * Get the magento product model
     * 
     * @param Nicovogelaar_Nedisimport_Model_Entity_Product $product Product
     * 
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct(Nicovogelaar_Nedisimport_Model_Entity_Product $product)
    {
        return Mage::getModel('catalog/product')->loadByAttribute('sku', $product->getSku());
    }

    /**
     * Create new product
     * 
     * @param Nicovogelaar_Nedisimport_Model_Entity_Product $product Product
     * 
     * @return Mage_Catalog_Model_Product
     */
    protected function createProduct(Nicovogelaar_Nedisimport_Model_Entity_Product $product)
    {
        $categoryIds = $this->createCategories($product->getCategories());
        $attributeSetId = $this->getAttributeSetId();

        $magentoProduct = Mage::getModel('catalog/product')
            ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->setAttributeSetId($attributeSetId)
            ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->setCreatedAt(strtotime('now'))
            ->setUpdatedAt(strtotime('now'))
            ->setSku($product->getSku())
            ->setName($product->getName())
            //->setWeight(4.0000)
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->setTaxClassId(4) // tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            //->setManufacturer(28) // manufacturer id
            //->setColor(24)
            //->setNewsFromDate('06/26/2014') // product set as new from
            //->setNewsToDate('06/30/2014') // product set as new to
            ->setCountryOfManufacture('NL') // country of manufacture (2-letter country code)
            ->setPrice($product->getPrice()) // price in form 11.22
            //->setCost(22.33) // price in form 11.22
            //->setSpecialPrice(00.44) // special price in form 11.22
            //->setSpecialFromDate('06/1/2014') // special price from (MM-DD-YYYY)
            //->setSpecialToDate('06/30/2014') // special price to (MM-DD-YYYY)
            ->setMsrpEnabled(1) // enable MAP
            ->setMsrpDisplayActualPriceType(1) // display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
            ->setMsrp($product->getMsrp()) // Manufacturer's Suggested Retail Price
            //->setMetaTitle('test meta title 2')
            //->setMetaKeyword('test meta keyword 2')
            //->setMetaDescription('test meta description 2')
            ->setDescription($product->getDescription())
            ->setShortDescription($product->getShortDescription())
            //->setMediaGallery(array('images' => array(), 'values' => array ())) // media gallery initialization
            //->addImageToMediaGallery('media/catalog/product/1/0/10243-1.png', array('image','thumbnail','small_image'), false, false) // assigning image, thumb and small image to media gallery
            ->setStockData(
                array(
                   'use_config_manage_stock' => 0, // 'Use config settings' checkbox
                   'manage_stock' => 1, // manage stock
                   'min_sale_qty' => 1, // Minimum Qty Allowed in Shopping Cart
                   // 'max_sale_qty' => 2, // Maximum Qty Allowed in Shopping Cart
                   'is_in_stock' => $product->getIsInStock(),
                   'qty' => $product->getStockQty()
               )
            )
            ->setCategoryIds($categoryIds)
        ;

        $this->save($magentoProduct);
    }

    /**
     * Update product
     * 
     * @param Mage_Catalog_Model_Product                    $magentoProduct Magento product
     * @param Nicovogelaar_Nedisimport_Model_Entity_Product $product        Product
     * 
     * @return void
     */
    protected function updateProduct(Mage_Catalog_Model_Product $magentoProduct,
        Nicovogelaar_Nedisimport_Model_Entity_Product $product
    ) {
        $categoryIds = $magentoProduct->getCategoryIds();
        $newCategoryIds = $this->createCategories($product->getCategories());

        $categoryIds = array_unique(array_merge($categoryIds, $newCategoryIds));

        $magentoProduct
            ->setUpdatedAt(strtotime('now'))
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setSku($product->getSku())
            ->setName($product->getName())
            ->setMsrp($product->getMsrp())
            ->setDescription($product->getDescription())
            ->setShortDescription($product->getShortDescription())
            ->setCategoryIds($categoryIds)
        ;

        $stockItem = $magentoProduct->getStockItem();

        if ($stockItem) {
            $stockItem->setQty($product->getStockQty());
            $stockItem->setIsInStock($product->getIsInStock());
        } else {
            $this->createStockItem($magentoProduct, $product);
        }

        $this->save($magentoProduct);
    }

    /**
     * Create stock item
     * 
     * @param Mage_Catalog_Model_Product                    $magentoProduct Magento product
     * @param Nicovogelaar_Nedisimport_Model_Entity_Product $product        Product
     * 
     * @return void
     */
    protected function createStockItem(Mage_Catalog_Model_Product $magentoProduct,
        Nicovogelaar_Nedisimport_Model_Entity_Product $product
    ) {
        $stockItem = Mage::getModel('cataloginventory/stock_item')
            ->assignProduct($magentoProduct)
            ->setUseConfigManageStock(0)
            ->setManageStock(1)
            ->setMinSaleQty(1)
            ->setQty($product->getStockQty())
            ->setIsInStock($product->getIsInStock())
        ;

        $this->save($stockItem);
    }

    /**
     * Save object
     * 
     * @param Mage_Core_Model_Abstract $object Product
     * 
     * @return void
     */
    protected function save(Mage_Core_Model_Abstract $object)
    {
        try {
            $object->save();
        } catch (Zend_Db_Statement_Exception $e) {
            throw $e;
        }
    }

    /**
     * Get or create the root category
     * 
     * @return Mage_Catalog_Model_Category
     */
    protected function getOrCreateRootCategory()
    {
        if (null === $this->rootCategory) {
            $collection = Mage::getResourceModel('catalog/category_collection')
                ->addFieldToFilter('name', self::ROOT_CATEGORY_NAME);

            $this->rootCategory = count($collection) ?
                $collection->getFirstItem() : 
                $this->createRootCategory();
        }

        return $this->rootCategory;
    }

    /**
     * Create the root category
     * 
     * @return Mage_Catalog_Model_Category
     */
    protected function createRootCategory()
    {
        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->setName(self::ROOT_CATEGORY_NAME)
            ->setIsActive(1)
            ->setDisplayMode('PRODUCTS')
            ->setPath('1')
            ->save();

        return $category;
    }

    /**
     * Create categories
     * 
     * @param Nicovogelaar_Nedisimport_Model_Entity_Category[][] $categories Category paths
     * 
     * @return array
     */
    protected function createCategories(array $categories)
    {
        $categoryIds = array();

        $root = $this->getOrCreateRootCategory();
        $parentIds = array($root->getParentId(), $root->getId());

        foreach ($categories as $categoryPath) {
            $categoryId = null;
            $path = $parentIds;
            $urlKeys = array();
            foreach ($categoryPath as $category) {
                $urlKeys[] = $this->formatUrlKey($category->getName());
                $urlKeysPath = implode('/', $urlKeys);
                $categoryId = $this->getCategoryIdByUrlKeyPath($urlKeysPath);
                if (false === $categoryId) {
                    $magentoCategory = $this->createCategory($category, $path);
                    $categoryId = $magentoCategory->getId();
                    $this->categoryUrlKeyPaths[$categoryId] = $urlKeysPath;
                }
                $path[] = $categoryId;
            }
            if ($categoryId > 0) {
                $categoryIds[] = $categoryId;
            }
        }

        return $categoryIds;
    }

    /**
     * Create category
     * 
     * @param Nicovogelaar_Nedisimport_Model_Entity_Category $category Category
     * @param array                                          $path     Path
     * 
     * @return Mage_Catalog_Model_Category
     */
    protected function createCategory(Nicovogelaar_Nedisimport_Model_Entity_Category $category, array $path)
    {
        $name = $category->getName();
        $urlKey = $this->formatUrlKey($name);

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->setName($name)
            ->setUrlKey($urlKey)
            ->setIsActive(1)
            ->setDisplayMode('PRODUCTS')
            ->setIsAnchor(1)
            ->setPath(implode('/', $path))
            ->save()
        ;

        return $category;
    }

    /**
     * Format url key
     * 
     * @param string $string Format url key
     * 
     * @return string
     */
    protected function formatUrlKey($string)
    {
        return Mage::getModel('catalog/product_url')->formatUrlKey($string);
    }

    /**
     * Get category Id for the specified url key path
     * 
     * @param string $path Url key path
     * 
     * @return integer|boolean
     */
    protected function getCategoryIdByUrlKeyPath($urlKeyPath)
    {
        if (null === $this->categoryUrlKeyPaths) {
            $rootCategory = $this->getOrCreateRootCategory();
            $category = Mage::getModel('nicovogelaar_nedisimport/category');
            $pathFilter = '/' . $rootCategory->getId() . '/?';

            $this->categoryUrlKeyPaths = $category->getUrlKeyPaths($pathFilter);
        }

        return array_search($urlKeyPath, $this->categoryUrlKeyPaths);
    }

    /**
     * Get attribute set Id
     * 
     * @return integer
     */
    protected function getAttributeSetId()
    {
        if (null === $this->attributeSetId) {
            $attributeSet = $this->getAttributeSetByName(self::ATTRIBUTE_SET_NAME);

            if (!$attributeSet) {
                $attributeSet = $this->createAttributeSet();
            }

            $this->attributeSetId = $attributeSet->getId();
        }

        return $this->attributeSetId;
    }

    /**
     * Create attribute set
     * 
     * @return Mage_Eav_Model_Entity_Attribute_Set
     */
    protected function createAttributeSet()
    {
        $entityTypeId = $this->getCatalogProductEntityTypeId();

        $attributeSet = Mage::getModel('eav/entity_attribute_set');
        $attributeSet->setEntityTypeId($entityTypeId);
        $attributeSet->setAttributeSetName(self::ATTRIBUTE_SET_NAME);
        $attributeSet->save();

        $attributeSet->initFromSkeleton($entityTypeId);
        $attributeSet->save();

        return $attributeSet;
    }

    /**
     * Get the attribute set for the specified attribute set name
     * 
     * @param string $name Attribute set name
     * 
     * @return Mage_Eav_Model_Entity_Attribute_Set|null
     */
    protected function getAttributeSetByName($name)
    {
        $attributeSet = null;

        $collection = Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->setEntityTypeFilter($this->getCatalogProductEntityTypeId())
            ->addFieldToFilter('attribute_set_name', $name);

        if (count($collection)) {
            $attributeSet = $collection->getFirstItem();
        }

        return $attributeSet;
    }

    /**
     * Get the catalog product entity type Id
     * 
     * @return integer
     */
    protected function getCatalogProductEntityTypeId()
    {
        if (null === $this->entityTypeId) {
            $this->entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();
        }

        return $this->entityTypeId;
    }
}