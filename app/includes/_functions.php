<?php

/**
 * Generate a unique token and add it to the user session. 
 *
 * @return void
 */
function generateToken()
{
    if (
        !isset($_SESSION['token'])
        || !isset($_SESSION['tokenExpire'])
        || $_SESSION['tokenExpire'] < time()
    ) {
        $_SESSION['token'] = md5(uniqid(mt_rand(), true));
        $_SESSION['tokenExpire'] = time() + 60 * 15;
    }
}

/**
 * Redirect to the given URL.
 *
 * @param string $url
 * @return void
 */
function redirectTo(string $url): void
{
    // var_dump('REDIRECT ' . $url);
    header('Location: ' . $url);
    exit;
}


/**
 * Get from an array a HTML list string
 * @param array $array your array you want in HTML list
 * @param string $ulClass an optional CSS class to add to UL element
 * @param string $liClass an optional CSS class to add to LI elements
 * @return string the HTML list
 */
function getArrayAsHTMLList(array $array, string $ulClass = '', string $liClass = ''): string
{
    // $values = '';
    // foreach($array as $value){
    //     $values .= "<li>{$value}</li>";
    // }

    $ulClass = $ulClass ? ' class="' . $ulClass . '"' : '';
    $liClass = $liClass ? ' class="' . $liClass . '"' : '';

    return '<ul' . $ulClass . '>'
        . implode(array_map(fn ($v) => '<li' . $liClass . '>' . $v . '</li>', $array))
        . '</ul>';
}


/**
 * Get HTML to display errors available in user SESSION
 *
 * @param array $errorsList - Available errors list
 * @return string HTMl to display errors
 */
function getHtmlErrors(array $errorsList): string
{
    if (!empty($_SESSION['errorsList'])) {
        $errors = $_SESSION['errorsList'];
        unset($_SESSION['errorsList']);

        return getArrayAsHTMLList(
            array_map(fn ($e) => $errorsList[$e], $errors),
            'notif-error'
        );
    }
    return '';
}

/**
 * Get HTML to display messages available in user SESSION
 *
 * @param array $messagesList - Available Messages list
 * @return string HTML to display messages
 */
function getHtmlMessages(array $messagesList): string
{
    if (isset($_SESSION['msg'])) {
        $m = $_SESSION['msg'];
        unset($_SESSION['msg']);
        return '<p class="notif-success">' . $messagesList[$m] . '</p>';
    }
    return '';
}

/**
 * Verify HTTP referer and token. Redirect with error message.
 *
 * @return void
 */
function preventCSRF(string $redirectUrl = 'index.php'): void
{
    global $globalUrl;

    if (!isset($_SERVER['HTTP_REFERER']) || !str_contains($_SERVER['HTTP_REFERER'], $globalUrl)) {
        addError('referer');
        redirectTo($redirectUrl);
    }

    if (!isset($_SESSION['token']) || !isset($_REQUEST['token']) || $_SESSION['token'] !== $_REQUEST['token']) {
        addError('csrf');
        redirectTo($redirectUrl);
    }
}

/**
 * Add a new error message to display on next page. 
 *
 * @param string $errorMsg - Error message to display
 * @return void
 */
function addError(string $errorMsg): void
{
    if (!isset($_SESSION['errorsList'])) {
        $_SESSION['errorsList'] = [];
    }
    $_SESSION['errorsList'][] = $errorMsg;
}


/**
 * Add a new message to display on next page. 
 *
 * @param string $message - Message to display
 * @return void
 */
function addMessage(string $message): void
{
    $_SESSION['msg'] = $message;
}


/**
 * Get HTML to display a product in the list
 *
 * @param array $product - Data for the product to display
 * @return string HTML to display the product in the list
 */
function getHTMLProduct(array $product): string
{

    return $product['priority'] . '. ' . $product['name_product'] . ' (<span data-price-id="' . $product['ref_product'] . '">' . $product['price'] . '</span> €)'
        . ' <button type="button" class="js-increase-btn" data-increase-id="' . $product['ref_product'] . '">💰</button> '
        . ' <a href="actions.php?action=up&id=' . $product['ref_product'] . '&token=' . $_SESSION['token'] . '">⬆️</a> '
        . ' <a href="actions.php?action=down&id=' . $product['ref_product'] . '&token=' . $_SESSION['token'] . '">⬇️</a> '
        . ' <a href="index.php?action=edit&id=' . $product['ref_product'] . '">🖋️</a>';
}

/**
 * Remove data feedback to display in the form.
 *
 * @return void
 */
function eraseFormData(): void
{
    unset($_SESSION['formData']);
}


/**
 * Check for product fata format
 *
 * @param array $productData An array containing product data
 * @return boolean Is there errors in product data ?
 */
function checkProductInfo(array $productData): bool
{
    if (!isset($productData['name_product']) || strlen($productData['name_product']) === 0) {
        addError('product_name');
    }

    if (strlen($productData['name_product']) > 50) {
        addError('product_name_size');
    }

    if (!isset($productData['price']) || !is_numeric($productData['price'])) {
        addError('product_price');
    }

    return empty($_SESSION['errorsList']);
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
    $html = '<form action="actions.php" method="post">'
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

/**
 * Change product priority up or down.
 *
 * @param PDO $db Connection to the database
 * @param integer $changingValue -1 to up priority / 1 to up priority
 * @param integer $id ref_product to move
 * @return void
 */
function changeProductPriority(PDO $db, int $changingValue, int $id): void
{
    try {
        $db->beginTransaction();
    
        $query = $db->prepare("SELECT ref_product FROM product WHERE priority = (
            SELECT priority + :changingValue FROM product WHERE ref_product = :id
        );");
        $query->execute([
            'id' => $id,
            'changingValue' => $changingValue
        ]);
    
        $idToMove = intval($query->fetchColumn());
        if ($idToMove !== false) {
            $queryUpdate = $db->prepare("UPDATE product SET priority = priority + :changingValue WHERE ref_product = :id;");
            $queryUpdate->execute([
                'id' => $idToMove,
                'changingValue' => $changingValue * -1
            ]);
        } 
    
        $queryUpdate = $db->prepare("UPDATE product SET priority = priority + :changingValue WHERE ref_product = :id;");
        $isUpdateOk = $queryUpdate->execute([
            'id' => $id,
            'changingValue' => $changingValue
        ]);
    
        $db->commit();
    
        if ($isUpdateOk) {
            addMessage('update_ok');
        } else {
            addError('update_ko');
        }
        
    } catch (Exception $e) {
        $db->rollBack();
        addError('update_ko');
    }
}
