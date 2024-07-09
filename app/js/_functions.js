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
        });
}

/**
 * Delete product defined by th egiven id.
 * @param {*} id - Product id 'ref_product'
 */
function deleteProduct(id) {
    callAPI('DELETE', {
        action: 'delete',
        id: id,
        token: getToken()
    })
        .then(data => {
            // ...
        });
}


/**
 * Display error message with template
 * @param {string} errorMessage 
 */
function displayError(errorMessage) {
    const li = document.importNode(document.getElementById('templateError').content, true);
    li.querySelector('[data-error-message]').innerText = errorMessage;

    document.getElementById('errorsList').appendChild(li);
}