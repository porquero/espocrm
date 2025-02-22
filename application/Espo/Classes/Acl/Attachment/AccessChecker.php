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

namespace Espo\Classes\Acl\Attachment;

use Espo\Entities\{
    User,
    Note,
};

use Espo\ORM\Entity;

use Espo\Core\{
    ORM\EntityManager,
    AclManager,
    Acl\ScopeData,
    Acl\DefaultAccessChecker,
    Acl\AccessEntityCREDChecker,
    Acl\Traits\DefaultAccessCheckerDependency,
};

class AccessChecker implements AccessEntityCREDChecker
{
    use DefaultAccessCheckerDependency;

    private $aclManager;

    private $entityManager;

    public function __construct(
        DefaultAccessChecker $defaultAccessChecker,
        AclManager $aclManager,
        EntityManager $entityManager
    ) {
        $this->defaultAccessChecker = $defaultAccessChecker;
        $this->aclManager = $aclManager;
        $this->entityManager = $entityManager;
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($entity->get('parentType') === 'Settings') {
            return true;
        }

        $parent = null;

        $hasParent = false;

        if ($entity->get('parentId') && $entity->get('parentType')) {
            $hasParent = true;

            $parent = $this->entityManager->getEntity($entity->get('parentType'), $entity->get('parentId'));
        }
        else if ($entity->get('relatedId') && $entity->get('relatedType')) {
            $hasParent = true;

            $parent = $this->entityManager->getEntity($entity->get('relatedType'), $entity->get('relatedId'));
        }

        if (!$parent || !$hasParent) {
            if ($this->defaultAccessChecker->checkEntityRead($user, $entity, $data)) {
                return true;
            }

            return false;
        }

        if ($parent->getEntityType() === 'Note') {
            /** @var Note $parent */
            $result = $this->checkEntityReadNoteParent($user, $parent);

            if ($result !== null) {
                return $result;
            }
        }
        else if ($this->aclManager->checkEntity($user, $parent)) {
            return true;
        }

        if ($this->defaultAccessChecker->checkEntityRead($user, $entity, $data)) {
            return true;
        }

        return false;
    }

    private function checkEntityReadNoteParent(User $user, Note $note): ?bool
    {
        if ($note->getTargetType() === Note::TARGET_TEAMS) {
            $intersect = array_intersect(
                $note->getLinkMultipleIdList('teams'),
                $user->getLinkMultipleIdList('teams')
            );

            if (count($intersect)) {
                return true;
            }

            return null;
        }

        if ($note->getTargetType() === Note::TARGET_USERS) {
            $isRelated = $this->entityManager
                ->getRDBRepository('Note')
                ->getRelation($note, 'users')
                ->isRelated($user);

            if ($isRelated) {
                return true;
            }

            return null;
        }

        if (!$note->getParentId() || !$note->getParentType()) {
            return null;
        }

        $parent = $this->entityManager->getEntity($note->getParentType(), $note->getParentId());

        if ($parent && $this->aclManager->checkEntity($user, $parent)) {
            return true;
        }

        return null;
    }
}
