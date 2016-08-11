<?php

	include_once("classes/Maho/CMX.php");

	use Maho;

	$secret = "";
	$validator = "";

	$options = array ("secret" => $secret, "validator" => $validator);

	$cmx = new \Maho\CMX($secret, $validator);

	if($cmx->isEnabled()) {
		
	} else {
		$cmx->validateSecret();
	}

	echo $cmx->response();
	die ();

