<?php
class DrinkMarkdownPostfilter extends DrinkMarkdownFilter {

	function __construct($options = array()){
		$options += array(
			"table_class" => "table",
			"html_purification_enabled" => true,
			"temp_dir" => defined(TEMP) ? TEMP : sys_get_temp_dir(),
			"iobjects_processing_enabled" => true,
			"urlize_text" => true,
		);

		$this->options = $options;
	}

	function filter($content,$transformer){

		$uniqid = uniqid();
		$counter = 0;


		if($this->options["html_purification_enabled"]){
			// Html is being purified
			$tmp_dir = $this->options["temp_dir"].((defined("TEST") && TEST) ? "/test" : "")."/drink_markdown/html_purifier/";
			if(!file_exists($tmp_dir)){ Files::MkDir($tmp_dir); }
			$html_purifier_config = array(
				"Core.Encoding" => 'UTF-8',
				//"AutoFormat.AutoParagraph" => true,
				//"HTML.Doctype" => 'XHTML 1.0 Transitional',
				"Cache.SerializerPath" => $tmp_dir,
				//"HTML.Allowed" => 'h1,h2,h3,h4,p,b,br,a[href],i,img[src|alt|width|height]',
				"Attr.EnableID" => true,
			);
			$config = HTMLPurifier_Config::createDefault();
			foreach($html_purifier_config as $k => $v){
				$config->set($k,$v);
			}
			$purifier = new HTMLPurifier($config);
			$content = $purifier->purify($content); // TODO: purifikace je nutna pouza u komentaru uzivatelu
		}

		if($this->options["urlize_text"]){
			// Converts all URL or email text to links

			$replace_ar = array();

			// the conversion is not desired in <pre> blocks
			preg_match_all('/(<pre>.*?<\/pre>)/s',$content,$matches);
			foreach($matches[1] as $str){
				$replacement = "%link_replace_{$counter}_$uniqid%";
				$replace_ar[$str] = $replacement;
				$counter++;
			}
			$content = EasyReplace($content,$replace_ar);

			$lf = new LinkFinder();
			$content = $lf->process($content,array("escape_html_entities" => false));

			if($replace_ar){
				$replace_back = array_combine(array_values($replace_ar),array_keys($replace_ar));
				$content = EasyReplace($content,$replace_back);
			}
		}

		if($this->options["iobjects_processing_enabled"]){
			// Iobjects
			preg_match_all('/<(p|td)>\[#(?<id>\d+)[^\]]*\]<\/(\1)>/i',$content,$matches);
			foreach($matches["id"] as $i => $id){
				if(!$iobject = Iobject::GetInstanceById($id)){ continue; }

				$_tag = strtolower($matches[1][$i]); // "p" or "td"
				$_source = $iobject->getHtmlSource();

				$transformer->replaces[$matches[0][$i]] = $_tag=="td" ? "<td>{$_source}</td>" : $_source;
			}
		}

		$content = strtr($content,$transformer->replaces);

		// Tables
		if($this->options["table_class"]){
			$content = preg_replace('/<table>/i','<table class="'.htmlentities($this->options["table_class"],ENT_COMPAT).'">',$content);
			$table_class = $this->options["table_class"];
			$content = preg_replace_callback('/(<table\b.*?\bclass=["\'])(.*?)(["\'])/i',function($matches) use ($table_class){
				$current_class = " $matches[2] ";

				if(strpos($current_class," $table_class ")===false){ return "$matches[1]$matches[2] $table_class$matches[3]"; }
				return "$matches[1]$matches[2]$matches[3]";
			},$content);
		}
		$content = preg_replace('/<thead>\s*<tr>(\s*<th[^>]*>\s*<\/th>\s*){1,}<\/tr>\s*<\/thead>/s','<thead></thead>',$content); // Removing empty headers

		$content = "\n$content";
		$content = preg_replace('/(<[a-z][^>]*>)(<\/[^>]+>)(<center>.*?<\/center>)/i','\1\3\2',$content); // "<p></p><center>Centered text</center>"
		$content = preg_replace('/\n<p><\/p><center>\n/',"\n<center>\n",$content);
		$content = preg_replace('/\n<p><\/p><\/center>\n/',"\n</center>\n",$content);
		$content = preg_replace('/^\n/','',$content);

		// Removing HTML syntax glitches
		$content = preg_replace('/<p>(<(font)\b[^>]*>)<\/p>/','\1',$content); // <p><font color="red"></p> -> <font color="red">
		$content = preg_replace('/<p>(<\/(font)>)<\/p>/','\1',$content); // <p></font></p> -> </font>

		// Smazani extra radku na konci souboru
		$content = preg_replace('/\n$/s','',$content);

		return $content;
	}
}
