<?php
/**
 * 
 */
function smarty_modifier_safe_markdown($text){
	$markdown = new DrinkMarkdown(array(
		"table_class" => "table",
		"html_purification_enabled" => true, 
		"iobjects_processing_enabled" => false,
		"urlize_text" => true,
	));
	return $markdown->transform($text);
}
