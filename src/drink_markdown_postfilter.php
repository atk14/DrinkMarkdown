<?php
class DrinkMarkdownPostfilter {

	function __construct($options = array()){
		$options += array(
			"table_class" => "table table-bordered table-hover",
      "html_purification_enabled" => true,
      "temp_dir" => defined(TEMP) ? TEMP : sys_get_temp_dir(),
		);

    $this->options = $options;
	}

	function filter($content,$transformer){
		$replace_ar = array();

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

		// TODO: refactor the mess

		// URLs and emails in their plain forms are converted to links using LinkFinder
		// At first we must hide existing links in the document
		preg_match_all('/(<a\s[^>]*>.*?<\/a>)/',$content,$matches);
		foreach($matches[1] as $link){
			$replacement = "%link_replace_{$counter}_$uniqid%";
			$replace_ar[$link] = $replacement;
			$counter++;
		}

		preg_match_all('/(<pre>.*?<\/pre>)/s',$content,$matches);
		foreach($matches[1] as $str){
			$replacement = "%link_replace_{$counter}_$uniqid%";
			$replace_ar[$str] = $replacement;
			$counter++;
		}

		//
		$content = EasyReplace($content,$replace_ar);

		$lf = new LinkFinder();
		$content = $lf->process($content,array("escape_html_entities" => false));

		if($replace_ar){
			$replace_back = array_combine(array_values($replace_ar),array_keys($replace_ar));
			$content = EasyReplace($content,$replace_back);
		}

		// Iobjects
		preg_match_all('/<p>\[#(\d+)[^\]]*\]<\/p>/',$content,$matches);
		foreach($matches[1] as $i => $id){
			if(!$iobject = Iobject::GetInstanceById($id)){ continue; }

			$transformer->replaces[$matches[0][$i]] = $iobject->getHtmlSource();
		}

		$content = strtr($content,$transformer->replaces);

		// Tables
		if($this->options["table_class"]){
			$content = preg_replace('/<table>/','<table class="'.htmlentities($this->options["table_class"],ENT_COMPAT).'">',$content);
		}
		$content = preg_replace('/<thead>\s*<tr>(\s*<th[^>]*>\s*<\/th>\s*){1,}<\/tr>\s*<\/thead>/s','<thead></thead>',$content); // Removing empty headers

		$content = "\n$content";
		$content = preg_replace('/(<[a-z][^>]*>)(<\/[^>]+>)(<center>.*?<\/center>)/i','\1\3\2',$content); // "<p></p><center>Centered text</center>"
		$content = preg_replace('/\n<p><\/p><center>\n/',"\n<center>\n",$content);
		$content = preg_replace('/\n<p><\/p><\/center>\n/',"\n</center>\n",$content);
		$content = preg_replace('/^\n/','',$content);

		// Smazani extra radku na konci souboru
		$content = preg_replace('/\n$/s','',$content);

		return $content;
	}
}
