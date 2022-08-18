<?php
class MarkdownShortcodesPostfilter extends DrinkMarkdownFilter {

	function filter($content,$transformer){

		$replaces = $transformer->replaces;
		$replaces["test"] = "test";

		// ARRAY_FILTER_USE_KEY doesn't exist in PHP5.5
		//$replaces = array_filter($replaces,function($k){ return !!preg_match('/drink:_content_replacement_/',$k);},ARRAY_FILTER_USE_KEY);
		$_replaces = array();
		foreach($replaces as $k => $v){
			if(preg_match('/drink:_content_replacement_/',$k)){
				$_replaces[$k] = $v;
			}
		}
		$replaces = $_replaces;

		$content = EasyReplace($content,$replaces);

		$smarty = $transformer->getSmarty();

		// Function shortcodes
		$content = $this->_processFunctionShortcode($transformer,$content,$transformer->getFunctionShortcodes(),$smarty);

		// Inline block shortcodes
		$content = $this->_processBlockShortcode($transformer,$content,$transformer->getInlineBlockShortcodes(),$smarty);

		// Block shortcodes
		$content = $this->_processBlockShortcode($transformer,$content,array("row"),$smarty);
		$shortcodes = $transformer->getBlockShortcodes();
		$content = $this->_processBlockShortcode($transformer,$content,$shortcodes,$smarty);

		$content = $this->_highlightBlockShortcodeErrors($content,$transformer->getBlockShortcodes(),"div");
		$content = $this->_highlightBlockShortcodeErrors($content,$transformer->getInlineBlockShortcodes(),"span");

		return $content;
	}

	private function _processBlockShortcode($transformer,$content,$shortcodes,$smarty){
		if(!$shortcodes){
			return $content;
		}

		$shortcodes_str = "(?<shortcode>".join("|",$shortcodes).")";

		// <!-- drink:row -->
		//
		// <!-- drink:col -->
		//
		// Some content
		//
		// <!-- drink:col -->
		//
		// <!-- drink:row -->

		$pattern = '/<!-- drink:'.$shortcodes_str.'\s(?<params>(?:(?!(<!-- drink:\1 )).)*?)-->(?<source>(?:(?!(<!-- drink:\1 )).)*?)<!-- \/drink:\1 -->/s';

		while(1){
			if(!preg_match($pattern,$content,$matches)){
				break;
			}

			$snippet = $matches[0];
			$shortcode = $matches["shortcode"];
			$source = $matches["source"];
			$params = $this->parseParams($matches["params"]);
			$callback = $transformer->getShortcodeCallback($shortcode);

			if($callback){

				$out = $callback($source,$params);

			}else{

				Atk14Require::Helper("block.drink_shortcode__$shortcode",$smarty);

				$repeat = true;
				$fn = "smarty_block_drink_shortcode__$shortcode";
				$out = $fn($params,$source,$smarty,$repeat);
				if($repeat){
					$repeat = false;
					$out .= $fn($params,$source,$smarty,$repeat);
				}

			}

			if($shortcodes === array("row")){
				// we need to process columns of each row right here,
				// because smarty_block_drink_shortcode__row sets up variable number_of_columns
				$out = $this->_processBlockShortcode($transformer,$out,array("col"),$smarty);
			}

			$content = str_replace($snippet,$out,$content);
		}

		return $content;
	}

	private function _processFunctionShortcode($transformer,$content,$shortcodes,$smarty){
		if(!$shortcodes){
			return $content;
		}

		$shortcodes_str = "(?<shortcode>".join("|",$shortcodes).")";

		$pattern = '/<!-- drink:'.$shortcodes_str.'\s(?<params>.*?)-->/s';

		while(preg_match_all($pattern,$content,$matches)){
			foreach(array_keys($matches[0]) as $i){
				$snippet = $matches[0][$i];
				$shortcode = $matches["shortcode"][$i];
				$params = $this->parseParams($matches["params"][$i]);
				$callback = $transformer->getShortcodeCallback($shortcode);

				if($callback){

					$out = $callback($params);

				}else{

					Atk14Require::Helper("function.drink_shortcode__$shortcode",$smarty);

					$repeat = true;
					$fn = "smarty_function_drink_shortcode__$shortcode";
					$out = $fn($params,$smarty);

				}

				$content = str_replace($snippet,$out,$content);
			}
		}

		return $content;
	}

	protected function _highlightBlockShortcodeErrors($content,$shortcodes,$error_element){
		if(!$shortcodes){ return $content; }

		$h = function($string){
			$string = (string)$string;
			$flags =  ENT_COMPAT | ENT_QUOTES;
			if(defined("ENT_HTML401")){ $flags = $flags | ENT_HTML401; }
			return htmlspecialchars($string,$flags,"ISO-8859-1");
		};

		$shortcodes = "(".join("|",$shortcodes).")";
		$content = preg_replace_callback('/<!-- (\/|)drink:('.$shortcodes.'\b.*?) -->/',function($matches) use($error_element,$h){
			$title = $matches[1]=="/" ? "closing shortcode has no opening pair" : "opening shortcode has no closing pair";
			return '<'.$error_element.' class="bg-warning text-danger" title="'.$title.'">['.$h($matches[1].$matches[2]).']</'.$error_element.'>';
		},$content);
		return $content;
	}

	function parseParams($params_str){
		$params_str = trim($params_str);

		$comma_replacement = '.comma'.uniqid().'.';

		$params_str = str_replace(',',$comma_replacement,$params_str);

		// Atk14Utils::StringToOptions() is used to parse params
		$params_str = preg_replace('/ +([a-z0-9_]+)=/i',',\1=',$params_str); // 'class="message message-default" id=123' -> 'class="message message-default",id=123'
		$params = Atk14Utils::StringToOptions($params_str); // ["class" => "message message-default", "id" => "123"]

		foreach($params as $k => &$v){
			$v = str_replace($comma_replacement,',',$v);
			if(is_string($v) && preg_match('/^"(.*)"$/',$v,$matches)){
				$v = $matches[1];
				$v = str_replace("\\\"","\"",$v);
			}
			if(is_string($v) && preg_match("/^'(.*)'$/",$v,$matches)){
				$v = $matches[1];
				$v = str_replace('\\\'','\'',$v);
			}
		}
		return $params;
	}
}
