<?php
/**
 * ChannelAdvisor Grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('channel_profile_id');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Profile_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bridgechanneladvisor/settings')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Profile_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('profile_id',
            array(
                'header'    => $this->__('ID'),
                'width'     => '3%',
                'index'     => 'id',
                'filter' => false,
        ));

        $this->addColumn('title',
            array(
                'header'    => $this->__('Title'),
                'width'     => '20%',
                'index'     => 'title',
                'filter' => false,
        ));

        $this->addColumn('last_run',
            array(
                'header'    => $this->__('Last Run'),
                'type'      => 'datetime',
                'width'     => '12%',
                'index'     => 'last_run',
                'filter' => false,
        ));

        $this->addColumn('products_imported',
            array(
                'header'    => $this->__('Products Imported'),
                'width'     => '5%',
                'index'     => 'all_ca_products',
                'filter' => false,
        ));

        $this->addColumn('errors',
            array(
                'header'    => $this->__('Errors'),
                'width'     => '5%',
                'index'     => 'errors',
                'filter' => false,
                'type'      => 'options',
                'filter' => false,
                'options'   => array(0 => $this->__('no errors')),
                'frame_callback' => array($this, 'decorateErrors')
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'width'     => '5%',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'filter' => false,
            'options'   => array(0 => $this->__('Disabled'), 1 => $this->__('Enabled')),
            'frame_callback' => array($this, 'decorateStatus')
        ));

        $this->addColumn('channel_flag', array(
            'header'    => $this->__('Activity'),
            'width'     => '5%',
            'align'     => 'left',
            'index'     => 'channel_flag',
            'type'      => 'options',
            'filter' => false,
            'options'   => array(2 => $this->__('Finished'), 1 => $this->__('Running'), 0 => $this->__('Pending')),
            'frame_callback' => array($this, 'decorateActivity')
        ));

        $this->addColumn('skiped_skus', array(
            'header'    => $this->__('Skipped SKUs'),
            'width'     => '30%',
            'align'     => 'left',
            'index'     => 'skiped_skus',
            'type'      => 'text',
            'filter' => false
        ));

        $this->addColumn('action',array(
            'header' => $this->__('Action'),
            'width' => '5%',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Run'),
                    'url' => array('base' => '*/*/run'),
                    'field' => 'process'
                ),
                array(
                    'caption' => $this->__('Change Status'),
                    'url' => array('base' => '*/*/status'),
                    'field' => 'process'
                ),
                array(
                    'caption' => $this->__('Reset Activity'),
                    'url' => array('base' => '*/*/activity'),
                    'field' => 'process'
                ),
                array(
                    'caption' => $this->__('Reset Skipped Skus'),
                    'url' => array('base' => '*/*/resetSkipedSkus'),
                    'confirm' => 'These SKU may fail Import Products Process. Are you sure, you want to import it?',
                    'field' => 'process'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
            'renderer'  => 'QS_BridgeChannelAdvisor_Block_Adminhtml_Rend'
        ));

        return parent::_prepareColumns();
    }


    /**
     * Create error link to notification page
     *
     * @return string
     */
    public function decorateErrors($value, $row, $column, $isExport)
    {
        if ($row->getErrors() != 0) {
            $cell = '<a href="'.$this->getUrl('adminhtml/notification').'">Errors</a>';
        } else {
            $cell = $value;
        }

        return $cell;
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        if (isset($this->_invalidatedTypes[$row->getId()])) {
            $cell = '<span class="grid-severity-minor"><span>'.$this->__('Invalidated').'</span></span>';
        } else {
            if ($row->getStatus()) {
                $cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
            } else {
                $cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
            }
        }
        return $cell;
    }

    /**
     * Decorate activity column values
     *
     * @return string
     */
    public function decorateActivity($value, $row, $column, $isExport)
    {
        $class = '';
        if (isset($this->_invalidatedTypes[$row->getId()])) {
            $cell = '<span class="grid-severity-critical"><span>'.$this->__('Invalidated').'</span></span>';
        } else {
            if ($row->getChannelFlag() == 1 || $row->getChannelFlag() == 2) {
                $cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
            }
            if ($row->getChannelFlag() == 0) {
                $cell = '<span class="grid-severity-minor"><span>'.$value.'</span></span>';
            }
        }
        return $cell;
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_profile/grid', array('index' => $this->getIndex(),'_current'=>true));
    }

}
