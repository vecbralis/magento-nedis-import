<?php
/**
 * @copyright Copyright (c) 2015 Nico Vogelaar (http://nicovogelaar.nl)
 * @license   http://www.opensource.org/licenses/mit-license.php  MIT License
 * @link      http://nicovogelaar.nl
 */

/**
 * @author Nico Vogelaar <nico@nicovogelaar.nl>
 */
class Nicovogelaar_Nedisimport_Block_Adminhtml_Import_Catalog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('files_grid');
        $this->setGridHeader('Files');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();

        $files = Mage::helper('nicovogelaar_nedisimport/catalog')->getFiles();

        foreach ($files as $file) {
            $collection->addItem($file);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('filename', array(
            'header'    => $this->__('Filename'),
            'align'     => 'left',
            'index'     => 'filename',
            'sortable'  => false,
        ));

        $this->addColumn('filesize', array(
            'header'    => $this->__('Filesize'),
            'align'     => 'left',
            'index'     => 'filesize',
            'sortable'  => false,
            'frame_callback' => array($this, 'decorateFilesize')
        ));

        $this->addColumn('filemtime', array(
            'header'    => $this->__('Last modified'),
            'align'     => 'left',
            'index'     => 'filemtime',
            'sortable'  => false,
            'frame_callback' => array($this, 'decorateFilemtime')
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status',
            'sortable'  => false,
            'frame_callback' => array($this, 'decorateStatus')
        ));

        $this->addColumn('action',
           array(
               'header'    =>  $this->__('Action'),
               'width'     => '100',
               'type'      => 'action',
               'getter'    => 'getId',
               'actions'   => array(
                   array(
                       'caption'   => $this->__('Process'),
                       'url'       => array('base' => '*/*/process'),
                       'field'     => 'file'
                   ),
               ),
               'filter'    => false,
               'sortable'  => false,
               'is_system' => true,
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Decorate filesize column values
     *
     * @return string
     */
    public function decorateFilesize($value, $row, $column, $isExport)
    {
        return Mage::helper('nicovogelaar_nedisimport/catalog')->formatBytes($value);
    }

    /**
     * Decorate filemtime column values
     *
     * @return string
     */
    public function decorateFilemtime($value, $row, $column, $isExport)
    {
        return date('Y-m-d H:i:s', $value);
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        if ($value) {
            $text = $this->__('In progress');
            $class = 'grid-severity-notice';
        } else {
            $text = '';
            $class = '';
        }

        return '<span class="' . $class . '"><span>' . $text . '</span></span>';
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('files');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('nicovogelaar_nedisimport')->__('Delete'),
            'url'   => $this->getUrl('*/*/massDelete'),
        ));

        return $this;
    }
}
