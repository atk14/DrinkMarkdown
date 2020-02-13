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
		switch($this->id){
			case 101:
				return 'http://www.example.com/rose_'.$this->id.'.jpg?with=333&height=444';
			case 102:
				return '';
			default:
				return 'http://www.example.com/rose_'.$this->id.'.jpg';
		}
	}
}
