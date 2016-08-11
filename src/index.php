<?php

	include_once("classes/Maho/CMX.php");

	use Maho;

	$secret = "09r32jof0fks!";
	$validator = "84285536a7284a388b296c06644d273a74f11953";

	$options = array ("secret" => $secret, "validator" => $validator);

	$cmx = new \Maho\CMX($secret, $validator);

	if($cmx->isEnabled()) {
		$cmx->processRequest("php://input");
		$response = $cmx->response();
	} else {
		$response = $validator;
		$cmx->enable();
	}


	echo $response;
	die ();

