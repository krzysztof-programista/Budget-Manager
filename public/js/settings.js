//Const declarations
const addEditModal = document.querySelector('#add-edit-modal'); //cały wyskakujacy modal add-edit, dodajemy do niego atrybuty data-category, data-action, data-id
const addEditTitle = document.querySelector('#add-edit-title'); //tytuł modala
const addEditForm = document.querySelector('#add-edit-form');  //formularz w modalu z current-category-name, new-category-name, 2 potencjalnycmi polami na checkbox/label oraz add-edit-submit-form-btn,
const currentCategoryName = document.querySelector('#current-category-name');//nazwa edytowanej kategorii
const newCategoryName = document.querySelector('#new-category-name-parent'); //nowa nazwa kategorii

const limitCheckBox = document.querySelector('#limit-checkbox');//checkbox
const limitValue = document.querySelector('#limit-value');//label dla checkboxa

const addEditMessage = document.querySelector('#add-edit-message'); //wiadomość z informacją zwrotną z modelu o statusie
const addEditSubmitFormBtn = document.querySelector('#add-edit-submit-form-btn');//przycisk modala, do niego dodajemy addEventListener


const removeModal = document.querySelector('#remove-modal'); //cały wyskakujacy modal remove
const removeTitle = document.querySelector('#remove-title'); //tytuł modala
const removeMessage = document.querySelector('#remove-message'); //wiadomość z informacją zwrotną z modelu o statusie
const removeBtn = document.querySelector('#remove-btn');//przycisk modala


// Functions for add/edit
const prepareForm = (action, category, id, name, cash_limit) => {

    appendNewCategoryNameElement();
    
    addEditModal.setAttribute('data-action', action);
    addEditModal.setAttribute('data-category', category);

    if (id !== 0) {
        addEditModal.setAttribute('data-id', id);
    }

    switch (action) {
        case 'add':
            addEditSubmitFormBtn.innerText = `Dodaj kategorię`;

            switch (category) {
                case 'income':
                    addEditTitle.innerText = `Dodaj nową kategorię przychodów`;
                    break;
                case 'expense':
                    addEditTitle.innerText = `Dodaj nową kategorię wydatków`;
                    appendAddLimitElements();
                    break;
                case 'payment':
                    addEditTitle.innerText = `Dodaj nowy sposób płatności`;
                    break;
                default:
            }
            break;

        case 'edit':

            addEditSubmitFormBtn.innerText = `Edytuj kategorię`;
            addEditModal.setAttribute('data-name', name);

            appendCurrentCategoryNameElement();

            switch (category) {
                case 'income':
                    addEditTitle.innerText = `Edytuj kategorię przychodów`;
                    break;
                case 'expense':
                    addEditTitle.innerText = `Edytuj kategorię wydatków`;
                    addEditModal.setAttribute('data-cash_limit', cash_limit);
                    appendEditLimitElements();
                    break;
                case 'payment':
                    addEditTitle.innerText = `Edytuj sposób płatnosci`;
                    break;
                default:
            }
            break;

        default:
    }
}

const appendNewCategoryNameElement = () => {
    const newCategoryNameDiv = document.createElement('div');
    const newCategoryNameInput = document.createElement('input');
    const newCategoryNameLabel = document.createElement('label');

    newCategoryNameDiv.setAttribute('class','form-floating');

    newCategoryNameInput.setAttribute('type','text');
    newCategoryNameInput.setAttribute('class','form-control');
    newCategoryNameInput.setAttribute('class','form-control');
    newCategoryNameInput.setAttribute('id','new-category-name');
    newCategoryNameInput.setAttribute('name','new-category-name');
    newCategoryNameInput.required = true;

    newCategoryNameLabel.setAttribute('for', 'new-category-name')
    newCategoryNameLabel.innerText = 'Nazwa kategorii:';

    newCategoryName.appendChild(newCategoryNameDiv);
    newCategoryNameDiv.appendChild(newCategoryNameInput);
    newCategoryNameDiv.appendChild(newCategoryNameLabel);
}

