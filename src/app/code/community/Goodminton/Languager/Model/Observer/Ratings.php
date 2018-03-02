<?php
/**
 * Observer for ratings
 *
 * @package   Goodminton_Languager
 * @copyright 2017 foodspring GmbH <https://www.foodspring.com>
 * @author    Pawel Waniek <pawel@foodspring.com>
 */

class Goodminton_Languager_Model_Observer_Ratings
{

    /**
     * Map stores
     * Event: review_save_before
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     */
    public function mapStores(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfigFlag('goodminton_languager_config/languager/activated')) {
            return;
        }

        $event = $observer->getEvent();
        /* @var Mage_Review_Model_Review $review */
        $review = $event->getData('object');

        if ($this->_hasStoreChanged($review)) {
            return;
        }

        $storeId = $review->getData('store_id');
        $mappedStores = $this->_getMappedStoreIds($storeId);

        if (!empty($mappedStores) && in_array($storeId, $mappedStores)) {
            $review->setData('stores', $mappedStores);
        }
    }

    /**
     * Check if store was manual changed
     *
     * @param Mage_Review_Model_Review $review
     *
     * @return bool
     */
    protected function _hasStoreChanged($review)
    {
        $stores = $review->getData('stores');
        $originalStores = $review->getOrigData('stores');
        $adminStore = array_search('0', $originalStores);
        if ($adminStore !== false) {
            unset($originalStores[$adminStore]);
        }

        if (!in_array($review->getData('store_id'), $stores)) {
            return true;
        }

        return $originalStores == $stores;
    }

    /**
     * Get mapped Store Ids
     *
     * @param int $storeId
     *
     * @return array
     */
    protected function _getMappedStoreIds($storeId)
    {
        $stores = [];
        $baseLanguage = $this->_getLanguageByStoreId($storeId);
        $storeCollection = Mage::getModel('core/store')->getCollection();

        /* @type Mage_Core_Model_Store $store */
        foreach ($storeCollection as $store) {
            $languageCode = $this->_parseLanguageCode($store->getData('gl_language'));
            if ($languageCode == $baseLanguage) {
                $stores[] = $store->getId();
            }
        }

        return $stores;
    }

    /**
     * Get Language Code by store id
     *
     * @param int $storeId
     *
     * @return string
     */
    protected function _getLanguageByStoreId($storeId)
    {
        $store = Mage::getModel('core/store')->load($storeId);

        return $this->_parseLanguageCode($store->getData('gl_language'));
    }

    /**
     * Parse language Code
     *
     * @param string $code
     *
     * @return string
     */
    protected function _parseLanguageCode($code)
    {
        if (strpos($code, '_') !== false) {
            $parts = explode('_', $code);
            $code = $parts[0];
        }

        return $code;
    }
}