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
     * Set a flag to deactivate attribute propagation
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventAttributePropagation(Varien_Event_Observer $observer)
    {
        Mage::register('languager_deactivate_propagation', true, true);
    }

    /**
     * Propagate translation for a product's attributes
     *
     * @param Varien_Event_Observer $observer
     */
    public function propagateProductAttributeTranslation(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getData('product');

        $this->propagateAttributeTranslation($product);
    }

    /**
     * Propage translation for a category's attributes
     *
     * @param Varien_Event_Observer $observer
     */
    public function propagateCategoryAttributeTranslation(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getData('category');

        $this->propagateAttributeTranslation($category);
    }

    /**
     * Propagate translation to all entities's attributes with the similar language
     *
     * @param Mage_Catalog_Model_Product|Mage_Catalog_Model_Category $entity
     */
    public function propagateAttributeTranslation($entity)
    {
        if (Mage::getStoreConfig('goodminton_languager_config/languager/activated') != 1) {
            return ;
        }

        if (Mage::registry('languager_deactivate_propagation')) {
            return ;
        }

        if ($entity->getStoreId() === 0) {
            return;
        }

        $items = $this->getSimilarStores($entity->getStore());
        if (!$items) {
            return ;
        }
        $storesName = [];
        foreach ($items as $store) {

            $entity->setStoreId($store->getId());

            $storesName[] = Mage::helper('goodminton_languager')->__($store->getName()) . ' (' . $store->getCode() . ')';

            foreach ($entity->getAttributes() as $attribute) {
                /** @type Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                if ($attribute->getData('gl_translated')) {
                    if ($entity->getData($attribute->getAttributeCode())) {
                        $entity->getResource()->saveAttribute($entity, $attribute->getAttributeCode());
                    }
                }
            }
        }
        $success = Mage::helper('goodminton_languager')->__('Attributes values saved in stores');
        Mage::getSingleton('adminhtml/session')->addSuccess($success . ' ' . implode(', ', $storesName));
    }

    /**
     * Add all store views with similar language to a CMS block
     *
     * @param Varien_Event_Observer $observer
     */
    public function selectStoreViewForLanguage(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('goodminton_languager_config/languager/activated') != 1) {
            return ;
        }

        $object = $observer->getEvent()->getData('object');
        if ($object instanceof Mage_Cms_Model_Block) {
            $newStores = $object->getData('stores');
            if (!is_array($newStores)) {
                return;
            }
            $usedStores = $this->getUsedStore($object->getData('identifier'));
            foreach ($object->getData('stores') as $store) {
                $similarStores = $this->getSimilarStores($store, $usedStores);
                if (!$similarStores) {
                    continue ;
                }
                foreach ($similarStores as $similarStore) {
                    if (!in_array($similarStore->getId(), $newStores)) {
                        $newStores[] = $similarStore->getId();
                    }
                }
            }
            $object->setData('stores', $newStores);
        }
    }

    /**
     * Get all store with similar language as the one given
     *
     * @param Mage_Core_Model_Store|integer $store
     * @param array $exclude
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    protected function getSimilarStores($store, $exclude = [])
    {
        if (!$store instanceof Mage_Core_Model_Store) {
            $store = Mage::getModel('core/store')->load($store);
        }
        $stores = Mage::getModel('core/store')->getCollection();
        $stores->addFilter('gl_language', $store->getData('gl_language'));
        if (!empty($exclude)) {
            $stores->addFieldToFilter('store_id', ['nin' => $exclude]);
        }
        $items = $stores->getItems();
        if (empty($items)) {
            return false;
        }
        return $items;
    }

    /**
     * Get all used store ids for the given block identifier
     *
     * @param $blockIdentifier
     * @return array
     */
    protected function getUsedStore($blockIdentifier)
    {
        if (($storeIds = Mage::registry('languager_' . $blockIdentifier)) !== null) {
            return $storeIds;
        }
        $coreRead = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $coreRead->select()
            ->from(array('cb' => $coreRead->getTableName('cms_block')), ['cb.block_id'])
            ->join(
                array('cbs' => $coreRead->getTableName('cms_block_store')),
                'cb.block_id = cbs.block_id',
                ['cbs.store_id']
            )->where('cb.identifier = ?', $blockIdentifier)
        ;
        $stmt = $coreRead->query($select);
        $storeIds = $stmt->fetchAll(Zend_Db::FETCH_COLUMN, 1);
        Mage::register('languager_' . $blockIdentifier, $storeIds);
        return $storeIds;
    }
}