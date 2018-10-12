<?php

class Trenza_Reports_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reportsGrid');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setFilterVisibility(false);
                
        // set message for empty result
        $this->setEmptyCellLabel(Mage::helper('trenza_reports')->__('No records found.'));
    }
    

  protected function _prepareCollection()
  {
    
        
        $collection = Mage::getResourceModel('admin/roles_user_collection');
        
        $requestData    = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $delivery_man = $requestData['deliveryman'];

            if($delivery_man == 'showall'){
                $this->setCollection($collection);
            }
            else{
                $user_data = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username',$delivery_man);
                
                $this->setCollection($user_data);
            }

        
        return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('user_id', array(
          'header'    => Mage::helper('trenza_reports')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'user_id',
      ));

      $this->addColumn('username', array(
          'header'    => Mage::helper('trenza_reports')->__('Delivered By'),
          'align'     =>'left',
          'index'     => 'username',
          'renderer' => 'Trenza_Reports_Block_Adminhtml_Report_Grid_Renderer_Name',
      ));
      
      $this->addColumn('title', array(
            'header'    => Mage::helper('trenza_reports')->__('Total Delivery'),
            'align'     =>'left',
            'index' => 'username',
            'renderer' => 'Trenza_Reports_Block_Adminhtml_Report_Grid_Renderer_Delivery',
      ));
      
      
		$this->addExportType('*/*/exportCsv', Mage::helper('trenza_reports')->__('CSV'));
	  
      return parent::_prepareColumns();
  }
    
}