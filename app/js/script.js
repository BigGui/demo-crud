import * as Product from './_functions.js';

document.querySelectorAll('[data-increase-id]')
    .forEach(function (purse) {
        purse.addEventListener('click', function (e) {
            Product.increasePrice(parseInt(this.dataset.increaseId));
        });
    });
