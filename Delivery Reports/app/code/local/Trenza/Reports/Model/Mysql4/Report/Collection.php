<?php

class Trenza_Reports_Model_Mysql4_Report_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * 'From Date' filter
     * @var string
     */
    protected $_from;

    /**
     * 'To Date' filter
     * @var string
     */
    protected $_to;

    // add filter methods

    /**
     * Set date range to filter on
     * @param string $from
     * @param string $to
     * @return My_Reports_Model_Mysql4_Report_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_from = $from;
        $this->_to = $to;
        return $this;
    }


    /**
     * Apply our date range filter on select
     * @return My_Reports_Model_Mysql4_Report_Collection
     */
    protected function _applyDateRangeFilter()
    {
        if (!is_null($this->_from)) {
            $this->_from = date('Y-m-d G:i:s', strtotime($this->_from));
            $this->getSelect()->where('created_at >= ?', $this->_from);
        }
        if (!is_null($this->_to)) {
            $this->_to = date('Y-m-d G:i:s', strtotime($this->_to));
            $this->getSelect()->where('created_at <= ?', $this->_to);
        }

        return $this;
    }


    /**
     * Inicialise select right before loading collection
     * We need to fire _initSelect here, because the isTotals mode creates different results depending
     * on it's value. The parent implementation of the collection originally fires this method in the
     * constructor.
     * @return My_Reports_Model_Mysql4_Report_Collection
     */
    protected function _beforeLoad()
    {
        $this->_initSelect();
        return parent::_beforeLoad();
    }

    /**
     * This would render all of our pre-set filters on collection.
     * Calling of this method happens in Varien_Data_Collection_Db::_renderFilters(), while
     * the _renderFilters itself is called in Varien_Data_Collection_Db::load() before calling
     * _renderOrders() and _renderLimit() .
     * @return My_Reports_Model_Mysql4_Report_Collection
     */
    protected function _renderFiltersBefore()
    {
        $this->_applyDateRangeFilter();
        return $this;
    }

}