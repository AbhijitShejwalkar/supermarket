<?php
class DBController {
	private $host = "localhost";
	private $user = "root";
	private $password = "";
	private $database = "supermarket";
	private $conn;
	
	
	function __construct() {
		$this->conn = $this->connectDB();
	}
	
	
     /**
     * 	function for database connection 
     *  @return  mysqli database information
     */
	function connectDB() {
		$conn = mysqli_connect($this->host,$this->user,$this->password,$this->database);
		return $conn;
	}
	
	  /**
     * 	function for fech run query 
     *  @return  array result set 
     */
	function runQuery($query) {
		$result = mysqli_query($this->conn,$query);
		while($row=mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}		
		if(!empty($resultset))
			return $resultset;
	}
	

	  /**
     * 	function for fech number of rows 
     *  @return  row count
     */
	function numRows($query) {
		$result  = mysqli_query($this->conn,$query);
		$rowcount = mysqli_num_rows($result);
		return $rowcount;	
	}
}
?>