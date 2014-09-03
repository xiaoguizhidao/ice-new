<?php
/** {license_text}  */
class Oro_Admin_Model_Sales_Order extends Amasty_Orderattach_Model_Sales_Order
{
    /*
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param string $comment
     * @param string $status
     * @return Mage_Sales_Order_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if (false === $status) {
            $status = $this->getStatus();
        } elseif (true === $status) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }
        $history = Mage::getModel('sales/order_status_history')
            ->setStatus($status)
            ->setComment($comment)
            ->setEntityName($this->_historyEntityName);

        if (Mage::app()->getStore()->isAdmin()) {
            /** @var Mage_Admin_Model_Session $adminSession */
            $adminSession = Mage::getSingleton('admin/session');

            if ($adminSession->getUser() && $adminSession->getUser()->getId()) {
                /** @var Mage_Admin_Model_User $admin */
                $admin = $adminSession->getUser();
                $history->setAdminUsername($admin->getName());
                $history->setAdminEmail($admin->getEmail());
            }
        }

        $this->addStatusHistory($history);
        return $history;
    }
}
