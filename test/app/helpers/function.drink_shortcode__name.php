<?php
function smarty_function_drink_shortcode__name($params,$template){
	$params += array(
		"gender" => "male"
	);

	return $params["gender"]=="female" ? "Samantha Doe" : "John Doe";
}
