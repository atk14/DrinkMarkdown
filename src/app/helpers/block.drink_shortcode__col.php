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

	$number_of_columns = (int)$template->getTemplateVars("number_of_columns"); // set by smarty_block_drink_shortcode__row()
	$number_of_columns = $number_of_columns ? $number_of_columns : 2;

	$template->assign("content",$content);
	$template->assign("number_of_columns",$number_of_columns);

	return $template->fetch("shared/helpers/drink_shortcodes/_col.tpl");
}
