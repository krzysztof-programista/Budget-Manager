//Date autofill
document.querySelector("#date").valueAsDate = new Date();

//Const declarations
const amountField = document.querySelector('#amount');
const dateField = document.querySelector('#date');
const categoryField = document.querySelector('#category');

const limitInfoBox = document.querySelector('#limit-info');
const limitValueBox = document.querySelector('#limit-value');
const leftCashBox = document.querySelector('#left-cash');

//Methods
const getLimitForCategory = async (categoryId) => {
    try {
        const res = await fetch(`../api/limit/${categoryId}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
};

const getMonthlyExpenses = async (categoryId, date) => {
    try {
        const res = await fetch(`../api/limitSum/${categoryId}/${date}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log('ERROR', e);
    }
};

//Event listeners
window.addEventListener('load', (event) => {
    const amount = amountField.value;
    const date = dateField.value;
    const categoryId = categoryField.options[categoryField.selectedIndex].value;

    limitInfo(categoryId);
    limitValue(categoryId);
    leftCash(categoryId, amount, date);
});

amountField.addEventListener('input', async => {
    const amount = amountField.value;
    const date = dateField.value;
    const categoryId = categoryField.options[categoryField.selectedIndex].value;

    limitInfo(categoryId);
    limitValue(categoryId);
    leftCash(categoryId, amount, date);
});

dateField.addEventListener('change', async => {
    const amount = amountField.value;
    const date = dateField.value;
    const categoryId = categoryField.options[categoryField.selectedIndex].value;

    limitInfo(categoryId);
    limitValue(categoryId);
    leftCash(categoryId, amount, date);
});

categoryField.addEventListener('change', async => {
    const amount = amountField.value;
    const date = dateField.value;
    const categoryId = categoryField.options[categoryField.selectedIndex].value;

    limitInfo(categoryId);
    limitValue(categoryId);
    leftCash(categoryId, amount, date);
});

//Info box rendering
const limitInfo = async (categoryId) => {

    const limitData = await getLimitForCategory(categoryId);
    const isLimitActive = parseInt(limitData.is_limit_active);

    if(isLimitActive === 1){
        limitInfoBox.innerText = `Limit aktywny`;
    }else {
        limitInfoBox.innerText = `Brak limitu`;
    }
};

const limitValue = async (categoryId) => {
    const limitData = await getLimitForCategory(categoryId);
    const isLimitActive = parseInt(limitData.is_limit_active);
    const limit = parseInt(limitData.cash_limit);

    if(isLimitActive === 1){
        limitValueBox.innerText = `${limit.toFixed(2)} zł`;
    }else {
        limitValueBox.innerText = `---`;
    }
};

const leftCash = async (categoryId, amount, date) => {

    const limitData = await getLimitForCategory(categoryId);

    const isLimitActive = parseInt(limitData.is_limit_active);
    const limit = parseInt(limitData.cash_limit);
    const monthlyExpenses = await getMonthlyExpenses(categoryId, date);
    const leftCash = limit - amount - monthlyExpenses;

    leftCashBox.classList.remove('above-limit');
    if(isLimitActive === 1)
    {
        if (leftCash < '0'){
            leftCashBox.classList.add('above-limit');
        }
        leftCashBox.innerText =`${leftCash.toFixed(2)} zł`;
    }else {
        leftCashBox.innerText = `---`;
    }
};





