<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MODULE_NAMESPACE_ALIAS = 'nicovogelaar_nedisimport';

    /**
     * Get config helper
     * 
     * @return Nicovogelaar_Nedisimport_Helper_Config
     */
    public function getConfig()
    {
        return Mage::helper(self::MODULE_NAMESPACE_ALIAS . '/config');
    }

    /**
     * Get config value
     * 
     * @param string  $xmlPath XML Path
     * @param integer $storeId Store Id (optional)
     * 
     * @return mixed
     */
    public function getConfigValue($xmlPath, $storeId = null)
    {
        return Mage::getStoreConfig(self::MODULE_NAMESPACE_ALIAS . '/' . $xmlPath, $storeId);
    }
}