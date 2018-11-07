<?php
/**
 * Block override to add an extra save button
 *
 * @package   Goodminton_Languager
 * @copyright 2018 foodspring GmbH <https://www.foodspring.com>
 * @author    Pierre Bernard <pierre.bernard@foodspring.com>
 */

if (Mage::helper('core')->isModuleEnabled('Hackathon_MultistoreBlocks')) {
    abstract class Goodminton_Languager_Block_Adminhtml_Cms_EditAbstract
    extends Hackathon_MultistoreBlocks_Block_Adminhtml_Cms_Block_Edit
    {

    }
} else {
    abstract class Goodminton_Languager_Block_Adminhtml_Cms_EditAbstract
    extends Mage_Adminhtml_Block_Cms_Block_Edit
    {

    }
}