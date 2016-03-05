<?php



/*
 * Error handler
 */
function fatalError ($msg="unknown")
{
    header("HTTP/1.1 500 WTF are you doing?");
    header("Content-Type: text/plain");
    echo "Err... yeah, error!\n";
    echo "Details: $msg";
    exit;
}



// Get ID
if (!isset($_GET['id'])) {
    fatalError("Missing id!");
}
if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
    fatalError("GTF out of here!");
}
$id = (int) $_GET['id'];



// Check if post exists or not
if (!isset($redirectData[$id])) {
    fatalError("Utrinek oz reportaza oz. vatevr not gefunden zusein ja doch!");
}



/*
 * Ok, redirect then
 *
 * Keep 302 for now until we prove this works as expected
 */
//header("HTTP/1.1 301 Moved Permanently");
header("Location: ". $redirectData[$id]["newUri"]);
