<?php
class TcSmarty extends TcBase {

	function test(){
		$this->assertEquals('<h1>Welcome</h1>',smarty_modifier_markdown('# Welcome'));

		$template = null;

		$require = true;
		$this->assertEquals(null,smarty_block_markdown(array(),'',$template,$repeat));

		$repeat = false;
		$this->assertEquals('<h1>Block</h1>',smarty_block_markdown(array(),'# Block',$template,$repeat));
	}
}
