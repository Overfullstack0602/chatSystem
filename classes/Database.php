<?php 

class Database
{
	private $pdo;
	private $options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
	];

	public function __construct()
	{
		$username = "root";
		$password = "";
		$dbname = "chat";
		$dsn = "mysql:host=localhost;dbname=".$dbname."";
		try
		{
			$this->pdo = new PDO($dsn, $username, $password, $this->options);
		}
		catch(PDOException $e)
		{
			$error_msg = "Database Connection Error.";
			echo $error_msg;
		}
	}

	// Selecting From Database
	public function get($table_name, $col="", $val="")
	{
		try
		{
			$query = "SELECT * FROM $table_name";
			if($col !== "" && $val !== "")
			{
				$query .= " WHERE $col = ? ";
				$stmt = $this->pdo->prepare($query);
				$stmt->bindValue(1, $val);
				$stmt->execute();
			}
			else
			{
				$stmt = $this->pdo->query($query);
			}
			return $stmt->fetchAll();
		}
		catch(PDOException $e) { echo "Database Error."; exit; }
	}

	// Inserting Data to any table
	public function insert($table_name, $columns, $values)
	{
		$columns_imploded = implode(", ", $columns);
		$query = "INSERT INTO $table_name ($columns_imploded) VALUES ( ";

		for($i = 0; $i < count($columns); $i++)
		{
			if($i === count($columns) - 1)
			{
				$query .= "? )";
			}
			else
			{
				$query .= "?, ";
			}
		}
		
		try
		{
			$stmt = $this->pdo->prepare($query);

			foreach ($values as $key => $value) {
				$stmt->bindValue(++$key, $value);
			}
			if( $stmt->execute() )
			{
				return $this->pdo->lastInsertId();
			}
			return false;
		}
		catch(PDOException $e) { echo "Database Error."; exit; }
	}

	// Update Any Table's Data
	public function update($table_name, $columns, $values, $col, $val)
	{
		$query = "UPDATE $table_name SET ";
		if(count($columns) === count($values))
		{
			for ($i=0; $i < count($columns); $i++) {
				$column = $columns[$i];
				
				if($i === count($columns) - 1)
				{
					$query .= " $column = ? ";
				}
				else
				{
					$query .= " $column = ?, ";
				}
			}
			$query .= " WHERE $col = ? ";

			try
			{
				$stmt = $this->pdo->prepare($query);

				$i = 0;
				foreach ($values as $key => $value) {
					$stmt->bindValue(++$key, $value);
					$i = $key;
				}
				$stmt->bindValue(++$i, $val);
				return $stmt->execute();
			}
			catch(PDOException $e) { echo "Database Error."; exit; }
		}
	}

	// Delete From Any Table
	public function delete($table_name, $col, $val)
	{
		$query = "DELETE FROM $table_name WHERE $col = ? ";
		try
		{
			$stmt = $this->pdo->prepare($query);
			$stmt->bindValue(1, $val);
			return $stmt->execute();
		}
		catch(PDOException $e) { echo "Database Error."; exit; }
	}

	public function customQuery($query, $values, $type = "select")
	{
		try
		{
			if( $type === "select")
			{
				$stmt = $this->pdo->prepare($query);
				$stmt->execute($values);
				return $stmt->fetchAll();
			}
			else
			{
				$stmt = $this->pdo->prepare($query);
				return $stmt->execute($values);
			}
		}
		catch(PDOException $e) { echo "Database Error."; exit; }
	}
}

$db = new Database();