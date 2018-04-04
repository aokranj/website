<?php 
		if(!function_exists("pre")) {
			function pre($arr) {
				echo "<pre>";
				print_r($arr);
				echo "</pre>";
			}
		}
require "Lib/PinterestAPI.php";
$p = new PinterestAPI();
//$p->fetch_access_token($client_id, $client_secret, $username, $password);

$resp = $p->fetch_access_token("promz_Test_App", "a6b3c1d5", "nemojenai", "pramod1234");

pre($resp);

/*$resp = $p->upload_pin(array(
    'board' => "/",
    'details' => 'test',
    'image' => "@".realpath('step_1.png')
 ));
pre($resp);
*/
	

?>