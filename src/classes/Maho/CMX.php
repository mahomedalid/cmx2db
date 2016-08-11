<?php

	namespace Maho;

	class CMX 
	{
		protected $_secret = NULL;
		protected $_validator = NULL;

		protected $_response = NULL;

		public function __construct($secret, $validator)
		{
			$this->_secret = $secret;
			$this->_validator = $validator;
		}

		public function isEnabled ()
		{
			$hash = md5($this->_validator).".val";
			return file_exists("data/".$hash);
		}

		public function validateSecret ()
		{
		}

		public function response ()
		{
		}
	}
