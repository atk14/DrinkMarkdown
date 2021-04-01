<?php
class DrinkMarkdown{

	const VERSION = "0.6.4";

	var $replaces = array();

	protected $prefilters = array();
	protected $postfilters = array();

	protected $block_shortcodes = array("row","col","div");
	protected $inline_block_shortcodes = array();
	protected $function_shortcodes = array();
	protected $shortcode_callbacks = array();
	protected $block_shortcodes_with_markdown_transformation_disabled = array();

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

	/**
	 *
	 *	$this->registerBlockShortcode("highlight_html");
	 *	$this->registerBlockShortcode("highlight_html",[
	 *		"callback" => function($content,$params){ ... },
	 *		"markdown_transformation_enabled" => false,
	 *	]);
	 *	$this->registerBlockShortcode("highlight_html",function($content,$params){ ... });
	 *	$this->registerBlockShortcode("highlight_html",false);
	 */
	function registerBlockShortcode($shortcode,$options = array()){
		$this->_registerBlockShortcode($shortcode,$options,false);
	}

	function getBlockShortcodes(){
		return $this->block_shortcodes;
	}

	function registerInlineBlockShortcode($shortcode,$options = array()){
		$this->_registerBlockShortcode($shortcode,$options,true);
	}

	function getInlineBlockShortcodes(){
		return $this->inline_block_shortcodes;
	}

	protected function _registerBlockShortcode($shortcode,$options,$inline){
		if(!is_array($options)){
			if(is_callable($options)){
				$options = array(
					"callback" => $options,
				);
			}elseif(is_bool($options)){
				$options = array(
					"markdown_transformation_enabled" => $options,
				);
			}else{
				// what ???
				$options = array();
			}
		}
		$options += array(
			"callback" => null,
			"markdown_transformation_enabled" => true,
		);

		if($inline){
			$this->inline_block_shortcodes[] = $shortcode;
		}else{
			$this->block_shortcodes[] = $shortcode;
		}

		$this->shortcode_callbacks[$shortcode]  = $options["callback"];

		if(!$options["markdown_transformation_enabled"]){
			$this->block_shortcodes_with_markdown_transformation_disabled[$shortcode] = $shortcode;
		}else{
			unset($this->block_shortcodes_with_markdown_transformation_disabled[$shortcode]);
		}
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

	function _getBlockShortcodesWithMarkdownTransformationDisabled(){ // TODO: I don't like the name of this method
		return array_keys($this->block_shortcodes_with_markdown_transformation_disabled);
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
