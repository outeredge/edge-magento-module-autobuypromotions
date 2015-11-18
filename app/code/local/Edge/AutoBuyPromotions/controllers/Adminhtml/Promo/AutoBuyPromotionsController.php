<?php

require_once(Mage::getModuleDir('controllers','Mage_Adminhtml').DS.'Promo'.DS.'QuoteController.php');

class Edge_AutoBuyPromotions_Adminhtml_Promo_AutoBuyPromotionsController extends Mage_Adminhtml_Promo_QuoteController
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/autobuy');
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

    public function triggerProductsTabAction()
    {
        $this->_initRule();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function triggerProductsGridAction()
    {
        $this->_initRule();
        $this->loadLayout();
        $this->renderLayout();
    }
}
