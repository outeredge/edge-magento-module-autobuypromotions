<?php

class Edge_AutoBuyPromotions_Adminhtml_AutoBuyPromotionsController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/quote');
    }
    
    /**
     * Initialize rule from request parameters
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _initRule()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $rule = Mage::getModel('salesrule/rule')->load($id);

        Mage::register('autobuypromotions_rule', $rule);
        return $rule;
    }

    public function productsTabAction()
    {
        $this->_initRule();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function productsGridAction()
    {
        $this->_initRule();
        $this->loadLayout();
        $this->renderLayout();
    }
}
