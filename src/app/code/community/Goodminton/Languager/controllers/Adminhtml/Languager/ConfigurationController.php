<?php
/**
 * Goodminton
 *
 * Copyright 2016 Goodminton AG
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package     Goodminton_Languager
 * @author      Pierre Bernard <pierre.bernard@foodspring.com>
 * @copyright   Copyright (c) 2016 Goodminton AG (http://goodminton.ag)
 * @license     https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

class Goodminton_Languager_Adminhtml_Languager_ConfigurationController extends Mage_Adminhtml_Controller_Action
{
    public function storesAction()
    {
        $this->initLayout()
            ->_addContent($this->getLayout()->createBlock('goodminton_languager/adminhtml_stores_container'))
            ->renderLayout();
    }

    public function saveStoresAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            foreach ($data['stores'] as $key => $language) {
                /** @var Mage_Core_Model_Store $store */
                $store = Mage::getModel('core/store')->load($key);
                $store->setData('gl_language', $language);
                $store->save();
            }
        }
        $this->addSuccess('Stores configuration have been saved');
        $this->_redirectReferer();
    }

    public function productsAction()
    {
        $this->initLayout()
            ->_addContent($this->getLayout()->createBlock('goodminton_languager/adminhtml_products_container'))
            ->renderLayout();
    }

    public function saveProductsAction()
    {
        $this->saveAttributes('catalog/product_attribute_collection');
    }

    public function categoriesAction()
    {
        $this->initLayout()
            ->_addContent($this->getLayout()->createBlock('goodminton_languager/adminhtml_categories_container'))
            ->renderLayout();
    }

    public function saveCategoriesAction()
    {
        $this->saveAttributes('catalog/category_attribute_collection');
    }

    public function saveAttributes($attributeType)
    {
        if ($data = $this->getRequest()->getPost()) {

            /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributeCollection */
            $attributeCollection = Mage::getResourceModel($attributeType);
            $attributeCollection->addFilter('is_visible', 1);
            $attributeCollection->addFilter('gl_translated', 1);
            $attributes = $attributeCollection->getItems();
            foreach ($attributes as $attribute) {
                /** @type Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                $attribute->setData('gl_translated', 0);
                $attribute->save();
            }

            foreach ($data['attributes'] as $key => $translated) {
                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                $attribute = Mage::getResourceModel('catalog/eav_attribute')->load($key);
                $attribute->setData('gl_translated', $translated);
                $attribute->save();
            }
        }
        $this->addSuccess('Attributes have been saved');
        $this->_redirectReferer();
    }

    protected function initLayout()
    {
        $this->loadLayout();
        $this->setUsedModuleName('goodminton_languager');
        $this->_setActiveMenu('system');

        $this->_title('Languager');
        $this->_title('Configuration');

        return $this;
    }

    protected function addSuccess($message)
    {
        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess($message);
    }
}