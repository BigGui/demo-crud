async function callAPIIncrease(params) {
    try {
        const response = await fetch("api.php?" + params);
        const json = await response.json();
        document.querySelector("[data-price-id='" + json.id + "']").innerText = json.price;
    }
    catch(error) {
        console.error("Unable to load todolist datas from the server : " + error);
    }
}



let purses = document.querySelectorAll('.js-increase-btn');

purses.forEach(function(purse) {
    purse.addEventListener('click', function(e) {
        callAPIIncrease('action=increase&id=' + purse.dataset.increaseId + '&token=' + document.getElementById('token').value);
    });
});