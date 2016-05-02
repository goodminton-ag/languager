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

/**
 * Class Goodminton_Languager_Adminhtml_Languager_ConfigurationController
 */
class Goodminton_Languager_Adminhtml_Languager_ConfigurationController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'stores':
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/languager/store_attributes');
                break;
            case 'products':
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/languager/products_attributes');
                break;
            case 'categories':
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/languager/categories_attributes');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/languager');
                break;
        }
    }

    /**
     * Display all store views with dropdown to associate language to each of them
     */
    public function storesAction()
    {
        $this->_initLayout('Stores')->renderLayout();
    }

    /**
     * Save store view/language configuration
     *
     * @throws Exception
     */
    public function saveStoresAction()
    {
        try {
            if ($data = $this->getRequest()->getPost()) {

                $transaction = Mage::getModel('core/resource_transaction');

                foreach ($data['stores'] as $key => $language) {
                    if (!$language) {
                        continue;
                    }
                    $store = Mage::getModel('core/store')->load($key);
                    $store->setData('gl_language', $language);
                    $transaction->addObject($store);
                }

                $transaction->save();

                $this->_addSuccess('Stores configuration have been saved');
            }
        } catch (Exception $e) {
            $this->_addError($e->getMessage());
        }
        $this->_redirectReferer();
    }

    /**
     * Display all visible product's attributes with checkbox
     * to define if it's value should be save in other similar languages store
     */
    public function productsAction()
    {
        $this->_initLayout('Product\'s attributes')->renderLayout();
    }

    /**
     * Save checked product's attribute
     */
    public function saveProductsAction()
    {
        $this->saveAttributes('catalog/product_attribute_collection');
    }

    /**
     * Display all visible category's attributes with checkbox
     * to define if it's value should be save in other similar languages store
     */
    public function categoriesAction()
    {
        $this->_initLayout('Category\'s attributes')->renderLayout();
    }

    /**
     * Save checked product's attribute
     */
    public function saveCategoriesAction()
    {
        $this->saveAttributes('catalog/category_attribute_collection');
    }

    /**
     * Save attribute's configuration
     *
     * @param string $attributeType
     * @throws Exception
     */
    public function saveAttributes($attributeType)
    {
        try {
            if ($data = $this->getRequest()->getPost()) {

                $transaction = Mage::getModel('core/resource_transaction');

                /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $attributeCollection */
                $attributeCollection = Mage::getResourceModel($attributeType);
                $attributeCollection->addFilter('is_visible', 1);
                $attributeCollection->addFilter('gl_translated', 1);
                $attributes = $attributeCollection->getItems();
                foreach ($attributes as $attribute) {
                    /** @type Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                    $attribute->setData('gl_translated', 0);
                    $transaction->addObject($attribute);
                }

                foreach ($data['attributes'] as $key => $translated) {
                    $attribute = Mage::getResourceModel('catalog/eav_attribute')->load($key);
                    $attribute->setData('gl_translated', $translated);
                    $transaction->addObject($attribute);
                }

                $transaction->save();

                $this->_addSuccess('Attributes have been saved');
            }
        } catch (Exception $e) {
            $this->_addError($e->getMessage());
        }
        $this->_redirectReferer();
    }

    /**
     * Prepare the layout of the page
     *
     * @param $title
     * @return $this
     */
    protected function _initLayout($title)
    {
        $this->loadLayout();
        $this->setUsedModuleName('goodminton_languager');
        $this->_setActiveMenu('system');

        $this->_title(Mage::helper('goodminton_languager')->__('Languager'));
        $this->_title(Mage::helper('goodminton_languager')->__('Configuration'));
        $this->_title(Mage::helper('goodminton_languager')->__($title));

        return $this;
    }

    /**
     * Add success message in the admin session
     *
     * @param string $message
     */
    protected function _addSuccess($message)
    {
        $session = Mage::getSingleton('adminhtml/session');
        $session->addSuccess(Mage::helper('goodminton_languager')->__($message));
    }

    /**
     * Add error message in the admin session
     *
     * @param string $message
     */
    protected function _addError($message)
    {
        $session = Mage::getSingleton('adminhtml/session');
        $session->addError(Mage::helper('goodminton_languager')->__($message));
    }
}