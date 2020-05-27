<?php
class MarkdownShortcodesPrefilter extends DrinkMarkdownFilter {

	function filter($raw,$transformer){
		// Opening tags
		// [col] --> <!-- drink:col -->
		// [col align="right" class="highlight"] ---> <!-- drink:col align="right" class="highlight" -->
		foreach(array(
			array($transformer->getBlockShortcodes(),'<!-- block_shortcode_break -->'),
			array($transformer->getInlineBlockShortcodes(),''),
			array($transformer->getFunctionShortcodes(),'')
		) as $item){
			$shortcodes = $item[0];
			$break = $item[1];

			if(!$shortcodes){ continue; }

			$shortcodes_str = "(?<shortcode>".join("|",$shortcodes).")";

			if($break){
				$raw = preg_replace('/[\r\n\s]*\[('.$shortcodes_str.'(?<params>| [^]]*))\][\r\n\s]*/s',"$break<!-- drink:\\1 -->$break",$raw);
			}else{
				$raw = preg_replace('/\[('.$shortcodes_str.'(?<params>| [^]]*))\]/s',"<!-- drink:\\1 -->",$raw);
			}

		}

		// Closing tags:
		// [/col] ---> <!-- /drink:col -->
		// [/row] ---> <!-- /drink:row -->
		foreach(array(
			array($transformer->getBlockShortcodes(),'<!-- block_shortcode_break -->'),
			array($transformer->getInlineBlockShortcodes(),''),
		) as $item){
			$shortcodes = $item[0];
			$break = $item[1];

			if(!$shortcodes){ continue; }

			$shortcodes_str = "(?<shortcode>".join("|",$shortcodes).")";

			if($break){
				$raw = preg_replace('/[\r\n\s]*\[\/'.$shortcodes_str.'\][\r\n\s]*/s',"$break<!-- /drink:\\1 -->$break",$raw);
			}else{
				$raw = preg_replace('/\[\/'.$shortcodes_str.'\]/',"<!-- /drink:\\1 -->",$raw);
			}
		}

		$raw = preg_replace('/(<!-- block_shortcode_break -->)+([\s\n\r]*)$/s','\2',$raw);
		$raw = preg_replace('/^([\s\n\r]*)(<!-- block_shortcode_break -->)+/s','\1',$raw);
		$raw = preg_replace('/(<!-- block_shortcode_break -->)+/',"\n\n",$raw);

		$shortcodes = $transformer->_getBlockShortcodesWithMarkdownTransformationDisabled();
		if($shortcodes){
			$shortcodes_str = "(?<shortcode>".join("|",$shortcodes).")";
			$replaces = array();
			preg_match_all("/(?<openingtag><!-- (drink:$shortcodes_str) .*?-->)(?<content>.*?)(?<closingtag><!-- \\/\\2 -->)/s",$raw,$matches);
			for($i=0;$i<sizeof($matches[0]);$i++){
				$openingtag = $matches["openingtag"][$i];
				$closingtag = $matches["closingtag"][$i];
				$content = $matches["content"][$i];
				$placeholder = "<!-- drink:_content_replacement_$i -->";
				$replaces["$openingtag$content$closingtag"] = "$placeholder";
				$transformer->replaces[$placeholder] = "$openingtag$content$closingtag";
			}
			$raw = EasyReplace($raw,$replaces);
		}

		//echo "<pre>";
		//echo h($raw);
		//echo "</pre>";

		return $raw;
	}
}
