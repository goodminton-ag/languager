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
 * Class Goodminton_Languager_Block_Adminhtml_Container_Attributes_Form
 */
class Goodminton_Languager_Block_Adminhtml_Container_Attributes_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'method'    => 'post',
            'action'    => $this->getUrl($this->getData('action_path'))
        ]);

        $fieldset = $form->addFieldset('attributes', [
            'legend'     => Mage::helper('goodminton_languager')->__('Attributes that will be translated'),
            'class'     => 'required-entry',
            'required'  => true
        ]);
        
        $attributeCollection = Mage::getResourceModel($this->getData('resource_model'));
        $attributeCollection->addFilter('is_visible', 1);
        $attributeCollection->setOrder('frontend_label', $attributeCollection::SORT_ORDER_ASC);
        $attributes = $attributeCollection->getItems();
        foreach ($attributes as $attribute) {
            $fieldset->addField('attribute_' . $attribute->getId(), 'checkbox', [
                'label'     => Mage::helper('goodminton_languager')->__($attribute->getFrontendLabel()),
                'value'     => 1,
                'checked'   => $attribute->getData('gl_translated'),
                'name'      => 'attributes[' . $attribute->getId() . ']'
            ]);
        }

        $form->setData('use_container', true);
        $this->setForm($form);
    }
}