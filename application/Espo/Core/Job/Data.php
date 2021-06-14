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

namespace Espo\Core\Job;

use Espo\Core\Utils\ObjectUtil;

use TypeError;
use stdClass;

class Data
{
    private $data;

    private $targetId = null;

    private $targetType = null;

    public function __construct(?stdClass $data = null)
    {
        $this->data = $data ?? (object) [];
    }

    /**
     * Create an instance.
     *
     * @param stdClass|array|null $data Raw data.
     * @return self
     */
    public static function create($data = null): self
    {
        if ($data !== null && !is_object($data) && !is_array($data)) {
            throw new TypeError();
        }

        if (is_array($data)) {
            $data = (object) $data;
        }

        return new self($data);
    }

    public function getRaw(): stdClass
    {
        return ObjectUtil::clone($this->data);
    }

    /**
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->getRaw()->$name ?? null;
    }

    public function has(string $name): bool
    {
        return property_exists($this->data, $name);
    }

    public function getTargetId(): ?string
    {
        return $this->targetId;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function withTargetId(?string $targetId): self
    {
        $obj = clone $this;

        $obj->targetId = $targetId;

        return $obj;
    }

    public function withTargetType(?string $targetType): self
    {
        $obj = clone $this;

        $obj->targetType = $targetType;

        return $obj;
    }
}
