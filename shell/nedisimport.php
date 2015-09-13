<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

$dir = __DIR__ . '/../../../../shell';

if (file_exists($dir . '/abstract.php')) {
    require_once $dir . '/abstract.php';
} else {
    require_once 'abstract.php';
}

ini_set('memory_limit', '512M');

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Shell_Nedisimport extends Mage_Shell_Abstract
{
    /**
     * @var Nicovogelaar_Nedisimport_Helper_Catalog
     */
    protected $_helper;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $filename = $this->getArg('file');
        $offset = $this->getArg('offset');
        $output = $this->getArg('output');

        $this->_helper = Mage::helper('nicovogelaar_nedisimport/catalog');

        $file = $this->_helper->getFilePath($filename);

        if (!file_exists($file)) {
            throw new RuntimeException('File not exist "' . $file . '"');
        }

        $time = microtime(true);

        $this->log('Starting import: ' . $file);

        try {
            $this->_process($file, $filename, $offset, $output, $time);

            $this->log('Import finished: ' . round(microtime(true) - $time) . ' seconds');
            $this->_helper->removeProgress($filename);
        } catch (Exception $e) {
            $this->log('Import failed: ' . round(microtime(true) - $time) . ' seconds');
            $this->_helper->removeProgress($filename);
            throw $e;
        }
    }

    /**
     * Process file
     * 
     * @param string  $file     File path
     * @param string  $filename Filename
     * @param integer $offset   Offset
     * @param boolean $output   Enable output
     * @param integer $time     Start time
     * 
     * @return void
     */
    protected function _process($file, $filename, $offset, $output, $time)
    {
        $this->_helper->saveProgress($filename);

        $reader = new Nicovogelaar_Nedisimport_Model_Reader_Xml_CatalogReader($file);
        $processor = new Nicovogelaar_Nedisimport_Model_Processor_ProductProcessor();

        foreach ($reader as $index => $product) {
            if ($offset > 0 && $index < $offset) {
                continue;
            }

            $processor->process($product);

            if ($index % 100 === 0) {
                $this->_helper->saveProgress($filename);
            }

            $message = 'Processed product with SKU "' . $product->getSku() . '"'
                . "\t - " . ($index + 1)
                . "\t - " . round(microtime(true) - $time) . ' seconds'
                . "\t - " . $this->_helper->formatBytes(memory_get_usage(true)) . ' memory'
            ;

            $this->log($message);

            if ($output) {
                echo $message . "\n";
            }
        }
    }

    /**
     * Log
     * 
     * @param string $message Message
     * 
     * @return void
     */
    public function log($message)
    {
        Mage::log($message, null, 'nedisimport.log', true);
    }

    /**
     * {@inheritdoc}
     */
    public function usageHelp()
    {
        return <<<USAGE

Usage:  php -f nedisimport.php -- [options]

  --file <filename>      File to import
  --offset <number>      Offset
  --output               Enable output

  help                   This help

USAGE;
    }
}

$shell = new Nicovogelaar_Nedisimport_Shell_Nedisimport();

try {
    $shell->run();
} catch (Exception $e) {
    $shell->log($e->getMessage());
    throw $e;
}