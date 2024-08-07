/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM – Open Source CRM application.
 * Copyright (C) 2014-2024 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
 * Website: https://www.espocrm.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

import ListRecordView from 'views/record/list';

export default class extends ListRecordView {

    massUpdateDisabled = true
    mergeDisabled = true
    exportDisabled = true
    removeDisabled = true

    rowActionsView = 'views/email-folder/record/row-actions/default'

    // noinspection JSUnusedGlobalSymbols
    actionMoveUp(data) {
        const model = this.collection.get(data.id);

        if (!model) {
            return;
        }

        const index = this.collection.indexOf(model);

        if (index === 0) {
            return;
        }

        Espo.Ajax.postRequest('EmailFolder/action/moveUp', {id: model.id})
            .then(() => {
                this.collection.fetch();
            });
    }

    // noinspection JSUnusedGlobalSymbols
    actionMoveDown(data) {
        const model = this.collection.get(data.id);

        if (!model) {
            return;
        }

        const index = this.collection.indexOf(model);

        if ((index === this.collection.length - 1) && (this.collection.length === this.collection.total)) {
            return;
        }

        Espo.Ajax.postRequest('EmailFolder/action/moveDown', {id: model.id})
            .then(() => {
                this.collection.fetch();
            });
    }
}
