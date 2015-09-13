<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Block_Adminhtml_Import_Catalog_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Nicovogelaar_Nedisimport_Block_Adminhtml_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $helper = Mage::helper('nicovogelaar_nedisimport');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => $helper->__('Catalog Import Settings'))
        );
        $fieldset->addField('import_file', 'file', array(
            'name'     => 'import_file',
            'label'    => $helper->__('Select File to Import'),
            'title'    => $helper->__('Select File to Import'),
            'required' => true,
            'after_element_html' => '<em>Max upload file size: ' . ini_get('upload_max_filesize') . '</em>',
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
