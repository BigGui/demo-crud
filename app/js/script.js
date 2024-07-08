async function callAPIIncrease(params) {
    try {
        const response = await fetch("api.php?" + params);
        const dataResponse = await response.json();
        if (!dataResponse.isOk) {
            console.error(dataResponse.errorMessage);
            return;
        }
        document.querySelector("[data-price-id='" + dataResponse.id + "']").innerText = dataResponse.price;
    }
    catch (error) {
        console.error("Unable to load datas from server : " + error);
    }
}




document.querySelectorAll('[data-increase-id]')
    .forEach(function (purse) {
        purse.addEventListener('click', function (e) {
            callAPIIncrease('action=increase&id=' + purse.dataset.increaseId + '&token=' + document.getElementById('token').value);
        });
    });