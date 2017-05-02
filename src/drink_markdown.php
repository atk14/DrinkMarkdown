<?php
class DrinkMarkdown{

	function __construct($options = array()){
		$options += array(
			"prefilter" => new DrinkMarkdownPrefilter(),
			"postfilter" => new DrinkMarkdownPostfilter(),
		);

		$this->prefilter = $options["prefilter"];
		$this->postfilter = $options["postfilter"];
	}

	/**
	 * $dm = new DrinkMarkdown();
	 * $html = $dm->transform($markdown_text);
	 */
	function transform($markdown){
		if($this->prefilter){ $markdown = $this->prefilter->filter($markdown,$this); }

		$html = Michelf\MarkdownExtra::defaultTransform($markdown);

		if($this->postfilter){ $html = $this->postfilter->filter($html,$this); }

		return $html;
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
