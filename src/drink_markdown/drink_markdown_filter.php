<?php
/**
 * Base class for other filters
 */
class DrinkMarkdownFilter {

	/**
	 * Filter method
	 */
	function filter($source,$transformer){
		return $source;
	}

	/**
	 *
	 *	$this->hideSomething('/(<a\b[^>]*>.*?<\/a>)/si')
	 */
	function hideSomething($pattern,&$content,&$replaces_back = null){
		static $counter = 0, $uniqid = null;

		if(!isset($uniqid)){ $uniqid = uniqid(); }

		if(is_null($replaces_back)){
			$replaces_back = array();
		}

		$replace_ar = array();

		preg_match_all($pattern,$content,$matches);
		foreach($matches[0] as $link){
			$counter++;

			$replacement = ",replace_{$counter}_$uniqid,";
			$replace_ar[$link] = $replacement;
			$replaces_back[$replacement] = $link;
		}

		$content = EasyReplace($content,$replace_ar);

		return $replaces_back;
	}

	/**
	 *
	 *	$source = $this->formatSourceCode($raw_source,array("lang" => "php"));
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
			$source = @$geshi->parse_code(); // There is an error in GeSHi: Undefined offset: 0 in /path/to/an/app/vendor/easybook/geshi/geshi.php:3500

			$source = preg_replace('/^<pre class="[^"]+"/','<pre',$source); // '<pre class="javascript">' -> '<pre>'
		}else{
			$source = '<pre><code>'.htmlentities($source).'</code></pre>';
		}
		return $source;
	}
}
