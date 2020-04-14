<?php
class DrinkMarkdown{

	const VERSION = "0.4.1";

	var $replaces = array();

	protected $prefilters = array();
	protected $postfilters = array();

	function __construct($options = array()){
		$options += array(
			"prefilter" => null,
			"postfilter" => null,
		);

		$prefilter = $options["prefilter"];
		$postfilter = $options["postfilter"];

		unset($options["prefilter"]);
		unset($options["postfilter"]);

		$this->prefilters[] = $prefilter ? $prefilter : new DrinkMarkdownPrefilter($options);
		$this->postfilters[] = $postfilter ? $postfilter : new DrinkMarkdownPostfilter($options);
	}

	function prependPrefilter($prefilter){
		array_unshift($this->prefilters,$prefilter);
	}

	function appendPrefilter($prefilter){
		$this->prefilters[] = $prefilter;
	}

	function prependPostfilter($postfilter){
		array_unshift($this->postfilters,$postfilter);
	}

	function appendPostfilter($postfilter){
		$this->postfilters[] = $postfilter;
	}

	/**
	 * Performs the transformation of a Markdown document to a HTML document
	 *
	 *	$dm = new DrinkMarkdown();
	 *	$html = $dm->transform($markdown_text);
	 */
	function transform($markdown){
		foreach($this->prefilters as $prefilter){
			$markdown = $prefilter->filter($markdown,$this);
		}

		$html = Michelf\MarkdownExtra::defaultTransform($markdown);

		foreach($this->postfilters as $postfilter){
			$html = $postfilter->filter($html,$this);
		}

		return $html;
	}
}