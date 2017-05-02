<?php
// Here is the function EasyReplace which is in the ATK14 Framework - TODO: need to be removed

/**
* Searches and replaces in a string.
*
*	<code>
* 	echo EasyReplace("Hi %who%, how it's %what%?",array("%who%" => "Valda", "%what%" => "going"));
* </code>
*
*	@param string		$str
*	@param array		$replaces	associative array
*	@return	strig
*/
function EasyReplace($str,$replaces){
	settype($replaces,"array");
	return str_replace(array_keys($replaces),array_values($replaces),$str);
}
