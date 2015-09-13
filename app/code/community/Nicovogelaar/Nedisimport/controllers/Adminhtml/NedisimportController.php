<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Adminhtml_NedisimportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Catalog import
     */
    public function indexAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->_processForm();
        }

        $this->loadLayout();
 
        $this->renderLayout();
    }

    /**
     * Process form
     */
    protected function _processForm()
    {
        $helper = Mage::helper('nicovogelaar_nedisimport/catalog');

        $file = $helper->handleUploadedFile();

        if (false !== $file) {
            $this->_getSession()->addSuccess('File uploaded successfully');

            $helper->processFile($file);
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Process file
     */
    public function processAction()
    {
        $filename = $this->getRequest()->getParam('file');

        Mage::helper('nicovogelaar_nedisimport/catalog')->processFile($filename);

        $this->_redirect('*/*/index');
    }

    /**
     * Mass delete files
     */
    public function massDeleteAction()
    {
        $files = $this->getRequest()->getParam('files', array());

        $helper = Mage::helper('nicovogelaar_nedisimport/catalog');

        foreach ($files as $file) {
            $helper->deleteFile($file);
        }

        $this->_redirect('*/*/index');
    }
}