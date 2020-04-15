<?php
function smarty_block_drink_shortcode__col($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"order" => 0,
		"class" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	$template->assign("content",$content);

	return $template->fetch("shared/helpers/drink_shortcodes/_col.tpl");
}
