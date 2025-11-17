<?php
/**
 * Base class for other filters
 */
class DrinkMarkdownFilter {

	protected $options;

	function __construct($options = array()){
		$this->options = $options;
	}

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
		static $css;

		if(!$css){
			$css = file_get_contents(__DIR__ . "/../public/styles/highlight.php/default.css");
		}

		$options += array(
			"lang" => ""
		);

		if(strlen($options["lang"])){
			$hl = new \Highlight\Highlighter();
			
			try {
				// Source code highlighting
				$highlighted = $hl->highlight($options["lang"], $source);
				$source = $highlighted->value;

				// CSS inliner
				$css_inliner = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
				$source = $css_inliner->convert(
					$source,
					$css
				);
				$source = preg_replace('/^.*?<body>(.*)<\/body><\/html>$/s','\1',$source);
				$source = preg_replace('/ class="[^"]*"/','',$source);
			}
			catch (DomainException $e) {
				// This is thrown if the specified language does not exist
			}
			$source = "<pre>$source</pre>";
		}else{
			$source = '<pre><code>'.htmlentities($source).'</code></pre>';
		}
		return $source;
	}
}
