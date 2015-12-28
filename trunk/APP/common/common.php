<?php

function aa($a,$b)
{
	return $a."\t-\t".$b;
}

function pp($v,$d=null)
{
	echo '<pre>';
	print_r($v);
	echo '</pre>';
	$d ? die : null;
}

function date2($format,$time=null)
{
	if ($time) {
		return date($format,$time);
	} else {
		return null;
	}
}//date2

?>