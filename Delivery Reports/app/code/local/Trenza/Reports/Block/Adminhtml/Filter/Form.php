<?php

class Trenza_Reports_Block_Adminhtml_Filter_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * This will contain our form element's visibility
     * @var array
     */
    protected $_fieldVisibility  = array();

    /**
     * Field options
     * @var array
     */
    protected $_fieldOptions   = array();

    /**
     * Sets a form element to be visible or not
     * @param string $fieldId
     * @param bool $visibility
     * @return My_Reports_Block_Adminhtml_Filter_Form
     */
    public function setFieldVisibility($fieldId, $visibility)
    {
        $this->_fieldVisibility[$fieldId] = $visibility ? true : false;
        return $this;
    }

    /**
     * Returns the field is visible or not. If we hadn't set a value
     * for the field previously, it will return the value defined in the
     * defaultVisibility parameter (it's true by default)
     * @param string $fieldId
     * @param bool $defaultVisibility
     * @return bool
     */
    public function getFieldVisibility($fieldId, $defaultVisibility = true)
    {
        if (isset($this->_fieldVisibility[$fieldId])) {
            return $this->_fieldVisibility[$fieldId];
        }
        return $defaultVisibility;
    }

    /**
     * Set field option(s)
     * @param string $fieldId
     * @param string|array $option if option is an array, loop through it's keys and values
     * @param mixed $value if option is an array this option is meaningless
     * @return My_Reports_Block_Adminhtml_Filter_Form
     */
    public function setFieldOption($fieldId, $option, $value = null)
    {
        if (is_array($option)) {
            $options    = $option;
        } else {
            $options    = array($option => $value);
        }

        if (!isset($this->_fieldOptions[$fieldId])) {
            $this->_fieldOptions[$fieldId] = array();
        }

        foreach ($options as $key => $value) {
            $this->_fieldOptions[$fieldId][$key] = $value;
        }

        return $this;
    }

    /**
     * Prepare our form elements
     * @return My_Reports_Block_Adminhtml_Filter_Form
     */
    protected function _prepareForm()
    {
          // inicialise our form
        $actionUrl      = $this->getCurrentUrl();
        $form           = new Varien_Data_Form(array(
            'id'        => 'filter_form',
            'action'    => $actionUrl, 
            'method'    => 'get'
        ));

        // set ID prefix for all elements in our form
        $htmlIdPrefix   = 'trenza_reports_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        // create a fieldset to add elements to
        $fieldset       = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('trenza_reports')->__('Filter Form')));

        // prepare our filter fields and add each to the fieldset

        // date filter
        $dateFormatIso  = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from', 'date', array(
            'name'      => 'from',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('trenza_reports')->__('From'),
            'title'     => Mage::helper('trenza_reports')->__('From'),
            'required'  => true
        ));
        $fieldset->addField('to', 'date', array(
            'name'      => 'to',
            'format'    => $dateFormatIso,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('trenza_reports')->__('To'),
            'title'     => Mage::helper('trenza_reports')->__('To'),
            'required'  => true
        ));
                
        $fieldset->addField('deliveryman', 'select', array(
            'name'      => 'deliveryman',
            'options'   => $this->_getDeliveryManOptions(),
            'label'     => Mage::helper('trenza_reports')->__('Delivered By')
        ));
                        
        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }
        
    protected function _getDeliveryManOptions()
    {
        
        $options='';      
        $roles_users = Mage::getResourceModel('admin/roles_user_collection');
        $options['0'] = Mage::helper('trenza_reports')->__("");   
        $options['showall'] = Mage::helper('trenza_reports')->__("Show All");        
        foreach($roles_users as $roleuser):
            $user = Mage::getModel('admin/user')->load($roleuser->getUserId());
            $_name = $user->getFirstname(). " ". $user->getLastname();
            $username = $user->getUsername();
            $options[$username] = Mage::helper('trenza_reports')->__($_name);            
        endforeach;
        return $options;
    }
            
    /**
     * Inicialise form values
     * Called after prepareForm, we apply the previously set values from filter on the form
     * @return My_Reports_Block_Adminhtml_Filter_Form
     */
    protected function _initFormValues()
    {
        $filterData     = $this->getFilterData();
        $this->getForm()->addValues($filterData->getData());
        return parent::_initFormValues();
    }

    /**
     * Apply field visibility and field options on our form fields before rendering
     * @return My_Reports_Block_Adminhtml_Filter_Form
     */
    protected function _beforeHtml()
    {
        $result         = parent::_beforeHtml();

        $elements       = $this->getForm()->getElements();

        // iterate on our elements and select fieldsets
        foreach ($elements as $element) {
            $this->_applyFieldVisibiltyAndOptions($element);
        }

        return $result;
    }

    /**
     * Apply field visibility and options on fieldset element
     * Recursive
     * @param Varien_Data_Form_Element_Fieldset $element
     * @return Varien_Data_Form_Element_Fieldset
     */
    protected function _applyFieldVisibiltyAndOptions($element) {
        if ($element instanceof Varien_Data_Form_Element_Fieldset) {
            foreach ($element->getElements() as $fieldElement) {
                // apply recursively
                if ($fieldElement instanceof Varien_Data_Form_Element_Fieldset) {
                    $this->_applyFieldVisibiltyAndOptions($fieldElement);
                    continue;
                }

                $fieldId = $fieldElement->getId();
                // apply field visibility
                if (!$this->getFieldVisibility($fieldId)) {
                    $element->removeField($fieldId);
                    continue;
                }

                // apply field options
                if (isset($this->_fieldOptions[$fieldId])) {
                    $fieldOptions = $this->_fieldOptions[$fieldId];
                    foreach ($fieldOptions as $k => $v) {
                        $fieldElement->setDataUsingMethod($k, $v);
                    }
                }
            }
        }

        return $element;
    }
        

}