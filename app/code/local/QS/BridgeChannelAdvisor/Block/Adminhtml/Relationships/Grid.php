<?php
class QS_BridgeChannelAdvisor_Block_Adminhtml_Relationships_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
        {
            parent::__construct();
            $this->setId('relationships_grid');
            $this->setSaveParametersInSession(true);
            $this->setUseAjax(false);
        }

        /**
         * Prepare grid collection object
         *
         * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Relationships_Grid
         */
        protected function _prepareCollection()
        {
            /** @var QS_BridgeChannelAdvisor_Model_Mysql4_Relationships_Collection $collection */
            $collection = Mage::getResourceModel('bridgechanneladvisor/relationships_collection');
            $this->setCollection($collection);
            parent::_prepareCollection();
            return $this;
        }

        /**
         * Prepare grid colunms
         *
         * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Relationships_Grid
         */
        protected function _prepareColumns()
        {
            $this->addColumn('rel_id',
                       array(
                           'header'    => $this->__('ID'),
                           'width'     => '3%',
                           'index'     => 'rel_id',
                   ));

                   $this->addColumn('relationship_name',
                       array(
                           'header'    => $this->__('relationship Name'),
                           'width'     => '47%',
                           'index'     => 'relationship_name',
                   ));

                   $this->addColumn('attributes',
                       array(
                           'header'    => $this->__('attributes '),
                           'width'     => '47%',
                           'index'     => 'attributes',
                   ));


            return parent::_prepareColumns();
        }

        protected function _prepareMassaction()
        {
            $this->setMassactionIdField('rel_id');
            $this->getMassactionBlock()->setFormFieldName('rel_id');
            $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('tax')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => Mage::helper('tax')->__('Are you sure?')
            ));
            return $this;
        }

}