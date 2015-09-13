<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
abstract class Nicovogelaar_Nedisimport_Block_Adminhtml_Import_Container extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->_removeButton('back')
             ->_removeButton('reset');
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'import_id';
        $this->_blockGroup = 'nicovogelaar_nedisimport';
        $this->_controller = 'adminhtml_import';
    }
}
