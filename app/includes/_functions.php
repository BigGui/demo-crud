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
    return $product['name_product'] . ' (' . $product['price'] . ' €)'
        . ' <a href="actions.php?action=increase&id=' . $product['ref_product'] . '&token=' . $_SESSION['token'] . '">augmenter</a> | '
        . ' <a href="index.php?action=edit&id=' . $product['ref_product'] . '">modifier</a>';
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
    if (!isset($_REQUEST['name_product']) || strlen($_REQUEST['name_product']) === 0) {
        addError('product_name');
    }

    if (strlen($_REQUEST['name_product']) > 50) {
        addError('product_name_size');
    }

    if (!isset($_REQUEST['price']) || !is_numeric($_REQUEST['price'])) {
        addError('product_price');
    }

    return empty($_SESSION['errorsList']);
}
