<?php
class Iobject {

	static function GetInstanceById($id){
		return new Image($id);
	}
}
