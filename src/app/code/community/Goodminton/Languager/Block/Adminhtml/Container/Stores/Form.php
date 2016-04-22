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
 * Class Goodminton_Languager_Block_Adminhtml_Container_Stores_Form
 */
class Goodminton_Languager_Block_Adminhtml_Container_Stores_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'method'    => 'post',
            'action'    => $this->getUrl('*/*/saveStores')
        ]);

        $fieldset = $form->addFieldset('stores', [
            'legend'     => Mage::helper('goodminton_languager')->__('Store/Language mapping'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'stores',
        ]);

        $stores = Mage::getModel('core/store')->getCollection();
        foreach ($stores as $store) {
            /** @type Mage_Core_Model_Store $store */
            $fieldset->addField('store_' . $store->getId(), 'select', [
                'label'     => $store->getName() . ' (' . $store->getCode() . ')',
                'values'    => Zend_Locale::getTranslationList('language'),
                'value'     => $store->getData('gl_language'),
                'name'      => 'stores[' . $store->getId() . ']'
            ]);
        }

        $form->setData('use_container', true);
        $this->setForm($form);
    }
}