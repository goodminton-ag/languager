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
 * Class Goodminton_Languager_Model_Observer
 */
class Goodminton_Languager_Model_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function propagateProductAttributeTranslation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getEvent()->getData('product');

        $this->propagateAttributeTranslation($product);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function propagateCategoryAttributeTranslation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Category $category */
        $category = $observer->getEvent()->getData('category');

        $this->propagateAttributeTranslation($category);
    }

    /**
     * @param Mage_Catalog_Model_Product|Mage_Catalog_Model_Category $entity
     */
    public function propagateAttributeTranslation($entity)
    {
        if ($entity->getStoreId() === 0) {
            return;
        }
        
        $language = $entity->getStore()->getData('gl_language');
        if (is_null($language)) {
            return ;
        }
        
        /** @var Mage_Core_Model_Resource_Store_Collection $stores */
        $stores = Mage::getModel('core/store')->getCollection();
        $stores->addFilter('gl_language', $language);
        $items = $stores->getItems();
        foreach ($items as $store) {
            /** @type Mage_Core_Model_Store $store */
            $entity->setStoreId($store->getId());
            foreach ($entity->getAttributes() as $attribute) {
                /** @type Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                if ($attribute->getData('gl_translated')) {
                    $entity->setData($attribute->getAttributeCode(), $entity->getData($attribute->getAttributeCode()));
                    $entity->getResource()->saveAttribute($entity, $attribute->getAttributeCode());
                }
            }
        }
    }
}