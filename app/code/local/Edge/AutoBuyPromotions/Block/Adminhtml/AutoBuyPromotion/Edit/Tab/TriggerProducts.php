<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_AutoBuyPromotion_Edit_Tab_TriggerProducts
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('autobuypromotions_trigger_product_grid');
        $this->setUseAjax(true);
    }

    public function getRule()
    {
        return Mage::registry('current_promo_quote_rule');
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'selected_triggerproducts') {
            $productIds = $this->_getSelectedTriggerProducts();
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
            ->addAttributeToSelect('*');

        if (!$this->getRequest()->getParam('ajax')) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedTriggerProducts()));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('selected_triggerproducts', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'selected_triggerproducts',
            'values'    => $this->_getSelectedTriggerProducts(),
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

        $categoryOptions = array();
        $categories = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('name')
            ->addFieldToFilter('level', array('nin' => array(0,1)));

        foreach ($categories as $category) {
            $categoryOptions[$category->getEntityId()] = $category->getName();
        }

        $this->addColumn('category', array(
            'header'    => Mage::helper('autobuypromotions')->__('Category'),
            'index'     => 'category',
            'type'      => 'options',
            'options'   => $categoryOptions,
            'renderer'  => 'admingridcolumns/grid_render_category',
            'filter_condition_callback' => array($this, 'categoryFilterCallback')
        ));

        $this->addColumn('type', array(
            'header'=> Mage::helper('catalog')->__('Type'),
            'width' => '60px',
            'index' => 'type_id',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
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
        return $this->getUrl('*/*/triggerProductsGrid', array('_current'=>true));
    }

    protected function _getSelectedTriggerProducts()
    {
        $products = $this->getRequest()->getPost('selected_triggerproducts');
        if (is_null($products)) {
            return $this->getRule()->getTriggerProductId();
        }
        return $products;
    }

    public function getSelectedTriggerProducts()
    {
        return $this->getRule()->getTriggerProductId();
    }

    public function categoryFilterCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $category = Mage::getModel('catalog/category')->load($value);
        $collection->addCategoryFilter($category);
        return $collection;
    }
}