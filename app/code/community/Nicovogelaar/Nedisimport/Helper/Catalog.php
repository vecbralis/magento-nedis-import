<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Helper_Catalog extends Nicovogelaar_Nedisimport_Helper_Data
{
    /**
     * Handle the uploaded file
     * 
     * @return string|boolean
     */
    public function handleUploadedFile()
    {
        $upload = new Zend_File_Transfer_Adapter_Http();

        $target = $this->getTargetFilename($upload->getFilename());

        $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $target)));

        return $upload->receive() ? $target : false;
    }

    /**
     * Get the target filename to store the catalog file
     * 
     * @param string $filename Filename
     * 
     * @return string
     */
    protected function getTargetFilename($filename)
    {
        $filename = uniqid() . '_' . basename($filename);
        $filename = ltrim($filename, '/ ');

        return $this->getBaseDir() . '/' . $filename;
    }

    /**
     * Process file
     * 
     * @param string $filename Filename
     * 
     * @return boolean|void
     */
    public function processFile($filename)
    {
        $file = $this->getFilePath($filename);

        if ('' == $filename || !file_exists($file)) {
            return false;
        }

        $filename = basename($file);

        $this->saveProgress($filename);

        $suffix = '> /dev/null 2>/dev/null &';
        $script = Mage::getRoot() . "/../shell/nedisimport.php --file '$filename'";

        exec('php ' . $script . ' ' . $suffix);
    }

    /**
     * Delete file
     * 
     * @param string $filename Filename
     * 
     * @return void
     */
    public function deleteFile($filename)
    {
        $file = $this->getFilePath($filename);

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Get the (uploaded) catalog files
     * 
     * @return Varien_Object[]
     */
    public function getFiles()
    {
        $files = array();

        $filenames = scandir($this->getBaseDir());

        foreach ($filenames as $filename) {
            if (strpos($filename, '.') === 0) {
                continue;
            }

            $file = $this->getFilePath($filename);
            $filesize = filesize($file);
            $filemtime = filemtime($file);
            $inProgress = $this->isInProgress($filename);

            $data = array(
                'filename' => $filename,
                'file' => $file,
                'filesize' => $filesize,
                'filemtime' => $filemtime,
                'status' => $inProgress
            );

            $object = new Varien_Object($data);
            $object->setIdFieldName('filename');

            $files[] = $object;
        }

        return $files;
    }

    /**
     * Format bytes
     * 
     * @param integer $bytes     Filesize in bytes
     * @param integer $precision Precision
     * 
     * @return string
     */
    public function formatBytes($bytes, $precision = 2)
    { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 

    /**
     * Get the file path
     * 
     * @param string $filename Filename
     * 
     * @return string
     */
    public function getFilePath($filename)
    {
        return $this->getBaseDir() . '/' . basename($filename);
    }

    /**
     * Get the base dir
     * 
     * @return string
     */
    protected function getBaseDir()
    {
        return Mage::getBaseDir('var') . '/nedisimport/catalog';
    }

    /**
     * Save progress
     * 
     * @param string $filename Filename
     * 
     * @return void
     */
    public function saveProgress($filename)
    {
        $cache = Mage::app()->getCache();

        $cache->save((string) time(), 'nedisimport_' . $filename, array('nedisimport'), 600);
    }

    /**
     * Remove progress
     * 
     * @param string $filename Filename
     * 
     * @return void
     */
    public function removeProgress($filename)
    {
        $cache = Mage::app()->getCache();

        $cache->remove('nedisimport_' . $filename);
    }

    /**
     * Checks if an import is in progress
     * 
     * @param string $filename Filename
     * 
     * @return boolean
     */
    public function isInProgress($filename)
    {
        $cache = Mage::app()->getCache();

        return false !== $cache->load('nedisimport_' . $filename);
    }
}