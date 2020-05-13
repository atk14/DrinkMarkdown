<?php
function smarty_block_drink_shortcode__row($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	// counting columns
	$_content = trim($content);
	$_content = preg_replace('/<!-- drink:row -->.*?<!-- \/drink:row -->/s','',$_content);
	preg_match_all('/(!-- drink:col -->)/',trim($_content),$matches);
	$number_of_columns = isset($matches[1]) && sizeof($matches[1]) ? sizeof($matches[1]) : 2;

	$template->assign("content",$content);
	$template->assign("number_of_columns",$number_of_columns);

	return $template->fetch("shared/helpers/drink_shortcodes/_row.tpl");
}
