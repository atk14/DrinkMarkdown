<?php
class DrinkMarkdown{

	const VERSION = "0.7.2";

	var $replaces = array();

	protected $prefilters = array();
	protected $postfilters = array();

	protected $block_shortcodes = array("row" => "row", "col" => "col");
	protected $inline_block_shortcodes = array();
	protected $function_shortcodes = array();
	protected $shortcode_callbacks = array();
	protected $block_shortcodes_with_markdown_transformation_disabled = array();

	protected $smarty;

	function __construct($options = array()){
		static $autodiscovered_shortcodes = array();

		$options += array(
			"prefilter" => null,
			"postfilter" => null,
			"shortcodes_enabled" => true,
			"smarty" => null,
		);

		$prefilter = $options["prefilter"];
		$postfilter = $options["postfilter"];

		unset($options["prefilter"]);
		unset($options["postfilter"]);

		$this->prefilters[] = $prefilter ? $prefilter : new DrinkMarkdownPrefilter($options);
		$this->postfilters[] = $postfilter ? $postfilter : new DrinkMarkdownPostfilter($options);
	
		// Preparing Smarty object
		if(!$options["smarty"]){
			$options["smarty"] = Atk14Utils::GetSmarty();
		}
		$smarty = $options["smarty"];
		// adding template dir
		$template_dirs = $smarty->getTemplateDir();
		$template_dirs = is_array($template_dirs) ? $template_dirs : array($template_dirs);
		$template_dirs[] = __DIR__ . "/../app/views/";
		$smarty->setTemplateDir($template_dirs);
		// adding plugin dir
		$plugin_dirs = $smarty->getPluginsDir();
		$plugin_dirs = is_array($plugin_dirs) ? $plugin_dirs : array($plugin_dirs);
		$plugin_dirs[] = __DIR__ . "/../app/helpers/";
		$smarty->setPluginsDir($plugin_dirs);
		//
		$this->smarty = $smarty;

		if($options["shortcodes_enabled"]){
			$this->appendPrefilter(new MarkdownShortcodesPrefilter());
			$this->prependPostfilter(new MarkdownShortcodesPostfilter());
			$this->registerBlockShortcode("div", function($content,$params){
				$h = function($string){
					$flags =  ENT_COMPAT | ENT_QUOTES;
					if(defined("ENT_HTML401")){ $flags = $flags | ENT_HTML401; }
					return htmlspecialchars($string,$flags,"ISO-8859-1");
				};

				$attrs = [];
				foreach($params as $k => $v){
					$attrs[] = sprintf('%s="%s"',$h($k),$h($v));
				}
				$attrs = $attrs ? " ".join(" ",$attrs) : "";

				return "<div$attrs>\n$content\n</div>";
			});

			// Smarty shortcode autowiring
			$plugins_dir = array_unique($this->smarty->getPluginsDir());
			$ad_key = md5(serialize($plugins_dir));
			if(!isset($autodiscovered_shortcodes[$ad_key])){
				$autodiscovered_shortcodes[$ad_key] = array(
					"block_shortcodes" => array(),
					"function_shortcodes" => array(),
				);
				foreach($plugins_dir as $dir){
					if(!file_exists($dir)){ continue; }
					foreach(scandir($dir) as $file){
						if(preg_match("/^block.drink_shortcode__(.+).php$/",$file,$matches)){
							$autodiscovered_shortcodes[$ad_key]["block_shortcodes"][] = $matches[1];
						}
						if(preg_match("/^function.drink_shortcode__(.+).php$/",$file,$matches)){
							$autodiscovered_shortcodes[$ad_key]["function_shortcodes"][] = $matches[1];
						}
					}
				}
			}
			foreach($autodiscovered_shortcodes[$ad_key]["block_shortcodes"] as $shortcode){
				if($this->isShortcodeRegistered($shortcode)){ continue; }
				$this->registerBlockShortcode($shortcode);
			}
			foreach($autodiscovered_shortcodes[$ad_key]["function_shortcodes"] as $shortcode){
				if($this->isShortcodeRegistered($shortcode)){ continue; }
				$this->registerFunctionShortcode($shortcode);
			}

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

	function isShortcodeRegistered($shortcode){
		$shortcode = (string)$shortcode;
		return isset($this->block_shortcodes[$shortcode]) || isset($this->inline_block_shortcodes[$shortcode]) || isset($this->function_shortcodes[$shortcode]);
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
		return array_values($this->block_shortcodes);
	}

	function registerInlineBlockShortcode($shortcode,$options = array()){
		$this->_registerBlockShortcode($shortcode,$options,true);
	}

	function getInlineBlockShortcodes(){
		return array_values($this->inline_block_shortcodes);
	}

	protected function _unregisterShortcode($shortcode){
		unset($this->block_shortcodes[$shortcode]);
		unset($this->inline_block_shortcodes[$shortcode]);
		unset($this->function_shortcodes[$shortcode]);
		unset($this->shortcode_callbacks[$shortcode]);
		unset($this->block_shortcodes_with_markdown_transformation_disabled[$shortcode]);
	}

	protected function _registerBlockShortcode($shortcode,$options,$inline){
		$this->_unregisterShortcode($shortcode);

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
			$this->inline_block_shortcodes[$shortcode] = $shortcode;
		}else{
			$this->block_shortcodes[$shortcode] = $shortcode;
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
		$this->_unregisterShortcode($shortcode);

		$this->function_shortcodes[$shortcode] = $shortcode;
		$this->shortcode_callbacks[$shortcode]  = $callback;
	}

	function getFunctionShortcodes(){
		return array_values($this->function_shortcodes);
	}

	function getShortcodeCallback($shortcode){
		return isset($this->shortcode_callbacks[$shortcode]) ? $this->shortcode_callbacks[$shortcode] : null;
	}

	function _getBlockShortcodesWithMarkdownTransformationDisabled(){ // TODO: I don't like the name of this method
		return array_keys($this->block_shortcodes_with_markdown_transformation_disabled);
	}

	function getSmarty(){
		return $this->smarty;
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
