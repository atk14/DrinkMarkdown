<?php
class DrinkMarkdownPrefilter {

	function filter($raw){
		$GLOBALS["wiki_replaces"] = array();

		$raw = "\n$raw\n";
		
		$replaces = array();
		$uniqid = uniqid();

		// Source codes wrapped in ```...```
		preg_match_all('/[\n\r]```([ a-z0-9]*)[\n\r](.*?)\n```[\n\r]/s',$raw,$matches);
		for($i=0;$i<sizeof($matches[0]);$i++){
			$snippet = $matches[0][$i];
			$source = $this->formatSourceCode($matches[2][$i],array("lang" => $matches[1][$i]));
			$placeholder = "source.$i.$uniqid";
			$replaces[$snippet] = "\n\n$placeholder\n\n";

			$GLOBALS["wiki_replaces"]["<p>$placeholder</p>"] = $source;
		}

		// HTML tables
		preg_match_all('/\n<table\b[^>]*>.*?<\/table>\s*?\n/si',$raw,$matches);
		for($i=0;$i<sizeof($matches[0]);$i++){
			$snippet = $matches[0][$i];
			$table = trim($snippet);
			$placeholder = "table.$i.$uniqid";
			$replaces[$snippet] = "\n\n$placeholder\n\n";
			
			$GLOBALS["wiki_replaces"]["<p>$placeholder</p>"] = $table;
		}

		$raw = EasyReplace($raw,$replaces);

		return $raw;
	}

	/**
	 * $source = $this->formatSourceCode($raw_source,array("lang" => "php"));
	 */
	function formatSourceCode($source,$options = array()){
		$options += array(
			"lang" => ""
		);

		if(strlen($options["lang"])){
			$geshi = new GeSHi($source, $options["lang"]);
			$geshi->enable_keyword_links(false);
			$geshi->set_overall_style("");
			$geshi->enable_classes(false);
			$source = $geshi->parse_code();

			$source = preg_replace('/^<pre class="[^"]+"/','<pre',$source); // '<pre class="javascript">' -> '<pre>'
		}else{
			$source = '<pre><code>'.htmlentities($source).'</code></pre>';
		}
		return $source;
	}
}
