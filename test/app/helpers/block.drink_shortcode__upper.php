<?php
function smarty_block_drink_shortcode__upper($params,$content,$template,&$repeat){
	if($repeat){ return; }

	return strtoupper($content);
}
