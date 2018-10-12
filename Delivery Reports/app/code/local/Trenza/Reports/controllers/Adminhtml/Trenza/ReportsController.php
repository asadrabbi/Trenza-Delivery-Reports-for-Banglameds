<?php

class Trenza_Reports_Adminhtml_Trenza_ReportsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize titles and navigation breadcrumbs
     * @return My_Reports_Adminhtml_ReportsController
     */
    protected function _initAction()
    {
        $this->_title($this->__('Reports'))->_title($this->__('Sales'))->_title($this->__('Delivery Reports'));
        $this->loadLayout()
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(Mage::helper('trenza_reports')->__('Delivery Reports'), Mage::helper('trenza_reports')->__('Delivery Reports'));
        return $this;
    }

    /**
     * Prepare blocks with request data from our filter form
     * @return My_Reports_Adminhtml_ReportsController
     */
    protected function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = array($blocks);
        }
 
        $requestData    = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData    = $this->_filterDates($requestData, array('from', 'to', 'dman'));
        $params         = $this->_getDefaultFilterData();
        
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
 
        foreach ($blocks as $block) {
            if ($block) {
                $block->setFilterData($params);
            }
        }
        return $this;
    }

    /**
     * Grid action
     */
   	public function indexAction()
    {
        $this->_initAction();

        $gridBlock = $this->getLayout()->getBlock('adminhtml_report.grid');
        $filterFormBlock = $this->getLayout()->getBlock('grid.filter.form');
        
        $this->_initReportAction(array(
            $gridBlock,
            $filterFormBlock
        ));

        $this->renderLayout();
    }

    /**
     * Export reports to CSV file
     */
    public function exportCsvAction()
    {
        
        $fileName   = 'trenza_reports.csv';
        $content    = $this->getLayout()->createBlock('trenza_reports/adminhtml_report_grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }


    /**
     * Returns default filter data
     * @return Varien_Object
     */
    protected function _getDefaultFilterData()
    {
        return new Varien_Object(array(
            'from'      => '',
            'to'        => ''
        ));
    }
    protected function _prepareDownloadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}