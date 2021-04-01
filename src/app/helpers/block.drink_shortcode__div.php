<?php
function smarty_block_drink_shortcode__div($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$h = function($string){
		$flags =  ENT_COMPAT | ENT_QUOTES;
		if(defined("ENT_HTML401")){ $flags = $flags | ENT_HTML401; }
		return htmlspecialchars($string,$flags,"ISO-8859-1");
	};

	$attrs = [];
	foreach($params as $k => $v){
		$attrs[] = sprintf('%s="%s"',$h($k),$h($v));
	}
	$attrs = $attrs ? " ".join(" ",$attrs) : "";

	return "<div$attrs>\n$content\n</div>";
}
