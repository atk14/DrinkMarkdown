<?php
/**
 * Markdown modifier
 *
 *	{$source_text|markdown}
 *
 * In an ATK14 application, you may use nofilter
 *
 *	{$source_text|markdown nofilter}
 *	{!$source_text|markdown}
 */
function smarty_modifier_markdown($text){
	$markdown = new DrinkMarkdown(array(
		"table_class" => "table"
	));
	return $markdown->transform($text);
}
