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

		// 1-column row
		$src = '
[row]
[col]Col 1[/col]
[/row]
		';
		$expected = '
			<div class="row row--shortcode">
				<div class="col-12 col-xs-12 col-md-12 col--shortcode">
					<p>Col 1</p>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		// 1-column row in a 1-column row
		$src = '
[row class="outer_row"]
[col]
[row class="inner_row"]
[col]
Col 1
[/col]
[/row]
[/col]
[/row]
		';
		$expected = '
			<div class="row row--shortcode outer_row">
				<div class="col-12 col-xs-12 col-md-12 col--shortcode">
					<div class="row row--shortcode inner_row">
						<div class="col-12 col-xs-12 col-md-12 col--shortcode">
							<p>Col 1</p>
						</div>
					</div>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		// 2-column row in a 1-column row
		$src = '
[row class="outer_row"]
[col]
[row class="inner_row"]
[col class="col_1"]
Col 1
[/col]
[col class="col_2"]
Col 2
[/col]
[/row]
[/col]
[/row]
		';
		$expected = '
			<div class="row row--shortcode outer_row">
				<div class="col-12 col-xs-12 col-md-12 col--shortcode">
					<div class="row row--shortcode inner_row">
						<div class="col-12 col-xs-12 col-md-6 col--shortcode col_1">
							<p>Col 1</p>
						</div>
						<div class="col-12 col-xs-12 col-md-6 col--shortcode col_2">
							<p>Col 2</p>
						</div>
					</div>
				</div>
			</div>
		';
		$this->assertHtmlEquals($expected,$markdown->transform($src));

		//
		$src = '
[row class="outer_row"]

[col class="outer_col_left"]
[row class="inner_row_left"]
[col class="inner_col_left"]
Col left
[/col]
[/row]
[/col]

[col class="outer_col_right"]
[row class="inner_row_right"]
[col class="inner_col_right"]
Col right
[/col]
[/row]
[/col]

[/row]

		';
		$expected = '
			<div class="row row--shortcode outer_row">
				<div class="col-12 col-xs-12 col-md-6 col--shortcode outer_col_left">
					<div class="row row--shortcode inner_row_left">
						<div class="col-12 col-xs-12 col-md-12 col--shortcode inner_col_left">
							<p>Col left</p>
						</div>
					</div>
				</div>
				<div class="col-12 col-xs-12 col-md-6 col--shortcode outer_col_right">
					<div class="row row--shortcode inner_row_right">
						<div class="col-12 col-xs-12 col-md-12 col--shortcode inner_col_right">
							<p>Col right</p>
						</div>
					</div>
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
