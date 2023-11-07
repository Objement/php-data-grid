if (!Tether) {
    console.error('Please include tetherjs for the Data Grid to work properly. https://tetherjs.dev/');
}

let __DataGridActiveFilterDialog = undefined;

function OmDataGridConfirmationDialogOptions() {
    this.text = '';
    this.type = 'default';
    this.matchingText = '';
}

/**
 * @property undefined|{(dataGrid, confirmationDialogOptions, handleResult) => void} renderConfirmDialogCallback
 * */
function OmDataGridOptions() {
}

/**
 * @type undefined|{(dataGrid, confirmationDialogOptions, handleResult) => void}
 */
OmDataGridOptions.prototype.renderConfirmDialogCallback = function (dataGrid, confirmationDialogOptions, handleResult) {

    let result = false;

    if (confirmationDialogOptions.type === 'critical') {
        const textToMatch = prompt(confirmationDialogOptions.text.replace('{matchingText}', confirmationDialogOptions.matchingText));
        result = (confirmationDialogOptions.matchingText ?? '') === textToMatch;
    } else {
        result = confirm(confirmationDialogOptions.text);
    }

    handleResult(result);
}

/**
 *
 * @param dataGridId
 * @param options
 * @constructor
 */
function OmDataGrid(dataGridId, options = new OmDataGridOptions()) {

    const self = this;

    this.dataGridId = dataGridId;
    let currentTether = undefined;

    /**
     *
     * @type {undefined|Function}
     */
    let renderConfirmDialogCallback = undefined;

    /**
     *
     * @param type
     * @param text
     * @param matchingText
     * @returns boolean
     */
    function openConfirmDialog(type, text, matchingText = undefined) {

        return new Promise((resolve, reject) => {

            if (!renderConfirmDialogCallback) {
                resolve(true);
            }

            /**
             * @type OmDataGridConfirmationDialogOptions
             */
            const options = {
                type,
                text,
                matchingText
            }

            renderConfirmDialogCallback(self, options, resolve);
        });
    }

    function clickButtonOnEnterPress(field, button) {
        field.addEventListener("keydown", function (e) {
            if (e.keyCode === 13) {
                button.click();
                button.disabled = true;
            }
        })
    }

    function init() {

        renderConfirmDialogCallback = options.renderConfirmDialogCallback;

        const elemDataGrid = document.querySelector('.data-grid__container[data-data-grid-id="' + dataGridId + '"]')

        const fieldSearch = elemDataGrid.querySelector('.data-grid__header__search-field');
        if (fieldSearch) {
            const buttonSearch = elemDataGrid.querySelector('.data-grid__header__search-button');
            buttonSearch.addEventListener('click', () => dataGridAddSearch(dataGridId, fieldSearch.value));
            clickButtonOnEnterPress(fieldSearch, buttonSearch);
        }

        const fieldSpecialFilter = elemDataGrid.querySelector('.data-grid__header__special-filter-field');
        if (fieldSpecialFilter) {
            fieldSpecialFilter.addEventListener('change', (e) => dataGridAddSpecialFilter(dataGridId, e.currentTarget.value));
        }

        const filterButtons = elemDataGrid.querySelectorAll('.data-grid__header-cell__filter-button');
        for (let filterButton of filterButtons) {
            filterButton.addEventListener('click', function (e) {
                openFilterDialog(filterButton, filterButton.dataset.columnIndex, filterButton.dataset.currentOperation, filterButton.dataset.currentCriteria)
            });
        }

        const fieldEntriesPerpage = elemDataGrid.querySelector('.data-grid__header__entries-per-page-field');
        if (fieldEntriesPerpage) {
            fieldEntriesPerpage.addEventListener('change', (e) => dataGridSetEntriesPerPage(dataGridId, e.currentTarget.value));
        }

        document.body.addEventListener('click', (event) => {
            closeFilterDialog(false, event);
        });

        const rowOptionButtonsWithConfirmation = elemDataGrid.querySelectorAll('.data-grid__column__option-link--confirmation-needed');
        for (let rowOptionButtonWithConfirmation of rowOptionButtonsWithConfirmation) {
            rowOptionButtonWithConfirmation.addEventListener('click', function (e) {
                // when triggered by .click(), which is the case when the result is "true" from the render dialog below.
                if (e.isTrusted === false) {
                    return true;
                }

                const linkElement = e.currentTarget;

                e.preventDefault();
                e.stopPropagation();

                const buttonDataSet = linkElement.dataset;
                linkElement.href = linkElement.dataset.href;

                openConfirmDialog(buttonDataSet.confirmationType, buttonDataSet.confirmationText, buttonDataSet.confirmationMatchingText ?? undefined)
                    .then((hasUserConfirmed) => {
                        if (hasUserConfirmed) {
                            linkElement.click()
                        }
                    });

                return false;
            });
        }

        const elemResetFilter = elemDataGrid.querySelector('.data-grid__container--filter-active .data-grid__header__reset-filter-button');
        if (elemResetFilter) {
            elemResetFilter.addEventListener('click', function (e) {
                const resetButton = e.currentTarget;
                const dataGridId = resetButton.dataset.dataGridId;

                const urlParams = new URLSearchParams(window.location.search);
                const filteredParams = [...urlParams.keys()]
                    .filter(key => !key.startsWith('d' + dataGridId))
                    .map(key => `${key}=${urlParams.get(key)}`)
                    .join('&');

                window.location.href = `${window.location.origin}${window.location.pathname}${filteredParams ? '?' + filteredParams : ''}`;
            });
        }
    }

    function closeFilterDialog(force = false, event = undefined) {
        if (!currentTether) {
            return;
        }

        if (force || (event && !currentTether.element.contains(event.target) && !currentTether.target.contains(event.target))) {
            currentTether.element.style.display = 'none';
            currentTether.destroy();
            currentTether = undefined;
            __DataGridActiveFilterDialog = undefined;
        }
    }

    function getTemplateElement(selector) {
        const template = document.querySelector(selector);
        return template.content.firstElementChild.cloneNode(true);
    }

    function getFilterDialogFilterFieldsByColumnType(type) {

        let templateSelector = '#dataGrid' + self.dataGridId + 'FilterDialogTemplateFilterText';

        if (type == 'date') {
            templateSelector = '#dataGrid' + self.dataGridId + 'FilterDialogTemplateFilterDate';
        }

        return getTemplateElement(templateSelector);
    }

    function openFilterDialog(filterButton, columnIndex, currentOperation, currentCriteria) {

        if (__DataGridActiveFilterDialog !== undefined) {
            closeFilterDialog(true);
        }

        let elemDialog = __DataGridActiveFilterDialog;

        if (elemDialog === undefined) {
            elemDialog = getTemplateElement('#dataGrid' + self.dataGridId + 'FilterDialogTemplate');
            __DataGridActiveFilterDialog = elemDialog;

            const elemDialogFilterFields = getFilterDialogFilterFieldsByColumnType(filterButton?.dataset.columnType);
            elemDialog.querySelector('.data-grid__header-cell__filter__filter-fields').append(elemDialogFilterFields);

            const elemOperationField = elemDialog.querySelector('.data-grid__header-cell__filter__operation-field');
            const elemCriteriaField = elemDialog.querySelector('.data-grid__header-cell__filter__criteria-field');

            const elemAddFilterButton = elemDialog.querySelector('.data-grid__header-cell__filter__add-button');
            const elemCancelButton = elemDialog.querySelector('.data-grid__header-cell__filter__cancel-button');

            clickButtonOnEnterPress(elemCriteriaField, elemAddFilterButton);

            elemAddFilterButton.addEventListener('click', function (e) {
                dataGridAddFilter({
                    columnIndex: columnIndex,
                    operation: elemOperationField.value,
                    criteria: elemCriteriaField.value
                });
            })
            elemCancelButton.addEventListener('click', function (e) {
                closeFilterDialog(true);
            });
            document.body.append(elemDialog);
        }

        if (currentOperation && currentCriteria) {
            elemDialog.querySelector('.data-grid__header-cell__filter__operation-field').value = currentOperation;
            elemDialog.querySelector('.data-grid__header-cell__filter__criteria-field').value = currentCriteria;
        }

        elemDialog.style.display = 'block';

        currentTether = new Tether({
            element: elemDialog,
            target: filterButton,
            attachment: 'top left',
            targetAttachment: 'bottom left',
            constraints: [
                {
                    to: 'window',
                    pin: true
                }
            ]
        });

        elemDialog.querySelector('.data-grid__header-cell__filter__criteria-field').focus();
    }

    function getDataGridFilterForm(dataGridId) {
        return document.getElementById('dataGrid' + dataGridId + 'FilterForm');
    }

    function dataGridAddSearch(dataGridId, searchQuery) {
        const dataGridFilterForm = getDataGridFilterForm(dataGridId);

        const inputNameSearch = 'd' + dataGridId + 'q';

        const inputSearchQuery = document.getElementsByName(inputNameSearch)?.[0] || document.createElement('input');
        if (searchQuery === '') {
            inputSearchQuery.remove();
        } else {
            inputSearchQuery.type = 'hidden';
            inputSearchQuery.name = inputNameSearch;
            inputSearchQuery.value = searchQuery;
            dataGridFilterForm.append(inputSearchQuery);
        }

        dataGridFilterForm.submit();
    }

    function dataGridAddFilter(filter) {
        const dataGridFilterForm = getDataGridFilterForm(dataGridId);

        const inputNameOperation = 'd' + dataGridId + 'c' + filter.columnIndex + '_o';
        const inputNameCriteria = 'd' + dataGridId + 'c' + filter.columnIndex + '_c';

        const inputCriteria = document.getElementsByName(inputNameCriteria)?.[0] || document.createElement('input');
        const inputOperation = document.getElementsByName(inputNameOperation)?.[0] ?? document.createElement('input');

        if (filter.criteria === '') {
            inputCriteria.remove();
            inputOperation.remove();
        } else {
            inputCriteria.type = 'hidden';
            inputCriteria.name = inputNameCriteria;
            inputCriteria.value = filter.criteria;
            dataGridFilterForm.append(inputCriteria);


            inputOperation.type = 'hidden';
            inputOperation.name = inputNameOperation;
            inputOperation.value = filter.operation;
            dataGridFilterForm.append(inputOperation);
        }

        dataGridFilterForm.submit();
    }

    function dataGridAddSpecialFilter(dataGridId, filterIndex) {
        const dataGridFilterForm = getDataGridFilterForm(dataGridId);

        const inputNameSpecialFilter = 'd' + dataGridId + 'sf';

        const inputSpecialFilter = document.getElementsByName(inputNameSpecialFilter)?.[0] || document.createElement('input');

        if (filterIndex === '') {
            inputSpecialFilter.remove()
        } else {
            inputSpecialFilter.type = 'hidden';
            inputSpecialFilter.name = inputNameSpecialFilter;
            inputSpecialFilter.value = filterIndex;
            dataGridFilterForm.append(inputSpecialFilter);
        }

        dataGridFilterForm.submit();
    }

    function dataGridSetEntriesPerPage(dataGridId, option) {
        const dataGridFilterForm = getDataGridFilterForm(dataGridId);

        const inputNameCurrentPage = 'd' + dataGridId + 'cp';
        const inputNameEntriesPerPage = 'd' + dataGridId + 'epp';

        const inputCurrentPage = document.getElementsByName(inputNameCurrentPage)?.[0] || document.createElement('input');
        const inputEntriesPerPage = document.getElementsByName(inputNameEntriesPerPage)?.[0] || document.createElement('input');

        if (option === '') {
            inputCurrentPage.remove();
            inputEntriesPerPage.remove()
        } else {
            inputCurrentPage.type = 'hidden';
            inputCurrentPage.name = inputNameCurrentPage;
            inputCurrentPage.value = 1; // reset page to 1 if the "entries per page" changes
            dataGridFilterForm.append(inputCurrentPage);

            inputEntriesPerPage.type = 'hidden';
            inputEntriesPerPage.name = inputNameEntriesPerPage;
            inputEntriesPerPage.value = option;
            dataGridFilterForm.append(inputEntriesPerPage);
        }

        dataGridFilterForm.submit();
    }

    init()
}

/**
 *
 * @param {OmDataGridOptions} options
 */
OmDataGrid.init = function (options) {
    const dataGrids = document.querySelectorAll(".data-grid");
    for (let dataGrid of dataGrids) {
        new OmDataGrid(dataGrid.dataset.dataGridId, options);
    }
};