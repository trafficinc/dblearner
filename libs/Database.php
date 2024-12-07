<?php

class Database
{

	private PDO $conn;
	private $stmt;

	public function __construct()
	{
        /** @var $host string */
        /** @var $db string */
		include_once('dbconfig.php');
		$dsn = 'mysql:host=' . $host . ';dbname=' . $db;

		$options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		try {
            /** @var $user string */
            /** @var $pass string */
            $this->conn = new PDO($dsn, $user, $pass, $options);
		} catch (PDOException $e) {
            die($e->getMessage());
		}
	}

	public function query($query)
	{
		$this->stmt = $this->conn->query($query);
	}

	// Prepare statement with query
	public function queryPrepare($query)
	{
		$this->stmt = $this->conn->prepare($query);
	}

	// Bind values
	public function bind($param, $value, $type = null)
	{
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}

	// Execute the prepared statement
	public function execute()
	{
		return $this->stmt->execute();
	}

	public function exec($sql)
	{
		return $this->conn->exec($sql);
	}

	// Get result set as array of objects
	public function resultset()
	{
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_OBJ);
	}

	// Get single record as object
	public function single()
	{
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_OBJ);
	}

	// Get record row count
	public function rowCount()
	{
		return $this->stmt->rowCount();
	}

	// Returns the last inserted ID
	public function lastInsertId()
	{
		return $this->conn->lastInsertId();
	}

	public function getStmt() {
		return $this->stmt;
	}

	public function fetchAll($type = PDO::FETCH_OBJ) {
		return $this->stmt->fetchAll($type);
	}

	public function fetch($type = PDO::FETCH_OBJ) {
		return $this->stmt->fetch($type);
	}
}
