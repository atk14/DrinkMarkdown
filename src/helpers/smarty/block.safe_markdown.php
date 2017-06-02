<?php
require_once(__DIR__ . "/modifier.safe_markdown.php");

/**
 * Block safe_markdown helper
 *
 *	{safe_markdown}
 *	# Hi there!
 *
 *	Welcome to our brand new website.
 *	{/safe_markdown}
 */
function smarty_safe_block_markdown($params,$content,$template,&$repeat){
	if($repeat){ return; }

	return smarty_modifier_safe_markdown($content);
}
