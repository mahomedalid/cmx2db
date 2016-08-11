<?php

	namespace Maho;

	class CMX 
	{
		protected $_secret = NULL;
		protected $_validator = NULL;

		protected $_response = NULL;

		protected $_processAdapter = NULL;

		public function __construct($secret, $validator, $adapter = NULL)
		{
			$this->_secret = $secret;
			$this->_validator = $validator;
			if($adapter instanceOf CMX_Request_Adapter) {
				 
			} else {
				$adapter = new CMX_Request_DBAdapter();
			}
			$this->_processAdapter = $adapter;
		}

		public function isEnabled ()
		{
			$hash = md5($this->_validator).".val";
			return file_exists("data/".$hash);
		}

		public function enable ()
                {
                        $hash = md5($this->_validator).".val";
                        file_put_contents("data/".$hash, $this->_secret);
                }

		public function validateSecret ()
		{
			return TRUE;
		}

		public function response ()
		{
		}

		public function processRequest ($request)
		{
			if($this->validateSecret()) {
				$data = json_decode(file_get_contents($request), true);
				$this->_processRequest($data);
			}
		}

		protected function _processRequest ($request)
		{
			$this->_processAdapter->processRequest($request);
		}
	}

	interface CMX_Request_Adapter
	{
		public function processRequest($request);
	}

	class CMX_Request_DBAdapter implements CMX_Request_Adapter
	{
		protected $_table = "cmx_records";
		protected $_columns = array ("apMac", "apTags", "apFloors", "clientMac", "ipv4", "ipv6", "seenTime", "seemEpoch", "ssid", "rssi", "manufacturer", 
						"os", "lat", "lng", "unc");

		/* TABLE SQL 
		 CREATE TABLE cmx_records 
			(id INT NOT NULL AUTO_INCREMENT, 
			apMac VARCHAR(99), apTags VARCHAR(99), apFloors VARCHAR(99), 
			clientMac VARCHAR(99), ipv4 VARCHAR(99), ipv6 VARCHAR(99), 
			seenTime VARCHAR(99), seemEpoch INTEGER, ssid VARCHAR(99), 
			rssi INTEGER, manufacturer VARCHAR(99), os VARCHAR(99), 
			lat DECIMAL, lng DECIMAL, unc DECIMAL, 
			PRIMARY KEY(id));
		*/
		protected $_connectionString = "mysql:host=localhost;dbname=cmx";
		protected $_connectionUser = "root";
		protected $_connectionPassword = "";

		protected $_connection = NULL;

		public function processRequest($request)
		{
			$rows = $request->rows;
			
			foreach($rows as $row) {
				$this->_insertRecord($row, $request);
			}
		}

		protected function _insertRecord ($row, $request)
		{
			$sql = "INSERT INTO {$this->_table} (".implode(', ', $this->_columns).") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			
			$db = $this->_connection();

			$statement = $db->prepare($sql);

			$rawData = array($request->apMac, $request->apTags, $request->apFloors,
					$row->clientMac, $row->ipv4, $row->ipv6, $row->seenTime, $row->seenEpoch, $row->ssid, $row->rssi, 
					$row->manufacturer, $row->os, $row->location->lat, $row->location->lng, $row->location->unc);

			return $statement->execute($rawData);
		}
	
		protected function _connection ()
		{
			if($this->_connection) {
			} else {
				$this->_connection = new PDO($this->_connectionString, $this->_connectionUser, $this->_connectionPassword);
			}
			return $this->_connection;
		}
	}
