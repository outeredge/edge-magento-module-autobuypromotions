<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_AutoBuyPromotion_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('autobuypromotions_product_grid');
        $this->setUseAjax(true);
    }

    public function getRule()
    {
        return Mage::registry('current_promo_quote_rule');
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'selected_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('type_id', array('in' => array('simple', 'virtual')));

        if (!$this->getRequest()->getParam('ajax')) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('selected_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'selected_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('autobuypromotions')->__('ID'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('autobuypromotions')->__('Name'),
            'index'  => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('autobuypromotions')->__('SKU'),
            'index'     => 'sku',
            'width'     => '200px',
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('autobuypromotions')->__('Price'),
            'index'         => 'price',
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', array('_current'=>true));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if (is_null($products)) {
            return $this->getRule()->getProductId();
        }
        return $products;
    }

    public function getSelectedProducts()
    {
        return $this->getRule()->getProductId();
    }
}