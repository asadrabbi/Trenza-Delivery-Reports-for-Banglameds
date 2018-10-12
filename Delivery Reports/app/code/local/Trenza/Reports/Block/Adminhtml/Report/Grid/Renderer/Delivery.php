<?php 

class Trenza_Reports_Block_Adminhtml_Report_Grid_Renderer_Delivery extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $requestData    = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        
        $from = new DateTime($requestData['from']);
        $from = $from->format('Y-m-d H:i:s');
        
        $to = new DateTime($requestData['to']);
        $to = $to->format('Y-m-d H:i:s');
        
        $username = $row->getData('username');
        $orders = Mage::getModel('sales/order')->getCollection()
                                ->addAttributeToFilter('trz_delivery_man',$username)
                                ->addAttributeToFilter('created_at', array('from'=>$from, 'to'=>$to))
                                ->addAttributeToSelect('*');
        
        $count = count($orders);
        
        if($count > 0)
            return $count;
        else
            return '0';
    }
}