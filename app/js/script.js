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

function increasePrice(id) {
    callAPI('PUT', {
        action: 'increase',
        id: id,
        token: document.getElementById('token').value
    })
        .then(data => {
            if (!data.isOk) {
                console.error(data.errorMessage);
                return;
            }
            document.querySelector("[data-price-id='" + data.id + "']").innerText = data.price;
        });
}

function deleteProduct(id) {
    callAPI('DELETE', {
        action: 'delete',
        id: id,
        token: document.getElementById('token').value
    })
        .then(data => {
            // ...
        });
}


document.querySelectorAll('[data-increase-id]')
    .forEach(function (purse) {
        purse.addEventListener('click', function (e) {
            increasePrice(this.dataset.increaseId);
        });
    });