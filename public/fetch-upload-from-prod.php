<?php
/*
 * Retrieve missing wp-content/uploads/... files from production on-the-fly (and store it locally, and serve it to the client too)
 *
 * Instead of implementing a fancy tool that determines what files it should fetch
 * and fetch those files in advance, let's just capture the requests that would
 * have generated a 404 error in the wp-content/uploads/ directory, and handle
 * those requests by fetching the file from prod, storing it locally and serving
 * the said file back to the client too.
 *
 * With the exception of a slight delay on the first run, such handling should
 * make fetching uploads transparent to our user (provided they have a working
 * internet connection).
 */


/*
 * Init Wordpress
 */
$wp_did_header = true;
require_once __DIR__ . '/wp-load.php';


/*
 * Parse & verify the argument
 */
$filePath = $_GET["path"];   // Without leading slash

if (!preg_match('#^(wp-content/uploads/.+)/([^/]+)$#', $filePath, $m)) {
    throw new Exception("Unsupported path: $filePath");
}
$fileDir  = $m[1];
$fileName = $m[2];
$prodUrl  = "https://www.aokranj.com/" . $filePath;

/*
 * Check & create the directory
 */
if (!file_exists($fileDir)) {
    mkdir($fileDir, 0777, true);
} else {
    if (!is_dir($fileDir)) {
        throw new Exception("Not a directory: $fileDir");
    }
}


/*
 * Fetch the file from prod
 */
$response = wp_remote_get($prodUrl);
if (wp_remote_retrieve_response_code($response) != 200) {
    throw new Exception("Unable to retrieve remote resource: $prodUrl");
}


/*
 * Store the file locally
 */
$body = wp_remote_retrieve_body($response);
file_put_contents($filePath, $body);


/*
 * Pass the file content (and the correct content-type header) to the client
 */
header("content-type: ". $response['headers']['content-type']);
echo $body;
