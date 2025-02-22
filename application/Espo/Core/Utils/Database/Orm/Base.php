<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2021 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Core\Utils\Database\Orm;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;

class Base
{
    private $itemName = null;

    private $entityName = null;

    private $metadata;

    private $ormEntityDefs;

    private $entityDefs;

    protected $config;

    public function __construct(Metadata $metadata, array $ormEntityDefs, array $entityDefs, Config $config)
    {
        $this->metadata = $metadata;
        $this->ormEntityDefs = $ormEntityDefs;
        $this->entityDefs = $entityDefs;
        $this->config = $config;
    }

    protected function getMetadata()
    {
        return $this->metadata;
    }

    protected function getOrmEntityDefs()
    {
        return $this->ormEntityDefs;
    }

    protected function getEntityDefs()
    {
        return $this->entityDefs;
    }

    /**
     * Set current Field name or Link name.
     */
    protected function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     * Get current Field name.
     *
     * @return string
     */
    protected function getFieldName()
    {
        return $this->itemName;
    }

    /**
     * Get current Link name
     *
     * @return string
     */
    protected function getLinkName()
    {
        return $this->itemName;
    }

    /**
     * Set current Entity Name.
     */
    protected function setEntityName($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * Get current Entity Name.
     *
     * @return string
     */
    protected function getEntityName()
    {
        return $this->entityName;
    }

    protected function setMethods(array $keyValueList)
    {
        foreach ($keyValueList as $key => $value) {
            $methodName = 'set' . ucfirst($key);

            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }

    /**
     * Get Entity Defs by type (entity/orm)
     *
     * @param  boolean $isOrmEntityDefs
     * @return array
     */
    protected function getDefs($isOrmEntityDefs = false)
    {
        $entityDefs = $isOrmEntityDefs ? $this->getOrmEntityDefs() : $this->getEntityDefs();

        return $entityDefs;
    }

    /**
     * Get entity params by name.
     *
     * @param  string $entityName
     * @param  bool $isOrmEntityDefs
     * @param  mixed $returns
     * @return mixed
     */
    protected function getEntityParams($entityName = null, $isOrmEntityDefs = false, $returns = null)
    {
        if (!isset($entityName)) {
            $entityName = $this->getEntityName();
        }

        $entityDefs = $this->getDefs($isOrmEntityDefs);

        if (isset($entityDefs[$entityName])) {
            return $entityDefs[$entityName];
        }

        return $returns;
    }

    /**
     * Get field params by name for a specified entity.
     *
     * @param string $fieldName
     * @param string $entityName
     * @param bool $isOrmEntityDefs
     * @param mixed $returns
     * @return mixed
     */
    protected function getFieldParams($fieldName = null, $entityName = null, $isOrmEntityDefs = false, $returns = null)
    {
        if (!isset($fieldName)) {
            $fieldName = $this->getFieldName();
        }
        if (!isset($entityName)) {
            $entityName = $this->getEntityName();
        }

        $entityDefs = $this->getDefs($isOrmEntityDefs);

        if (isset($entityDefs[$entityName]) && isset($entityDefs[$entityName]['fields'][$fieldName])) {
            return $entityDefs[$entityName]['fields'][$fieldName];
        }

        return $returns;
    }

    /**
     * Get relation params by name for a specified entity.
     *
     * @param  string $linkName
     * @param  string $entityName
     * @param  bool $isOrmEntityDefs
     * @param  mixed $returns
     * @return mixed
     */
    protected function getLinkParams($linkName = null, $entityName = null, $isOrmEntityDefs = false, $returns = null)
    {
        if (!isset($linkName)) {
            $linkName = $this->getLinkName();
        }
        if (!isset($entityName)) {
            $entityName = $this->getEntityName();
        }

        $entityDefs = $this->getDefs($isOrmEntityDefs);
        $relationKeyName = $isOrmEntityDefs ? 'relations' : 'links';

        if (isset($entityDefs[$entityName]) && isset($entityDefs[$entityName][$relationKeyName][$linkName])) {
            return $entityDefs[$entityName][$relationKeyName][$linkName];
        }

        return $returns;
    }

    protected function getForeignField(string $name, string $entityType)
    {
        return $name;
    }

    /**
     * Set a value for all elements of array. So, in result all elements will have the same values.
     */
    protected function setArrayValue($inputValue, array $array)
    {
        foreach ($array as &$value) {
            $value = $inputValue;
        }

        return $array;
    }
}
