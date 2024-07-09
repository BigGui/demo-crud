<?php

/**
 * Get HTML to display a product in the list
 *
 * @param array $product - Data for the product to display
 * @return string HTML to display the product in the list
 */
function getHTMLProduct(array $product): string
{

    return $product['priority'] . '. ' . $product['name_product'] . ' (<span data-price-id="' . $product['ref_product'] . '">' . $product['price'] . '</span> €)'
        . ' <button type="button" data-increase-id="' . $product['ref_product'] . '">💰</button> '
        . ' <a href="actions.php?action=up&id=' . $product['ref_product'] . '&token=' . $_SESSION['token'] . '">⬆️</a> '
        . ' <a href="actions.php?action=down&id=' . $product['ref_product'] . '&token=' . $_SESSION['token'] . '">⬇️</a> '
        . ' <button type="button" data-delete-id="' . $product['ref_product'] . '">🗑️</button> '
        . ' <a href="index.php?action=edit&id=' . $product['ref_product'] . '">🖋️</a>';
}


/**
 * Get HTML code to display a form in order to create or modify a product.
 *
 * @param string $action Action to execute : 'create' or 'modify'
 * @param array $data Associative array with prefilled value for each field.
 * @return string HTMLM to code to display the form
 */
function getHtmlProductForm(string $action = 'create', array $data = []): string
{
    $html = '<form id="productForm" action="actions.php" method="post">'
        . '<ul>'
        . '<li>'
        . '<label for="name_product">Nom du produit</label> '
        . '<input type="text" name="name_product" id="name_product" . value="' . (isset($data['name_product']) ? $data['name_product'] : '') . '" placeholder="Oil - . Canola" maxlength="50" required>'
        . '</li>'
        . '<li>'
        . '<label for="price">Prix du produit</label> '
        . '<input type="text" name="price" id="price" value="' . (isset($data['price']) ? $data['price'] : '') . '" placeholder="9.90" maxlength="16" required>'
        . '</li>'
        . '</ul>';

    if ($action === 'modify') {
        $html .= '<input type="hidden" name="ref_product" value="' . (isset($data['ref_product']) ? $data['ref_product'] : '') . '">';
    }

    $buttonText = [
        'create' => 'Ajouter un produit',
        'modify' => 'Modifier le produit'
    ];

    $html .= '<input type="hidden" id="token" name="token" value="' . $_SESSION['token'] . '">'
        . '<input type="hidden" name="action" value="' . $action . '">'
        . '<input type="submit" value="' . $buttonText[$action] . '">'
        . '</form>';

    return $html;
}
