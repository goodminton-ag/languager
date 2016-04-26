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

/** @var Mage_Sales_Model_Mysql4_Setup $this */
$this->startSetup();

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $this->getConnection();

/**
 * Adding the column that will save the language used by the store
 */
$connection->addColumn($this->getTable('core/store'), 'gl_language',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => '5',
        'nullable'  => true,
        'comment'   => 'Define the language used for this store'
    ]
);

/**
 * Adding the column that will define if an attribute should be considered as translated
 * For example a price is a not a translated value
 */
$connection->addColumn($this->getTable('catalog/eav_attribute'), 'gl_translated',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length'    => '1',
        'nullable'  => true,
        'comment'   => 'Define if the attribute should be considered as translated'
    ]
);

/**
 * Setting standard attribute as 'to translate'
 */
$typesAttributes = [
    Mage_Catalog_Model_Product::ENTITY => [
        'name',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keyword'
    ],
    Mage_Catalog_Model_Category::ENTITY => [
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keyword'
    ]
];

foreach ($typesAttributes as $type => $attributes) {
    foreach ($attributes as $attribute) {
        $this->updateAttribute($type, $attribute, 'gl_translated', 1);
    }
}

$this->endSetup();