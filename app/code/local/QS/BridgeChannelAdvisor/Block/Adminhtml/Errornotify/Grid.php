<?php
/**
 * ChannelAdvisor Grid
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */
class QS_BridgeChannelAdvisor_Block_Adminhtml_Errornotify_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('channel_errornotify_id');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Errornotify_Grid
     */

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('bridgechanneladvisor/errornotify')->getCollection();
        $collection->getSelect()->join(Mage::getConfig()->getTablePrefix().'channel_sub_email', 'main_table.process_id ='.Mage::getConfig()->getTablePrefix().'channel_sub_email.process_id',array('process_title'));
        //var_dump($collection->getSelect()->__toString());exit;
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return QS_BridgeChannelAdvisor_Block_Adminhtml_Errornotify_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header'    => $this->__('ID'),
                'index'     => 'id',
                'filter_index' => 'id',
            ));

        $this->addColumn('process_id',
            array(
                'header'    => $this->__('Name Process'),
                'width'     => '20%',
                'index'     => 'process_title',
                'filter_index' => 'process_title',
            ));

        $this->addColumn('recording_time',
            array(
                'header'    => $this->__('Record Time'),
                'type'      => 'datetime',
                'width'     => '12%',
                'index'     => 'recording_time',
                'filter_index' => 'recording_time',
            ));

        $this->addColumn('message',
            array(
                'header'    => $this->__('Message'),
                'width'     => '35%',
                'index'     => 'message',
                'filter_index' => 'message',
            ));

        $this->addColumn('type_message',
            array(
                'header'    => $this->__('Type Message'),
                'width'     => '30%',
                'align'     => 'left',
                'index'     => 'type_message',
                'filter_index' => 'type_message',
                'type'      => 'text',
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('bridgechanneladvisor')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('bridgechanneladvisor')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id',
                        'confirm'  => Mage::helper('bridgechanneladvisor')->__('Are you sure?')
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
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
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->setNoFilterMassactionColumn(true);

        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => $this->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete', array('_current'=>true)),
            'confirm'  => $this->__('Are you sure?')
        ));
    }

}
