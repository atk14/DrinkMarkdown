<?php
class TcRowsAndCols extends TcBase {

	function test(){
		$markdown = new DrinkMarkdown(array("shortcodes_enabled" => true));

		$src = '[row][col]Hello World![/col][/row]';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-12 col--shortcode">
					<p>Hello World!</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		$src = '[row][col]Hello World![/col][col]Hello Another World![/col][/row]';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-6 col--shortcode">
					<p>Hello World!</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col--shortcode">
					<p>Hello Another World!</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));


		$src = '[row][col]Hello World![/col][col]Hello Another World![/col][col]Hello from outer space![/col][/row]';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-4 col--shortcode">
					<p>Hello World!</p>
				</div>
				<div class="col-12 col-xs-12 col-md-4 col--shortcode">
					<p>Hello Another World!</p>
				</div>
				<div class="col-12 col-xs-12 col-md-4 col--shortcode">
					<p>Hello from outer space!</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));


		$src = '[row][col]Hello World![/col][/row]
		[row][col]Hello Another World![/col][col]Hello from outer space![/col][/row]';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-12 col--shortcode">
					<p>Hello World!</p>
				</div>
			</div>
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-6 col--shortcode">
					<p>Hello Another World!</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col--shortcode">
					<p>Hello from outer space!</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		// 4-column row
		$src = '
[row]
[col]Col 1[/col]
[col]Col 2[/col]
[col]Col 3[/col]
[col]Col 4[/col]
[/row]
		';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-6 col-lg-3 col--shortcode">
					<p>Col 1</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-3 col--shortcode">
					<p>Col 2</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-3 col--shortcode">
					<p>Col 3</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-3 col--shortcode">
					<p>Col 4</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		// 6-column row
		$src = '
[row]
[col]Col 1[/col]
[col]Col 2[/col]
[col]Col 3[/col]
[col]Col 4[/col]
[col]Col 5[/col]
[col]Col 6[/col]
[/row]
		';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-6 col-lg-4 col-xl-2 col--shortcode">
					<p>Col 1</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-4 col-xl-2 col--shortcode">
					<p>Col 2</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-4 col-xl-2 col--shortcode">
					<p>Col 3</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-4 col-xl-2 col--shortcode">
					<p>Col 4</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-4 col-xl-2 col--shortcode">
					<p>Col 5</p>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col-lg-4 col-xl-2 col--shortcode">
					<p>Col 6</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		$src = '
[row]
[col class="alert-success"]Success[/col]
[col class="alert-info"]Info[/col]
[col class="alert-danger"]Danger[/col]
[/row]
		';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-4 col--shortcode alert-success">
					<p>Success</p>
				</div>
				<div class="col-12 col-xs-12 col-md-4 col--shortcode alert-info">
					<p>Info</p>
				</div>
				<div class="col-12 col-xs-12 col-md-4 col--shortcode alert-danger">
					<p>Danger</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));
	}

	function _compressHtml($html){
		$out = array();
		foreach(explode("\n",$html) as $line){
			$line = trim($line);
			if(!strlen($line)){ continue; }
			$out[] = $line;
		}
		return join("\n",$out);
	}

	function assertHtmlEquals($expected,$html,$message = ""){
		$this->assertEquals($this->_compressHtml($expected),$this->_compressHtml($html),$message);
	}
}
