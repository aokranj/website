<?php
/*
Plugin Name: Parent Category Toggler
Plugin URI: http://wordpress.org/extend/plugins/parent-category-toggler/
Description: Automatically toggle the parent categories when a sub category is selected.
Version: 1.3.4
Author: Ben Lobaugh
Author URI: http://ben.lobaugh.net
Original Author: Aw Guo
Original Author URI: http://www.ifgogo.com
Licence: GPL
*/

function super_category_toggler() {
	
	$taxonomies = apply_filters('super_category_toggler',array());
	for($x=0;$x<count($taxonomies);$x++)
	{
		$taxonomies[$x] = '#'.$taxonomies[$x].'div .selectit input';
	}
	$selector = implode(',',$taxonomies);
	if($selector == '') $selector = '.selectit input';
	
	echo '
		<script>
		jQuery("'.$selector.'").change(function(){
			var $chk = jQuery(this);
			var ischecked = $chk.is(":checked");
			$chk.parent().parent().siblings().children("label").children("input").each(function(){
var b = this.checked;
ischecked = ischecked || b;
})
			checkParentNodes(ischecked, $chk);
		});
		function checkParentNodes(b, $obj)
		{
			$prt = findParentObj($obj);
			if ($prt.length != 0)
			{
			 $prt[0].checked = b;
			 checkParentNodes(b, $prt);
			}
		}
		function findParentObj($obj)
		{
			return $obj.parent().parent().parent().prev().children("input");
		}
		</script>
		';
	
}
add_action('admin_footer-post.php', 'super_category_toggler');
add_action('admin_footer-post-new.php', 'super_category_toggler');
?>
