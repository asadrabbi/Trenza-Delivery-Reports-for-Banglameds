<?php 

class Trenza_Reports_Block_Adminhtml_Report_Grid_Renderer_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        
        $username = $row->getData('username');
        $user_data = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username',$username)->getFirstItem()->getData();

        $fname = $user_data['firstname'];
        $lname = $user_data['lastname'];
        $name = $fname.' '.$lname;
        
        return $name;
    }
}