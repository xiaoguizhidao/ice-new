<?php
/**
 * ChannelAdvisor Grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Prepareitems_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('item_id');
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Prepareitems_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bridgechanneladvisor/prepareitem')->getCollection();
        $collection->_joinMageProductData();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Prepareitems_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('item_id',
            array(
                'header'    => $this->__('ID'),
                'width'     => '3%',
                'index'     => 'item_id',
                'filter' => false,
        ));

        $this->addColumn('product_id',
            array(
                'header'    => $this->__('Product ID'),
                'width'     => '10%',
                'index'     => 'product_id',
                'filter' => false,
        ));

        $this->addColumn('name',
            array(
                'header'    => $this->__('Product Name'),
                'width'     => '50%',
                'index'     => 'name',
                'filter' => false,
        ));

        $this->addColumn('sku',
            array(
                'header'    => $this->__('SKU'),
                'width'     => '27%',
                'index'     => 'sku',
                'filter' => false,
        ));

        $this->addColumn('type_id',
            array(
                'header'    => $this->__('Product Type'),
                'width'     => '10%',
                'index'     => 'type_id',
                'filter' => false,
        ));

        $this->addColumn('export_try',
            array(
                'header'    => $this->__('Export Try'),
                'width'     => '10%',
                'index'     => 'export_try',
                'filter'    => false,
                'type'      => 'options',
                'options'   => array(
                    1 => Mage::helper('bridgechanneladvisor')->__('Tried Export'),
                    0 => Mage::helper('bridgechanneladvisor')->__('Pending'),
                ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('item_id');
        $this->setNoFilterMassactionColumn(true);

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => $this->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete', array('_current'=>true)),
            'confirm'  => $this->__('Are you sure?')
        ));
    }

}