const appendCurrentCategoryNameElement = () => {
    const currentCategoryNameForm = document.createElement('form');
    const currentCategoryNameInput = document.createElement('input');
    const currentCategoryNameLabel = document.createElement('label');

    currentCategoryNameForm.setAttribute('class','form-floating');

    currentCategoryNameInput.setAttribute('type','text');
    currentCategoryNameInput.setAttribute('class','form-control');
    currentCategoryNameInput.setAttribute('id','current-category-name');
    currentCategoryNameInput.value = addEditModal.getAttribute('data-name');
    currentCategoryNameInput.disabled = true;

    currentCategoryNameLabel.setAttribute('for', 'current-category-name')
    currentCategoryNameLabel.innerText = 'Aktualna nazwa kategorii:';

    currentCategoryName.appendChild(currentCategoryNameForm);
    currentCategoryNameForm.appendChild(currentCategoryNameInput);
    currentCategoryNameForm.appendChild(currentCategoryNameLabel);
}

const appendAddLimitElements = () => {
    const limitFormDiv = document.createElement('div');
    const checkBox = document.createElement('input');
    const checkBoxLabel = document.createElement('label');

    const limitValueInput = document.createElement('input');

    limitFormDiv.setAttribute('class', 'form-check additional');

    checkBox.setAttribute('class', 'form-check-input');
    checkBox.setAttribute('id', 'modal-checkbox');
    checkBox.setAttribute('value', '');
    checkBox.setAttribute('type', 'checkbox');
    checkBox.setAttribute('onclick', 'activateAddLimitField(this)');
    
    checkBoxLabel.setAttribute('class', 'form-check-label');
    checkBoxLabel.setAttribute('for', 'modal-checkbox');
    checkBoxLabel.innerText = 'Dodaj limit do kategorii';

    limitValueInput.setAttribute('class', 'form-control additional');
    limitValueInput.setAttribute('id', 'category-limit');
    limitValueInput.setAttribute('type', 'number');
    limitValueInput.setAttribute('name', 'category-limit');
    limitValueInput.setAttribute('min', '0');
    limitValueInput.setAttribute('step', '0.01');
    limitValueInput.setAttribute('placeholder', 'Podaj kwotę limitu');
    limitValueInput.disabled = true;

    limitCheckBox.appendChild(limitFormDiv);
    limitFormDiv.appendChild(checkBox);
    limitFormDiv.appendChild(checkBoxLabel);

    limitValue.appendChild(limitValueInput);
}

const appendEditLimitElements = () => {
    const limitValueCurrent = addEditModal.getAttribute('data-cash_limit');

    const limitFormDiv = document.createElement('div');
    const checkBox = document.createElement('input');
    const checkBoxLabel = document.createElement('label');

    const limitValueInput = document.createElement('input');

    limitFormDiv.setAttribute('class', 'form-check additional');

    checkBox.setAttribute('class', 'form-check-input');
    checkBox.setAttribute('id', 'modal-checkbox');
    checkBox.setAttribute('value', '');
    checkBox.setAttribute('type', 'checkbox');
    checkBox.setAttribute('onclick', 'activateEditLimitField(this)');

    checkBoxLabel.setAttribute('class', 'form-check-label');
    checkBoxLabel.setAttribute('for', 'modal-checkbox');
    checkBoxLabel.innerText = 'Edytuj limit';

    limitValueInput.setAttribute('class', 'form-control additional');
    limitValueInput.setAttribute('id', 'category-limit');
    limitValueInput.setAttribute('type', 'number');
    limitValueInput.setAttribute('name', 'category-limit');
    limitValueInput.setAttribute('min', '0');
    limitValueInput.setAttribute('step', '0.01');
    limitValueInput.setAttribute('placeholder', 'Podaj kwotę limitu');
    limitValueInput.disabled = true;
    limitValueInput.setAttribute('value', limitValueCurrent);
 
    limitCheckBox.appendChild(limitFormDiv);
    limitFormDiv.appendChild(checkBox);
    limitFormDiv.appendChild(checkBoxLabel);

    limitValue.appendChild(limitValueInput);
}

