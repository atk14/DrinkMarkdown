<?php
class MarkdownShortcodesPrefilter extends DrinkMarkdownFilter {

	function filter($raw,$transformer){
		$shortcodes = array("row","col");
		$shortcodes_str = "(?<shortcode>".join("|",$shortcodes).")";

		// Opening tags:
		// [col] ---> <!-- drink:col -->
		// [col align="right" class="highlight"] ---> <!-- drink:col align="right" class="highlight" -->
		$raw = preg_replace('/[\r\n\s]*\[('.$shortcodes_str.'(?<params>| [^]]*))\][\r\n\s]*/s',"<!-- shortcode_break --><!-- drink:\\1 --><!-- shortcode_break -->",$raw);

		// Closing tags:
		// [/col] ---> <!-- /drink:col -->
		// [/row] ---> <!-- /drink:row -->
		$raw = preg_replace('/[\r\n\s]*\[\/'.$shortcodes_str.'\][\r\n\s]*/s',"<!-- shortcode_break --><!-- /drink:\\1 --><!-- shortcode_break -->",$raw);

		$raw = preg_replace('/(<!-- shortcode_break -->)+([\s\n\r]*)$/s','\2',$raw);
		$raw = preg_replace('/^([\s\n\r]*)(<!-- shortcode_break -->)+/s','\1',$raw);
		$raw = preg_replace('/(<!-- shortcode_break -->)+/',"\n\n",$raw);

		//echo "<pre>";
		//echo h($raw);
		//echo "</pre>";

		return $raw;
	}
}
