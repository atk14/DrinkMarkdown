<?php
class DrinkMarkdown{

	const VERSION = "0.6";

	var $replaces = array();

	protected $prefilters = array();
	protected $postfilters = array();

	protected $block_shortcodes = array("row","col");
	protected $inline_block_shortcodes = array();
	protected $function_shortcodes = array();
	protected $shortcode_callbacks = array();

	function __construct($options = array()){
		$options += array(
			"prefilter" => null,
			"postfilter" => null,
			"shortcodes_enabled" => true,
		);

		$prefilter = $options["prefilter"];
		$postfilter = $options["postfilter"];

		unset($options["prefilter"]);
		unset($options["postfilter"]);

		$this->prefilters[] = $prefilter ? $prefilter : new DrinkMarkdownPrefilter($options);
		$this->postfilters[] = $postfilter ? $postfilter : new DrinkMarkdownPostfilter($options);

		if($options["shortcodes_enabled"]){
			$this->appendPrefilter(new MarkdownShortcodesPrefilter());
			$this->prependPostfilter(new MarkdownShortcodesPostfilter());
		}
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

	function registerBlockShortcode($shortcode,$callback = null){
		$this->block_shortcodes[] = $shortcode;
		$this->shortcode_callbacks[$shortcode]  = $callback;
	}

	function getBlockShortcodes(){
		return $this->block_shortcodes;
	}

	function registerInlineBlockShortcode($shortcode,$callback = null){
		$this->inline_block_shortcodes[] = $shortcode;
		$this->shortcode_callbacks[$shortcode]  = $callback;
	}

	function getInlineBlockShortcodes(){
		return $this->inline_block_shortcodes;
	}

	/**
	 *
	 *	$dm->registerFunctionShortcode("lower",function($content,$params){ return strtolower($content); });
	 */
	function registerFunctionShortcode($shortcode,$callback = null){
		$this->function_shortcodes[] = $shortcode;
		$this->shortcode_callbacks[$shortcode]  = $callback;
	}

	function getFunctionShortcodes(){
		return $this->function_shortcodes;
	}

	function getShortcodeCallback($shortcode){
		return isset($this->shortcode_callbacks[$shortcode]) ? $this->shortcode_callbacks[$shortcode] : null;
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
