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
class Goodminton_Languager_Block_Adminhtml_Categories_Container_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'method'    => 'post',
            'action'    => $this->getUrl('*/*/saveCategories')
        ]);

        $fieldset = $form->addFieldset('attributes', [
            'label'     => 'Attribute that will be translated',
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'categories_attributes',
        ]);

        /** @var Mage_Catalog_Model_Resource_Category_Attribute_Collection $attributeCollection */
        $attributeCollection = Mage::getResourceModel('catalog/category_attribute_collection');
        $attributeCollection->addFilter('is_visible', 1);
        $attributes = $attributeCollection->getItems();
        foreach ($attributes as $attribute) {
            /** @type Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            $fieldset->addField('attribute_' . $attribute->getId(), 'checkbox', [
                'label'     => $attribute->getFrontendLabel(),
                'value'     => 1,
                'checked'   => $attribute->getData('gl_translated'),
                'name'      => 'attributes[' . $attribute->getId() . ']'
            ]);
        }

        $form->setData('use_container', true);
        $this->setForm($form);
    }
}