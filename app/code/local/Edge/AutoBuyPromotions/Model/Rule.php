<?php

class Edge_AutoBuyPromotions_Model_Rule extends Mage_SalesRule_Model_Rule
{ 
    /**
     * Set coupon code and uses per coupon
     *
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _afterLoad()
    {
        //Manipulate Name
        $label = $this->getStoreLabel();
        if ($label) {
            $this->setFrontendName($label);   
        } else {
            $this->setFrontendName($this->getName());   
        }
        
        $this->setCouponCode($this->getPrimaryCoupon()->getCode());
        if ($this->getUsesPerCoupon() !== null && !$this->getUseAutoGeneration()) {
            $this->setUsesPerCoupon($this->getPrimaryCoupon()->getUsageLimit());
        }
        return parent::_afterLoad();
    }
}
