<?php
class MarkdownShortcodesPostfilter extends DrinkMarkdownFilter {

	function filter($content,$transformer){
		$shortcodes = array("row","col");
		$shortcodes_str = "(?<shortcode>".join("|",$shortcodes).")";

		$smarty = Atk14Utils::GetSmarty();
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

		// Block helpers
		$pattern = '/<!-- drink:'.$shortcodes_str.' (?<params>.*?)-->(?<source>.*?)<!-- \/drink:\1 -->?/s';
		while(preg_match_all($pattern,$content,$matches)){
			foreach(array_keys($matches[0]) as $i){
				$snippet = $matches[0][$i];
				$shortcode = $matches["shortcode"][$i];
				$source = $matches["source"][$i];
				$params = $this->parseParams($matches["params"][$i]);

				Atk14Require::Helper("block.drink_shortcode__$shortcode",$smarty);
				
				$repeat = true;
				$fn = "smarty_block_drink_shortcode__$shortcode";
				$out = $fn($params,$source,$smarty,$repeat);
				if($repeat){
					$repeat = false;
					$out .= $fn($params,$source,$smarty,$repeat);
				}

				$content = str_replace($snippet,$out,$content);
			}
		}

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
