<?php
	//non file output:
	//header("Content-type: text/html; charset=UTF-8");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=data.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	//get mysql server info
	$hostname = "localhost";
	$dbase = getenv('DBNAME');
	$dbusr = getenv('DBUSR');
	$dbpass = getenv('DBPASS');
	$range = 30; //get recent 30 days only
	$limit = 21600; // last 2-weeks in database (15 min data) : used w test DB
    
	$site = $_GET['site']; //TODO: check args are set/empty
	$param = $_GET['param'];  //TODO: check args are set/empty


	$con = mysqli_connect($hostname,$dbusr,$dbpass,$dbase);

	// Check connection
	if (mysqli_connect_errno()) {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  exit();
	}

	if ($site == "TJROS"){
		//for prod database that should have data current to now()
		//$sql = "SELECT CONVERT_TZ(time,'+00:00','-08:00'),$param FROM $site WHERE time != '0000-00-00 00:00:00' AND flag >= 0 AND time BETWEEN NOW() - INTERVAL $range DAY AND NOW() order by time;";

		//for the testing database that does not have current data get last n records instead of recent n days
		$sql = "SELECT CONVERT_TZ(time,'+00:00','-08:00'),$param FROM $site WHERE time != '0000-00-00 00:00:00' AND flag >= 0 order by time desc limit $limit;";

		if ($result = mysqli_query($con, $sql)){
			$fields = mysqli_num_fields ( $result );
			echo "datetime,$param\n";
			while($row = mysqli_fetch_row($result)){
				echo implode(",", $row) . "\n";
			}
		}
	} else { 
		//$sql = "SELECT time_pst,Discharge_cms,15m_ppt_mm FROM IBWCtj WHERE time_pst BETWEEN NOW() - INTERVAL $range DAY AND NOW() order by time_pst;";

		//for the testing database that does not have current data get last n records instead of recent n days
		$sql = "SELECT time_pst,Discharge_cms,15m_ppt_mm FROM IBWCtj WHERE time_pst order by time_pst desc limit $limit;";

		if ($result = mysqli_query($con, $sql)){
			$fields = mysqli_num_fields ( $result );
			//output header row
			echo "Date,Discharge_cms,15minRain_mm\n";
			while($row = mysqli_fetch_row($result)){
				echo implode(",", $row) . "\n";
			}
		}
	}
	mysqli_close($con);
?>