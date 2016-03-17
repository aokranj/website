<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include "Lib/LinkedIn.php";

function pre($arr) {
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}


$li = new LinkedIn(
  array(
    'api_key' => '75nvvv14uuhvpj', 
    'api_secret' => 'RDH93jYT8Hou5vk4', 
    'callback_url' => 'http://dev.codeholic.in/promz_auth/LinkedIn/'
  )
);


$url = $li->getLoginUrl(
  array(
    LinkedIn::SCOPE_BASIC_PROFILE, 
    LinkedIn::SCOPE_EMAIL_ADDRESS, 
    LinkedIn::SCOPE_NETWORK,
    LinkedIn::SCOPE_READ_WRTIE_UPDATES,
    LinkedIn::SCOPE_READ_WRITE_GROUPS,
  )
);

echo "<a href='$url'>Authorize</a> ";
if(isset($_REQUEST['code'])) {

    $token = $li->getAccessToken($_REQUEST['code']);
    $token_expires = $li->getAccessTokenExpiration();

    $post = array(
            'comment' => 'Test social Share',
            'content' => array(
                            'title' => 'Test Title',
                            'description' => 'test description', //Maxlen(255)
                            'submitted_url' => '#'
                            ),
            'visibility' => array(
                        'code' => 'anyone'
                        )

            );
    $post = $li->post('people/~/shares', $post);

    pre($post);
}

?>