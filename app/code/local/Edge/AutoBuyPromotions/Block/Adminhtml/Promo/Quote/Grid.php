<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_Promo_Quote_Grid extends Mage_Adminhtml_Block_Promo_Quote_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('salesrule/rule')
            ->getResourceCollection();
        $collection->addWebsitesToResult();

        $collection->addFieldToFilter('is_auto_buy_promotion', array(
            'eq' => ($this->getRequest()->getControllerModule() === 'Edge_AutoBuyPromotions_Adminhtml')
        ));
        $this->setCollection($collection);

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        return $this;
    }
}
