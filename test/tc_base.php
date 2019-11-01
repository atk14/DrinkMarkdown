<?php
class TcBase extends TcSuperBase {

	function assertHtmlEquals($expected,$actual){
		$this->assertTrue($this->_compare_html($expected,$actual),"\n\n### expected ###\n$expected\n\n### actual ###\n$actual\n\n");
	}
	
	function _compare_html($expected,$actual){
		$expected = $this->_htmlToXml($expected);
		$actual = $this->_htmlToXml($actual);

		$expected = new XMole($expected);
		$actual = new XMole($actual);
		return XMole::AreSame($expected,$actual);
	}

	function _htmlToXml($html){
		$xml = "<xml>$html</xml>";
		$xml = preg_replace('/<(img|br)\b(.*?)>/','<\1\2/>',$xml);
		return $xml;
	}
}
