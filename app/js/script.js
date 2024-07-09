import * as Product from './_functions.js';

document.getElementById('productForm').addEventListener('submit', function (event) {
    event.preventDefault();

    Product.createProduct({
        nameProduct: this.querySelector('[name="name_product"]').value,
        price: this.querySelector('[name="price"]').value
    });
});



// EVENTS ON PRODUCT LIST
document.getElementById('productList').addEventListener('click', function (event) {

    // INCREASE PRICE
    if (event.target.dataset.increaseId) {
        Product.increasePrice(parseInt(event.target.dataset.increaseId));
    } 

    // DELETE PRODUCT
    if (event.target.dataset.deleteId) {
        Product.deleteProduct(parseInt(event.target.dataset.deleteId));
    }
});

Product.getAllProduct();