const activateAddLimitField = function (element) {
    const limit = document.querySelector('#category-limit');

    if (element.checked) {
        limit.disabled = false;
        limit.required = true;
    } else {
        limit.disabled = true;
        limit.required = false;
    }
}

const activateEditLimitField = function (element) {
    const limit = document.querySelector('#category-limit');
    const newCategoryNameInput = document.querySelector('#new-category-name');
    
    if (element.checked) {
        limit.disabled = false;
        limit.required = true;
        newCategoryNameInput.required = false;

    } else {
        limit.disabled = true;
        limit.required = false;
        newCategoryNameInput.required = true;
    }
}

const clearInput = () => {
    newCategoryName.innerHTML='';
    currentCategoryName.innerHTML='';
    limitCheckBox.innerHTML='';
    limitValue.innerHTML='';

    addEditForm.reset();
    addEditMessage.innerText = '';

    newCategoryName.required = true;
}

const handleFormSubmit = async function (e) {
    
    const controller = addEditModal.getAttribute('data-category');
    const action = addEditModal.getAttribute('data-action');
    const id = addEditModal.getAttribute('data-id');

    e.preventDefault();

    let formData = new FormData(this);

    try {
        const res = await fetch(`../settings/${controller}/${action}${id ? '/' + id : ''}`, {
            method: 'POST',
            body: formData
        });

        if (res.ok) {
            const data = await res.json();

            if (data.message_type == 'error') {
                addEditMessage.innerText = data.message;
            }

            if (data.message_type == 'success') {
                location.reload();
            }
        } else {
            throw new Error('Request failed. Status: ' + res.status);
        }
    } catch (error) {
        console.error(error);
    }
}

const handleLimitCheckbox = async function (e) {
    const id = addEditModal.getAttribute('data-id');

    try {
        const res = await fetch(`../tooglelimit/${id}`);
        const data = await res.json();

    } catch (error) {
        console.error(error);
    }
}

// Functions for remove
const prepareRemoveModal = (category, id) => {

    removeModal.setAttribute('data-category', category);
    removeModal.setAttribute('data-id', id);

    removeBtn.innerText = `Usuń kategorię`;

    switch (category) {
        case 'income':
            removeTitle.innerText = `Usuń kategorię przychodów`;
            break;
        case 'expense':
            removeTitle.innerText = `Usuń kategorię wydatków`;
            break;
        case 'payment':
            removeTitle.innerText = `Usuń sposób płatności`;
            break;
        default:
            // 
    }
}

const removeCategory = async function (e) {

    const controller = removeModal.getAttribute('data-category');
    const id = removeModal.getAttribute('data-id');

    e.preventDefault();

    try {
        const res = await fetch(`../settings/${controller}/remove/${id}`);

        if (res.ok) {
            const data = await res.json();

            if (data.message_type == 'error') {
                removeMessage.innerText = data.message;
            }

            if (data.message_type == 'success') {
                //sessionStorage.setItem('action', `remove`);
                location.reload();
            }
        } else {
            throw new Error('Request failed. Status: ' + res.status);
        }
    } catch (error) {
        console.error(error);
    }
}

// Event listeners
addEditForm.addEventListener('submit', handleFormSubmit);
addEditModal.addEventListener('hidden.bs.modal', clearInput);
document.addEventListener('DOMContentLoaded', function () {
    let checkboxes = document.querySelectorAll('.limit-checkbox');

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', async function (e) {

            let id = this.getAttribute('data-expense-id');

            try {
                const res = await fetch(`../tooglelimit/${id}`);
                const data = await res.json();
        
            } catch (error) {
                console.error(error);
            }
            location.reload();
        });
    });
});
removeModal.addEventListener('click', function (e) {
  if (e.target.id === 'remove-btn') {
    removeCategory(e);
  }
});
