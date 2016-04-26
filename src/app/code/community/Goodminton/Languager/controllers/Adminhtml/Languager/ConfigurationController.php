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
    /**
     * Display all store views with dropdown to associate language to each of them
     */
    public function storesAction()
    {
        $this->_initLayout();

        $attributes = [
            'header_text' => Mage::helper('goodminton_languager')->__('Languager store management'),
            'mode' => 'container_stores'
        ];

        $block = $this->getLayout()->createBlock('goodminton_languager/adminhtml_container', 'attributes_block', $attributes);

        $this->_addContent($block);

        $this->renderLayout();
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
        $this->_initLayout(true);

        $attributes = [
            'form' => [
                'action_path' => '*/*/saveProducts',
                'resource_model' => 'catalog/product_attribute_collection'
            ],
            'header_text' => Mage::helper('goodminton_languager')->__('Languager products attributes management'),
            'mode' => 'container_attributes'
        ];

        $block = $this->getLayout()->createBlock('goodminton_languager/adminhtml_container', 'attributes_block', $attributes);

        $this->_addContent($block);

        $this->renderLayout();
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
        $this->_initLayout(true);

        $attributes = [
            'form' => [
                'action_path' => '*/*/saveCategories',
                'resource_model' => 'catalog/category_attribute_collection'
            ],
            'header_text' => Mage::helper('goodminton_languager')->__('Languager categories attributes management'),
            'mode' => 'container_attributes'
        ];

        $block = $this->getLayout()->createBlock('goodminton_languager/adminhtml_container', 'attributes_block', $attributes);

        $this->_addContent($block);

        $this->renderLayout();
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
     * @param bool $extraAssets
     * @return $this
     */
    protected function _initLayout($extraAssets = false)
    {
        $this->loadLayout();
        $this->setUsedModuleName('goodminton_languager');
        $this->_setActiveMenu('system');

        $this->_title(Mage::helper('goodminton_languager')->__('Languager'));
        $this->_title(Mage::helper('goodminton_languager')->__('Configuration'));

        if ($extraAssets) {
            /** @var Mage_Page_Block_Html_Head $headBlock */
            $headBlock = $this->getLayout()->getBlock('head');
            $headBlock->addJs('goodminton/languager/admin.js');
            $headBlock->addItem('skin_js', 'goodminton/languager/jquery-ui/jquery-ui.min.js');
            $headBlock->addItem('skin_css', 'goodminton/languager/jquery-ui/jquery-ui.min.css');
            $headBlock->addItem('skin_css', 'goodminton/languager/css/style.css');
        }

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