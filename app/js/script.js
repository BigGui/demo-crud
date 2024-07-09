import * as Product from './_functions.js';

document.querySelectorAll('[data-increase-id]')
    .forEach(function (purse) {
        purse.addEventListener('click', function (e) {
            Product.increasePrice(parseInt(this.dataset.increaseId));
        });
    });

document.querySelectorAll('[data-delete-id]')
    .forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            Product.deleteProduct(parseInt(this.dataset.deleteId));
        });
    });


document.getElementById('productForm').addEventListener('submit', function (event) {
    event.preventDefault();

    Product.createProduct({
        nameProduct: this.querySelector('[name="name_product"]').value,
        price: this.querySelector('[name="price"]').value
    });

    return false;
});