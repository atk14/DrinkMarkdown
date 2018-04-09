<?php
class UppercasePostfilter extends DrinkMarkdownFilter {

	function filter($html,$transformer){
		$replaces_back = $this->hideSomething('/<[^>]+>/',$html); // removes all tags

		$html = strtoupper($html); // converts $html to uppercase

		$_replaces_back = array();
		foreach($replaces_back as $key => $value){
			$_replaces_back[strtoupper($key)] = $value;
		}
		$replaces_back = $_replaces_back;

		$html = EasyReplace($html,$replaces_back); // restores original tags

		return $html;
	}
}
