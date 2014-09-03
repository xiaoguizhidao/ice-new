<?php
/**
 * Adminhtml ChannelAdvisor Grid Render
 *
 * @category   QS
 * @package    QS_BridgeChannelAdvisor
 * @copyright  Copyright (c) 2013 QuartSoft (http://www.quartsoft.com)
 */

class QS_BridgeChannelAdvisor_Block_Adminhtml_Rend extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getId();
        $out = '';
        if($value == 2){
            $lnk = $this->getUrl('bridgechanneladvisor/adminhtml_profile/activity/process/'.$value);
            $lnkSpec = $this->getUrl('bridgechanneladvisor/adminhtml_profile/status/process/'.$value);
            $out = '<select class="action-select" onchange="varienGridAction.execute(this);"><option value=""></option><option value={"href":"'.$lnkSpec.'"}>Change Status</option><option value={"href":"'.$lnk.'"}>Set Activity To Pending</option></select>';
        }elseif($value == 1 || $value == 3 || $value == 4){
            $lnk1 = $this->getUrl('bridgechanneladvisor/adminhtml_profile/run/process/'.$value);
            $lnk2 = $this->getUrl('bridgechanneladvisor/adminhtml_profile/status/process/'.$value);
            $lnk3 = $this->getUrl('bridgechanneladvisor/adminhtml_profile/activity/process/'.$value);
            $lnk4 = $this->getUrl('bridgechanneladvisor/adminhtml_profile/resetSkipedSkus/process/'.$value);
            $lnk5 = $this->getUrl('bridgechanneladvisor/adminhtml_profile/activityfinished/process/'.$value);
            $lnk6 = $this->getUrl('bridgechanneladvisor/adminhtml_profile/runNow/process/'.$value);
            if($value == 1){
                $out = '<select class="action-select" onchange="varienGridAction.execute(this);"><option value=""></option><option value={"href":"'.$lnk1.'"}>Run</option><option value={"href":"'.$lnk2.'"}>Change Status</option><option value={"href":"'.$lnk3.'"}>Set Activity To Pending</option><option value={"href":"'.$lnk5.'"}>Set Activity To Finished</option><option value={"href":"'.$lnk4.'"}>Reset Skipped Skus</option></select>';
            }elseif($value == 3){
                $out = '<select class="action-select" onchange="varienGridAction.execute(this);"><option value=""></option><option value={"href":"'.$lnk6.'"}>RunProcessNow</option><option value={"href":"'.$lnk2.'"}>Change Status</option><option value={"href":"'.$lnk3.'"}>Set Activity To Pending</option><option value={"href":"'.$lnk4.'"}>Reset Skipped Skus</option></select>';
            }else{
                $out = '<select class="action-select" onchange="varienGridAction.execute(this);"><option value=""></option><option value={"href":"'.$lnk2.'"}>Change Status</option><option value={"href":"'.$lnk3.'"}>Set Activity To Pending</option></select>';
            }
        }
        return $out;
    }
}
