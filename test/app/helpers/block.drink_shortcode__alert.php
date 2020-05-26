<?php
function smarty_block_drink_shortcode__alert($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += array(
		"type" => "primary"
	);

	return "<div class=\"alert alert-$params[type]\" role=\"alert\">$content</div>";

}
