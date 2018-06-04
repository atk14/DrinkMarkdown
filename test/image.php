<?php
class Image {

	protected $id;

	function __construct($id){
		$this->id = $id;
	}

	function getHtmlSource(){
		return '<img src="rose_'.$this->id.'.jpg" with="444" height="333">';
	}

	function getDetailUrl(){
		return 'http://www.example.com/rose_'.$this->id.'.jpg';
	}
}
