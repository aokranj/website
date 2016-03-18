<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);


function pre($arr) {
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

require_once ('codebird/codebird.php');

\Codebird\Codebird::setConsumerKey('XHPcqEznPixX3t3htX6oEWiJV', 'BWZG3e5egIFJPyJkOvLCuKrVlnHWZxPAiIVkcmNyWMyNaWBj8Q'); // static, see 'Using multiple Codebird instances'

$cb = \Codebird\Codebird::getInstance();


session_start();

//reset_session(); exit;

if (! isset($_SESSION['oauth_token'])) {
    // get the request token
    $reply = $cb->oauth_requestToken(array(
        'oauth_callback' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
    ));

    // store the token
    $cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
    $_SESSION['oauth_token'] = $reply->oauth_token;
    $_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;
    $_SESSION['oauth_verify'] = true;

    // redirect to auth website
    $auth_url = $cb->oauth_authorize();
    /*pre($_SESSION);

    echo $auth_url."<br>";
    echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    exit;*/
    header('Location: ' . $auth_url);
    die();

} elseif (isset($_GET['oauth_verifier']) && isset($_SESSION['oauth_verify'])) {
    // verify the token
    $cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    unset($_SESSION['oauth_verify']);

    // get the access token
    $reply = $cb->oauth_accessToken(array(
        'oauth_verifier' => $_GET['oauth_verifier']
    ));

    // store the token (which is different from the request token!)
    $_SESSION['oauth_token'] = $reply->oauth_token;
    $_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;

    // send to same URL, without oauth GET parameters
    header('Location: ' . basename(__FILE__));
    die();
}


pre($_SESSION);

// assign access token on each page load
$cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

$params = array(
  'status' => 'My first Twitter app. YAY! #PHP #codebird  '
);
$reply = $cb->statuses_update($params);
pre($reply);

function reset_session() {
    foreach($_SESSION as $key => $session) {
        unset($_SESSION[$key]);
    }
}

?>