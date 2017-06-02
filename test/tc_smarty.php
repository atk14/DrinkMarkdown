<?php
class TcSmarty extends TcBase {

	function test(){
		$this->assertEquals('<h1>Welcome</h1>',smarty_modifier_markdown('# Welcome'));

		$template = null;

		$repeat = true;
		$this->assertEquals(null,smarty_block_markdown(array(),'',$template,$repeat));

		$repeat = false;
		$this->assertEquals('<h1>Block</h1>',smarty_block_markdown(array(),'# Block',$template,$repeat));

		// standard markdown modifier doesn't purify HTML code
		$this->assertEquals('<p onclick="JavaScript: alert(\'Hehe!\');">Nice Try</p>',smarty_modifier_markdown('<p onclick="JavaScript: alert(\'Hehe!\');">Nice Try</p>'));
		$this->assertEquals('<p onclick="JavaScript: alert(\'Block?!\');">Nice Try</p>',smarty_block_markdown(array(),'<p onclick="JavaScript: alert(\'Block?!\');">Nice Try</p>',$template,$repeat));

		// safe_markdown is suitable e.g. for users comments, HTML purification is enabled
		$this->assertEquals('<p>Nice Try</p>',smarty_modifier_safe_markdown('<p onclick="JavaScript: alert(\'Hehe!\');">Nice Try</p>'));
		$this->assertEquals('<p>Nice Try</p>',smarty_safe_block_markdown(array(),'<p onclick="JavaScript: alert(\'Block?!\');">Nice Try</p>',$template,$repeat));
	}
}
