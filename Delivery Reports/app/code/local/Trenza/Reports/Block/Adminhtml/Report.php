<?php

class Trenza_Reports_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_report';
    $this->_blockGroup = 'trenza_reports';
    $this->_headerText = Mage::helper('trenza_reports')->__('Delivery Reports');
    parent::__construct();
    $this->_removeButton('add');
    $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('trenza_reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    
  }
  
  public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/index', array('_current' => true));
    }
}