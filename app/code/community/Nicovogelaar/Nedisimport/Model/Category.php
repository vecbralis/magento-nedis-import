<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Model_Category
{
    /**
     * Get category url key paths
     * 
     * @param string $pathFilter Path filter
     * 
     * @return array
     */
    public function getUrlKeyPaths($pathFilter = null)
    {
        $urlKeyPaths = array();

        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('url_key');

        if (null !== $pathFilter) {
            $collection->addPathFilter($pathFilter);
        }

        $categories = $collection->getItems();

        foreach ($categories as $category) {
            $path = array_slice(explode('/', $category->getPath()), 1);
            $urlKeyPath = array();

            foreach($path as $index => $value) {
                $urlKey = $categories[$value]->getUrlKey();
                if (null !== $urlKey) {
                    $urlKeyPath[] = $categories[$value]->getUrlKey();
                }
            }

            if (count($urlKeyPath)) {
                $urlKeyPaths[$category->getId()] = implode('/', $urlKeyPath);
            }
        }

        return $urlKeyPaths;
    }
}