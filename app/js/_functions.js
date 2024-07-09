/**
 * Generate asynchronous call to api.php with parameters
 * @param {*} method GET, POST, PUT or DELETE
 * @param {*} params An object with data to send.
 * @returns 
 */
async function callAPI(method, params) {
    try {
        const response = await fetch("api.php", {
            method: method,
            body: JSON.stringify(params),
            headers: {
                'Content-type': 'application/json'
            }
        });
        const dataResponse = await response.json();
        return dataResponse;
    }
    catch (error) {
        console.error("Unable to load datas from server : " + error);
    }
}

/**
 * Get current global token value.
 * @returns 
 */
function getToken() {
    return document.getElementById('token').value;
}

/**
 * Increase product price for the given id.
 * @param {int} id - Product id 'ref_product'
 */
export function increasePrice(id) {
    if (!Number.isInteger(id)) {
        displayError("Impossible de déterminer l'identifiant du produit.");
        return;
    }

    const token = getToken();
    if (!token.length) {
        displayError("Jeton invalide.");
        return;
    }

    callAPI('PUT', {
        action: 'increase',
        id: id,
        token: token
    })
        .then(data => {
            if (!data.isOk) {
                displayError(data.errorMessage);
                return;
            }

            data.id = parseInt(data.id);
            data.price = parseFloat(data.price);
            if (!Number.isInteger(data.id) || data.price <= 0) {
                displayError("Données reçues incohérentes");
                return;
            }
            document.querySelector("[data-price-id='" + data.id + "']").innerText = data.price;
            displayMessage('Prix augmenté avec succès.');
        });
}

/**
 * Delete product defined by th egiven id.
 * @param {*} id - Product id 'ref_product'
 */
export function deleteProduct(id) {
    if (!Number.isInteger(id)) {
        displayError("Impossible de déterminer l'identifiant du produit.");
        return;
    }

    const token = getToken();
    if (!token.length) {
        displayError("Jeton invalide.");
        return;
    }

    callAPI('DELETE', {
        action: 'delete',
        id: id,
        token: token
    })
        .then(data => {
            if (!data.isOk) {
                displayError(data.errorMessage);
                return;
            }

            data.id = parseInt(data.id);
            if (!Number.isInteger(data.id)) {
                displayError("Données reçues incohérentes");
                return;
            }

            document.querySelector("[data-delete-id='" + data.id + "']").closest('.js-product').remove();
            displayMessage('Produit supprimé avec succès.');
        });
}

/**
 * Create a new product
 * 
 * @param {*} data New product data
 * @returns 
 */
export function createProduct(data) {

    if (!data.nameProduct.length) {
        displayError("Nom de produit invalide.");
        return;
    }

    data.price = parseFloat(data.price);
    if (!data.price) {
        displayError("Prix invalide.");
        return;
    }

    data.token = getToken();
    if (!data.token.length) {
        displayError("Jeton invalide.");
        return;
    }

    data.action = 'create';

    callAPI('POST', data)
        .then(output => {
            if (!output.isOk) {
                displayError(data.errorMessage);
                return;
            }

            displayProduct(output);
            displayMessage('Produit créé.');
        });
}


/**
 * Display error message with template
 * @param {string} errorMessage 
 */
function displayError(errorMessage) {
    const li = document.importNode(document.getElementById('templateError').content, true);
    console.log(li.querySelector('[data-error-message]'));
    const m = li.querySelector('[data-error-message]');
    m.innerText = errorMessage;
    document.getElementById('errorsList').appendChild(li);
    setTimeout(() => m.remove(), 2000);
}


/**
 * Display message with template
 * @param {string} message 
 */
function displayMessage(message) {
    const li = document.importNode(document.getElementById('templateMessage').content, true);
    const m = li.querySelector('[data-message]')
    m.innerText = message;
    document.getElementById('messagesList').appendChild(li);
    setTimeout(() => m.remove(), 2000);
}

/**
 * Generate and display a produc in the product list from given data.
 * @param {object} data 
 */
function displayProduct(data) {
    const li = document.importNode(document.getElementById('templateProduct').content, true);
    li.querySelector('[data-product-name]').innerText = data.nameProduct;
    li.querySelector('[data-price-id]').innerText = data.price;
    li.querySelector('[data-increase-id]').dataset.increaseId = data.id;
    li.querySelector('[data-delete-id]').dataset.deleteId = data.id;
    li.querySelector('[data-up-link]').setAttribute('href', 'actions.php?action=up&id=' + data.id + '&token=' + getToken());
    li.querySelector('[data-down-link]').setAttribute('href', 'actions.php?action=down&id=' + data.id + '&token=' + getToken());
    li.querySelector('[data-edit-link]').setAttribute('href', 'index.php?action=edit&id=' + data.id);

    document.getElementById('productList').appendChild(li);
}