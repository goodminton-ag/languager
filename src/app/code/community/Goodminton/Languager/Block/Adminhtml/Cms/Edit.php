<?php
/**
 * Block override to add an extra save button
 *
 * @package   Goodminton_Languager
 * @copyright 2018 foodspring GmbH <https://www.foodspring.com>
 * @author    Pierre Bernard <pierre.bernard@foodspring.com>
 */

class Goodminton_Languager_Block_Adminhtml_Cms_Edit
    extends Goodminton_Languager_Block_Adminhtml_Cms_EditAbstract
{
    public function __construct()
    {
        parent::__construct();

        $this->_addButton(
            'savenolanguager',
            [
                'label'     => Mage::helper('adminhtml')->__('Save and bypass languager'),
                'onclick'   => 'saveNoLanguager()',
                'class'     => 'save',
            ],
            -100
        );

        $this->_formScripts[] = '
            function saveNoLanguager(){
                editForm.validator.form.insert(\'<input type="hidden" value="1" name="block_propagation"/>\');
                saveAndContinueEdit();
            }
        ';
    }
}