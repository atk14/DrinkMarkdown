<?php
class DrinkMarkdown{

	var $replaces = array();

	function __construct($options = array()){
		$options += array(
			"prefilter" => null,
			"postfilter" => null,
		);

		$prefilter = $options["prefilter"];
		$postfilter = $options["postfilter"];

		unset($options["prefilter"]);
		unset($options["postfilter"]);

		$this->prefilter = $prefilter ? $prefilter : new DrinkMarkdownPrefilter($options);
		$this->postfilter = $postfilter ? $postfilter : new DrinkMarkdownPostfilter($options);
	}

	/**
	 * Performs the transformation of a Markdown document to a HTML document
	 *
	 *	$dm = new DrinkMarkdown();
	 *	$html = $dm->transform($markdown_text);
	 */
	function transform($markdown){
		if($this->prefilter){ $markdown = $this->prefilter->filter($markdown,$this); }

		$html = Michelf\MarkdownExtra::defaultTransform($markdown);

		if($this->postfilter){ $html = $this->postfilter->filter($html,$this); }

		return $html;
	}
}
