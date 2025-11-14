<?php
class TcIssue extends TcBase {

	function test(){

		// Building very long $source

		$source = [];

		$source[] = "[row]";

		$source[] = "[col]";
		$source[] = str_repeat("Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n",1000);
		$source[] = "[/col]";

		$source[] = "[col]";
		$source[] = str_repeat("Lorem ipsum dolor sit amet, consectetur adipiscing elit.\n",1000);
		$source[] = "[/col]";

		$source[] = "[/row]";

		$source = join("\n",$source);

		$markdown = new DrinkMarkdown(array(
			"table_class" => "table",
			"html_purification_enabled" => false,
			"iobjects_processing_enabled" => true,
			"urlize_text" => true,
			"shortcodes_enabled" => true,
		));

		$output = $markdown->transform($source);

		$this->assertStringContains('<div class="row row--shortcode">',substr($output,0,1000));
	}
}
