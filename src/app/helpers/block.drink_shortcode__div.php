<?php
function smarty_block_drink_shortcode__div($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$attrs = [];
	foreach($params as $k => $v){
		$attrs[] = sprintf('%s="%s"',h($k),h($v));
	}
	$attrs = $attrs ? " ".join(" ",$attrs) : "";

	return "<div$attrs>\n$content\n</div>";
}
