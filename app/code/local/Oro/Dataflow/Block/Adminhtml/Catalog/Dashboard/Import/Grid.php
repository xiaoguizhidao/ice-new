<?php

class Oro_Dataflow_Block_Adminhtml_Catalog_Dashboard_Import_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('importGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('oro_dataflow/schedule_catalog_import_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('oro_dataflow');
        $this->addColumn('id', array(
            'header' => $helper->__('ID'),
            'align' => 'left',
            'index' => 'id',
            'width' => '30px',
        ));

        $this->addColumn('user_id', array(
            'header' => $helper->__('User ID'),
            'align' => 'left',
            'index' => 'user_id',
            'width' => '30px',
        ));

        $this->addColumn('updated_at', array(
            'header' => $helper->__('Modified At'),
            'align' => 'left',
            'index' => 'updated_at',
            'type' => 'datetime',
        ));

        $this->addColumn('scheduled_at', array(
            'header' => $helper->__('Scheduled At'),
            'align' => 'left',
            'index' => 'scheduled_at',
            'type' => 'datetime',
        ));

        $this->addColumn('executed_at', array(
            'header' => $helper->__('Executed At'),
            'align' => 'left',
            'index' => 'executed_at',
            'type' => 'datetime',
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '80px',
            'options' => $helper->getScheduleStatusOptionArray(),
        ));

        $this->addColumn('action',
            array(
                'header' => $helper->__('Actions'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $helper->__('Delete'),
                        'url' => array('base' => '*/*/deleteImport'),
                        'field' => 'id'
                    ),
                    array(
                        'caption' => $helper->__('Details'),
                        'url' => array('base' => '*/*/detailsImport'),
                        'field' => 'id'
                    ),
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
                'align' => 'center'
            ));

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/importGrid', array('_current' => true));
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
