<?php
	/*
	require 'vendor/autoload.php';
	use Aws\S3\S3Client;
	
	$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-west-1', #改为美国西部
    'credentials' => [
        'key'    => 'AKIAIHRO3BEBBW5GXZJA', #访问秘钥
        'secret' => 'NEhTnYzvc26UnZyTEsICJE7p2MIcPP2PivlyzggL' #私有访问秘钥
    ]
	]);
	$bucketName = 'aymbb'; #存储桶的名字

*/
	ini_set("error_reporting","E_ALL & ~E_NOTICE");
	header("Content-type:text/html;charset=utf-8");
	include("conn.php");
	function diffBetweenTwoDays ($day1, $day2)
		{
		  $second1 = strtotime($day1);
		  $second2 = strtotime($day2);
		    
		  if ($second1 < $second2) {
		    $tmp = $second2;
		    $second2 = $second1;
		    $second1 = $tmp;
		  }
		  return ($second1 - $second2) / 86400;
		}
	$Rain_csv_header = ['Station ID','Station Name','Latitude','Longitude','Total Observed Amount','Observed days','Daily Average'];
	$MaxTemp_csv_header = ['Station ID','Station Name','Latitude','Longitude','Daily Observed','Daily Average MaxTemperature'];
	$MinTemp_csv_header = ['Station ID','Station Name','Latitude','Longitude','Daily Observed','Daily Average MaxTemperature'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>weather</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/index.css">
	<script src="jquery/jquery-1.12.4.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="js/clipboard.min.js"></script>
	<script src="js/highcharts.js"></script>
	<script src="js/highcharts-zh_CN.js"></script>
	<script src="js/exporting.js"></script>
</head>
<body>
	<!--header div-->
	<div class="big">
			<div class="small"><b>Weather Analytics</b></div>
	</div>
	<script type="text/javascript">
		function Show_Hidden(obj)
		{
		 if(obj.style.display=="block")
				 {
				  obj.style.display='none';
				 }
		 else
				 {
				  obj.style.display='block';
				 }
		}
		window.onload=function()
			{
			 var olink1=document.getElementById("a1");
			 var olink2=document.getElementById("a2");
			 var olink3=document.getElementById("a3");
			 var olink4=document.getElementById("a4");
			 var hide=document.getElementById("hide");
			 var show=document.getElementById("show");
			 var ddiv1=document.getElementById("map"); 
			 var ddiv2=document.getElementById("table");
			 var hide1=document.getElementById("hide1");
			 var show1=document.getElementById("show1");
			 var ddiv3=document.getElementById("map1"); 
			 var ddiv4=document.getElementById("table1");
			 olink1.onclick=function()
			 {
			  Show_Hidden(hide);
			  Show_Hidden(show);
			  Show_Hidden(ddiv1);
			  Show_Hidden(ddiv2);
			  return false;
			 }
			 olink2.onclick=function()
			 {
			  Show_Hidden(hide);
			  Show_Hidden(show);
			  Show_Hidden(ddiv1);
			  Show_Hidden(ddiv2);
			  return false;
			 }

			 olink3.onclick=function()
			 {
			  Show_Hidden(hide1);
			  Show_Hidden(show1);
			  Show_Hidden(ddiv3);
			  Show_Hidden(ddiv4);
			  return false;
			 }
			 olink4.onclick=function()
			 {
			  Show_Hidden(hide1);
			  Show_Hidden(show1);
			  Show_Hidden(ddiv3);
			  Show_Hidden(ddiv4);
			  return false;
			 }
			}
		function openMarker(marker,text){
			var infowindow = new google.maps.InfoWindow(
			{ content: text,});
		     google.maps.event.addListener(marker, 'click', function() {
			 infowindow.open(map,marker);
		    });
		}
		function getCircle(magnitude,color) {
			 return {
			 path: google.maps.SymbolPath.CIRCLE,
			 fillColor: color,
			 fillOpacity: 0.8,//用来设置填充颜色透明度（范围：0 - 1） 
			 scale:magnitude,
			 strokeColor: 'white',
			 strokeWeight:.5
					 };
		}
				 	 
       
	</script>
	<?php
		if(!empty($_GET['get'])){
			echo "<script>
			    $(function () {
			        $('#myTab li:eq(2) a').tab('show');
			    });
				</script>";
		}else if(!empty($_GET['plot1'])){
			echo "<script>
			    $(function () {
			        $('#myTab li:eq(1) a').tab('show');
			    });
				</script>";
		}else{
			echo "<script>
			    $(function () {
			        $('#myTab li:eq(0) a').tab('show');
			    });
				</script>";
		}
	?>
	<!--centen div-->
	<div class="option" >
		<ul id="myTab" class="nav nav-tabs">
		   <li class="active" style="width: 33.3%;text-align: center;" >
		   		<a href="#Rainfall" data-toggle="tab">
		      		Rainfall across stations
		      	</a>
		   </li>
		   <li style="width: 33.3%;text-align: center;" >
		   		<a href="#Temperature" data-toggle="tab">
		   			Temperature across stations
		   		</a>
		   </li>
		   <li style="width: 33.3%;text-align: center;" >
		   		<a href="#other" data-toggle="tab">
		   			Monthly Rainfall/Min/Max Temperature for a station
		   		</a>
		   </li>
		</ul>
	</div>
	
	
	<div id="myTabContent" class="tab-content">
	<!--Rainfall 选项卡-->
	<?php
			$from = $_GET['from'];
			$to = $_GET['to'];
			//echo $from." ".$to;
			$current_mon_days = date('t', strtotime($from));
			$diff = diffBetweenTwoDays($from, $to);
			$diff = $diff + 1;
			$filename = "Rainfall".$from."-".$to.".csv";
	?>
	   <div class="tab-pane fade in active" id="Rainfall">
	        <div style="width:100%;">
	        <form action="" method="get" >
	        	 From:<input type="date" name="from" value="<?php echo $_GET['from']?>"> To:<input type="date" name="to" value="<?php echo $_GET['to']?>">
	        	 <input type="submit" name="plot" value="PLOT DATA" class="btn btn-default">
	        </form>
	        </div>
	        <div style="width:100%;">
	        	 Quick jump
	        </div>
			<div style="width:100%">
				 2018 <a href="index.php?from=2018-01-01&to=2018-01-31&plot=PLOT+DATA">January</a> <a href="index.php?from=2018-02-01&to=2018-02-28&plot=PLOT+DATA">February</a> <a href="index.php?from=2018-03-01&to=2018-03-31&plot=PLOT+DATA">March</a> <a href="index.php?from=2018-04-01&to=2018-04-30&plot=PLOT+DATA">April</a> <a href="index.php?from=2018-05-01&to=2018-05-31&plot=PLOT+DATA">May</a> <a href="index.php?from=2018-06-01&to=2018-06-30&plot=PLOT+DATA">June</a> <a href="index.php?from=2018-07-01&to=2018-07-31&plot=PLOT+DATA">July</a> <a href="index.php?from=2018-08-01&to=2018-08-31&plot=PLOT+DATA">August</a>  
			</div>
			<div style="width: 100%;">
				<div id="hide" style="display:block;">Showing aggregated data for: <?php echo $diff;?> days<a href="#" id="a1">show data table</a></div>
				<div id="show">Showing aggregated data for: <?php echo $diff;?> days<a href="#" id="a2">hide data table</a>&nbsp;<a href="<?php if(!empty($_GET['plot'])) echo $filename ;?>" >Download table</a>&nbsp;<a href="#" >Download Raw table</a></div>
			</div>
			<div id="map" style="display: block;"></div>
			<div id="table" style="width: 100%;">
				<table class="table">
					 <tr>
					 	<th width="10%">Station ID</th>
					 	<th width="25%">Station Name</th>
					 	<th width="20%">Location</th>
					 	<th width="14%">Total Observed Amount</th>
					 	<th width="11%">Observed Days</th>
					 	<th width="10%">Daily Average</th>
					 </tr>
					 <?php
					 		$sql = "select * from station";
							$result = mysqli_query($conn,$sql);
							if(!empty($_GET['plot'])){
								$fp = fopen($filename,'w');
							}
							// 处理头部标题
							$header = implode(',', $Rain_csv_header).PHP_EOL;
							// 处理内容
							$text = '';
							while($row = mysqli_fetch_array($result)){
					  			$Rain_sql = "select * from dailyrainfall where stationID = '".$row['stationID']."' and date between '".$from."' and '".$to."'";
					  			$Rain_result = mysqli_query($conn,$Rain_sql);
								$amout_sum = 0;
								while($arr = mysqli_fetch_array($Rain_result)){
										$amout_sum+=$arr['amount'];
								}
								$Rain_avg = $amout_sum/$diff;
								$perc = $diff/$current_mon_days*100;
								//
								$v_arr = array($row['stationID'],$row['stationName'],$row['latitude'],$row['longitude'],$amout_sum,$diff,$Rain_avg);
								$text .= implode(',',$v_arr) . PHP_EOL;
					?>
							<tr>
								<td><?php echo $row['stationID'];?></td>
								<td><?php echo $row['stationName'];?></td>
								<td><?php echo $row['latitude'].",".$row['longitude'];?></td>
								<td><?php echo $amout_sum;?></td>
								<td><?php echo $diff;?></td>
								<td><?php echo $Rain_avg;?></td>
							</tr>
					<?php		
					  		}
					  		// 拼接
							$csv = $header.$text;
							// 写入并关闭资源
							fwrite($fp, iconv('UTF-8','GB2312',$csv));
							fclose($fp);
							/*$key = basename($filename);
							// Upload a publicly accessible file. The file size and type are determined by the SDK.
							try {
							    $result = $s3->putObject([
							        'Bucket' => $bucketName,
							        'Key'    => $key,
							        'Body'   => fopen($file_Path, 'r'),
							        'ACL'    => 'public-read',
							    ]);
							    //echo $result->get('ObjectURL');
							} catch (Aws\S3\Exception\S3Exception $e) {
							    //echo "There was an error uploading the file.\n";
							    //echo $e->getMessage();
							}

 
*/
					?>
				</table>
			</div>
			<?php
				if(!empty($_GET["plot"])){
					if(!empty($_GET["from"])&&!empty($_GET["to"])){
							$sql = "select * from station";
							$result = mysqli_query($conn,$sql);
			?>	
				<script>
					// Initialize and add the map
					function initMap(){
					  // The location of Uluru
					  var uluru = {lat: -37.7276, lng: 144.907};
					  // The map, centered at Uluru
					 	  var map = new google.maps.Map(
					      document.getElementById('map'), {zoom: 9, center: uluru, mapTypeId:'terrain'});
					  // The marker, positioned at Uluru
					  <?php
					  		while($row = mysqli_fetch_array($result)){
					  			$Rain_sql = "select * from dailyrainfall where stationID = '".$row['stationID']."' and date between '".$from."' and '".$to."'";
					  			
								//$Rain_sql ='select * from dailyrainfall where date between "2018-08-01" and "2018-08-31"';
								$Rain_result = mysqli_query($conn,$Rain_sql);
								$amout_sum = 0;
								while($arr = mysqli_fetch_array($Rain_result)){
										$amout_sum+=$arr['amount'];
								}
								$Rain_avg = $amout_sum/$diff;
								$perc = $diff/$current_mon_days*100;
					  ?>
					  var place = {lat:<?php echo $row['latitude'];?>, lng:<?php echo $row['longitude'];?>};
					  var marker = new google.maps.Marker({
					  	position: place, 
					  	icon:getCircle(<?php if($Rain_avg<1) echo '8';
					  		else if($Rain_avg<2) echo '10';else echo '13';?>,'blue'), 
					  	map: map,
					  	title: '<?php echo $row['stationName'];?>'
					  });
					  
					  //信息窗口
					   var contentString = '<div id="content">'+
					            '<div id="siteNotice">'+
					            '</div>'+
					            '<h1 id="firstHeading" class="firstHeading">'+'<?php echo $row['stationName'];?>'+'</h1>'+
					            '<div id="bodyContent">'
					            +
					            'Location:'+'<?php echo $row['latitude'];?>'+','+'<?php echo $row['longitude'];?><br>'
					            +
					            'Total observed rainfall amount:'+'<?php echo $amout_sum;?>'+'<br>'
					            +
					            'Total observed days:'+'<?php echo $diff.'('.round($perc,2).'%)';?>'+'<br>'
					            +
					            'Average per day:'+'<?php echo round($Rain_avg,2);?>'+'<br>'
					            +'</div>'+
					            '</div>';
							openMarker(marker,contentString);
					    <?php
					    	}
					    ?>

					}
					 
				 </script>
			<?php
				}else{ 
			?>
				<script>alert("format error!")</script>
			<?php
				}
			} 
			?>
			 
	   </div>
	 
	   <!--Temperature 选项卡-->
	   <div class="tab-pane fade" id="Temperature">
	   <?php
			$from = $_GET['from1'];
			$to = $_GET['to1'];
			$sel = $_GET['sel'];
			echo $from." ".$to;
			$current_mon_days = date('t', strtotime($from));
			$diff = diffBetweenTwoDays($from, $to);
			$diff = $diff + 1;
			$filename1 = "Avg_MaxTemperature".$from."-".$to.".csv";
			$filename2 = "Avg_MinTemperature".$from."-".$to.".csv";
	   ?>
	       	<div style="width:100%;">
	        <form action="" method="get" name="form1">
	        	 From:<input type="date" name="from1" value="<?php echo $_GET['from1']?>"> To:<input type="date" name="to1" value="<?php echo $_GET['to1']?>">
	        	 <select name="sel" >
	        	 	<option value="Max">Maximun Temperature</option>
	        	 	<option value="Min">Minimun Temperature</option>
	        	 </select>
	        	 <input type="submit" name="plot1" value="PLOT DATA" class="btn btn-default">
	        </form>
	        </div>
	        <div style="width:100%;">
	        	 Quick jump
	        </div>
			<div style="width:100%">
				 2018 <a href="index.php?from1=2018-01-01&to1=2018-01-31&plot1=PLOT+DATA">January</a> <a href="index.php?from1=2018-02-01&to1=2018-02-28&plot1=PLOT+DATA">February</a> <a href="index.php?from1=2018-03-01&to1=2018-03-31&plot1=PLOT+DATA">March</a> <a href="index.php?from1=2018-04-01&to1=2018-04-30&plot1=PLOT+DATA">April</a> <a href="index.php?from1=2018-05-01&to1=2018-05-31&plot1=PLOT+DATA">May</a> <a href="index.php?from1=2018-06-01&to1=2018-06-30&plot1=PLOT+DATA">June</a> <a href="index.php?from1=2018-07-01&to1=2018-07-31&plot1=PLOT+DATA">July</a> <a href="index.php?from1=2018-08-01&to1=2018-08-31&plot1=PLOT+DATA">August</a>  
			</div>
			<div style="width: 100%;">
				<div id="hide1" style="display:block;">Showing aggregated data for: <?php echo $diff;?> days<a href="#" id="a3">show data table</a></div>
				<div id="show1">Showing aggregated data for: <?php echo $diff;?> days<a href="#" id="a4">hide data table</a>&nbsp;<a href="<?php if(!empty($_GET['plot1'])){
					if($_GET['sel']=="Max"){
						echo $filename1;
					}else{
						echo $filename2;
					}
					}?>">Download table</a>&nbsp;<a href="#">Download Raw table</a></div>
			</div>
			<div id="map1" style="display: block;"></div>
			<div id="table1" style="width: 100%;">
				<table class="table">
					 <tr>
					 	<th width="10%">Station ID</th>
					 	<th width="25%">Station Name</th>
					 	<th width="20%">Location</th>
					 	<th width="11%">Observed Days</th>
					 	<th ><?php
					 		if($_GET['sel']=="Max"){
					 			echo 'Daily Average MaxTemperature';
					 		}else{
					 			echo 'Daily Average MinTemperature';
					 		}
					 	?></th>
					 </tr>
					 <?php
					 		$sql = "select * from station";
							$result = mysqli_query($conn,$sql);
							if(!empty($_GET['plot1'])){
								if($_GET['sel']=="Max"){
									// 处理头部标题
									$header = implode(',', $MaxTemp_csv_header).PHP_EOL;
					 				$fp = fopen($filename1,'w');
								}else{
									$header = implode(',', $MaxTemp_csv_header).PHP_EOL;
					 				$fp = fopen($filename2,'w');
								}
							}
							// 处理内容
							$text = '';
							while($row = mysqli_fetch_array($result)){
					  			$Temp_sql = "select * from dailytemperature where stationID = '".$row['stationID']."' and date between '".$from."' and '".$to."'";
					  			$Temp_result = mysqli_query($conn,$Temp_sql);
								$Temp_sum = 0;
								if($sel=="Max"){
									while($arr = mysqli_fetch_array($Temp_result)){
										$Temp_sum+=$arr['maxTemp'];
									}
								}else{
									while($arr = mysqli_fetch_array($Temp_result)){
										$Temp_sum+=$arr['minTemp'];
									}
								}
								$Temp_avg = $Temp_sum/$diff;
								$perc = $diff/$current_mon_days*100;
								$v_arr = array($row['stationID'],$row['stationName'],$row['latitude'],$diff,$Temp_avg);
								$text .= implode(',',$v_arr) . PHP_EOL;
					?>
							<tr>
								<td><?php echo $row['stationID'];?></td>
								<td><?php echo $row['stationName'];?></td>
								<td><?php echo $row['latitude'].",".$row['longitude'];?></td>
								<td><?php echo $diff;?></td>
								<td><?php echo $Temp_avg;?></td>
							</tr>
					<?php		
					  		}
					  		// 拼接
							$csv = $header.$text;
							// 写入并关闭资源
							fwrite($fp, iconv('UTF-8','GB2312',$csv));
							fclose($fp);
					 ?>
				</table>
			</div>

			<?php
				if(!empty($_GET["plot1"])){
					if(!empty($_GET["from1"])&&!empty($_GET["to1"])){
							$sql = "select * from station";
							$result = mysqli_query($conn,$sql);
			?>	
				<script>
					// Initialize and add the map

					function initMap(){
					  // The location of Uluru
					  var uluru = {lat: -37.7276, lng: 144.907};
					  // The map, centered at Uluru
					 	  var map = new google.maps.Map(
					      document.getElementById('map1'), {zoom: 9, center: uluru, mapTypeId:'terrain'});
					  // The marker, positioned at Uluru
					  <?php
					  		while($row = mysqli_fetch_array($result)){
					  			$Temp_sql = "select * from dailytemperature where stationID = '".$row['stationID']."' and date between '".$from."' and '".$to."'";
					  			$Temp_result = mysqli_query($conn,$Temp_sql);
								$Temp_sum = 0;
								if($sel=="Max"){
									while($arr = mysqli_fetch_array($Temp_result)){
										$Temp_sum+=$arr['maxTemp'];
									}
									$color = '#FF0000';
									$name = "Maximum";
								}else{
									while($arr = mysqli_fetch_array($Temp_result)){
										$Temp_sum+=$arr['minTemp'];
									}
									$color = 'orange';
									$name = "Minimum";
								}
								$Temp_avg = $Temp_sum/$diff;
								$perc = $diff/$current_mon_days*100;
								 
					  ?>
					  var place = {lat:<?php echo $row['latitude'];?>, lng:<?php echo $row['longitude'];?>};
					  var marker = new google.maps.Marker({
					  	position: place, 
					  	icon:getCircle(<?php if($_GET['sel']=="Max") {if($Temp_avg<15) echo '8';
					  		else if($Temp_avg<20) echo '10';else echo '13';}else{if($Temp_avg<5) echo '8';else if($Temp_avg<7) echo '10'; else echo '13';} ?>,'<?php echo $color;?>'), 
					  	map: map,
					  	title: '<?php echo $row['stationName'];?>'
					  });
					  
					  //信息窗口
					   var contentString = '<div id="content">'+
					            '<div id="siteNotice">'+
					            '</div>'+
					            '<h1 id="firstHeading" class="firstHeading">'+'<?php echo $row['stationName'];?>'+'</h1>'+
					            '<div id="bodyContent">'
					            +
					            'Location:'+'<?php echo $row['latitude'];?>'+','+'<?php echo $row['longitude'];?><br>'
					            +
					            'Total observed days:'+'<?php echo $diff.'('.round($perc,2).'%)';?>'+'<br>'
					            +
					            'Average '+'<?php echo $name;?>'+' Temperature:'+'<?php echo round($Temp_avg,2);?>'+'<br>'
					            +'</div>'+
					            '</div>';
							openMarker(marker,contentString);
					    <?php
					    	}
					    ?>

					}
					 
				 </script>
			<?php
				}else{ 
			?>
				<script>alert("format error!")</script>
			<?php
				}
			} 
			?>
	   </div>

	   <!--other 选项卡-->

	   <div class="tab-pane fade" id="other">
	     <div style="width:100%;">
	     <form name="form2" action="" method="get">
	     	<b>Select a weather station in the area of interest</b><br>
	     	年份:<select name="sel_date">
	     		<option value="2010">2010</option>
	     		<option value="2011">2011</option>
	     		<option value="2012">2012</option>
	     		<option value="2013">2013</option>
	     		<option value="2014">2014</option>
	     		<option value="2015">2015</option>
	     		<option value="2016">2016</option>
	     		<option value="2017">2017</option>
	     		<option value="2018">2018</option>
	     	</select>&nbsp;&nbsp;
			地点名:<select name="stationName">
				<?php
	   				$station_sql = 'select * from station';
	   				$res = mysqli_query($conn,$station_sql);
	   				while($op = mysqli_fetch_array($res)){
	   			?>
	   				<option value="<?php echo $op['stationID'];?>"><?php echo $op['stationName'];?></option>
	   			<?php
	   				}
	   			?>
			</select>
	  		<br>
	     	<input class="btn btn-primary" type="submit" name="get" value="Get Data">
		</form>
	     </div>
	     <div id="container" style="min-width:400px;height:400px"></div>
	     <?php
				if(!empty($_GET["get"])){
					$year = $_GET['sel_date'];
					$id = $_GET['stationName'];
					$namesql = "select stationName from station where stationID = '".$id."'";
					$nameres = mysqli_query($conn,$namesql);
					$name = mysqli_fetch_array($nameres);

					print_r($name);
					for($i=1;$i<=12;$i++){
						 $from_date = $year."-".$i."-01";
						 $to_date = $year."-".$i."-".date('t', strtotime($from_date));
						 $Rain_sql = "select * from dailyrainfall where stationID = '".$id."' and date between '".$from_date."' and '".$to_date."'";
						 $Temp_sql = "select * from dailytemperature where stationID = '".$id."' and date between '".$from_date."' and '".$to_date."'";
						 $Rain_result = mysqli_query($conn,$Rain_sql);
						 $Temp_result = mysqli_query($conn,$Temp_sql);
						 $rain_sum = 0;
						 $max_sum = 0;
						 $min_sum = 0;
						 while($ar = mysqli_fetch_array($Rain_result)){
						 		$rain_sum+=$ar['amount'];
						 }
						 while($ar = mysqli_fetch_array($Temp_result)){
						 		$max_sum+=$ar['maxTemp'];
						 		$min_sum+=$ar['minTemp'];
						 }
						 $arr_rain[] = round($rain_sum/date('t', strtotime($from_date)),2);
						 $arr_max[] = round($max_sum/date('t', strtotime($from_date)),2);
						 $arr_min[] = round($min_sum/date('t', strtotime($from_date)),2);
					}
					/*
						print_r($arr_rain);
						print_r($arr_max);
						print_r($arr_min);
					*/ 
		 ?>	
		     <script language="JavaScript">
				var chart = Highcharts.chart('container', {
				chart: {
					zoomType: 'xy'
				},
				title: {
					text: '<b>Average Monthly Temperature and Rainfall in '+'<?php echo $name['stationName'];?> '+'on <?php echo $year; ?><b>'
				},
				xAxis: [{
					categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
								 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					crosshair: true
				}],
				yAxis: [{ // Primary yAxis
					labels: {
						format: '{value} mm',
						style: {
							color: Highcharts.getOptions().colors[0]
						}
					},
					title: {
						text: 'Rainfall',
						style: {
							color: Highcharts.getOptions().colors[0]
						}
					}
				}, { // Secondary yAxis
					title: {
						text: 'MaxTemperature',
						style: {
							color: Highcharts.getOptions().colors[1]
						}
					},
					labels: {
						format: '{value}°C',
						style: {
							color: Highcharts.getOptions().colors[1]
						}
					},
					opposite: true
				},{ // third yAxis
					title: {
						text: 'MinTemperature',
						style: {
							color: Highcharts.getOptions().colors[2]
						}
					},
					labels: {
						format: '{value}°C',
						style: {
							color: Highcharts.getOptions().colors[2]
						}
					},
					opposite: true
				}
					   ],
				tooltip: {
					shared: true
				},
				legend: {
					layout: 'vertical',
					align: 'left',
					x: 120,
					verticalAlign: 'top',
					y: 100,
					floating: true,
					backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
				},
				series: [{
					name: 'Rainfall',
					type: 'column',
					data: [<?php
						$num = count($arr_rain);
						for($i=0;$i<$num;$i++){
							echo $arr_rain[$i];
							if($i!=$num-1) echo ',';
						}
					?>],
					tooltip: {
						valueSuffix: ' mm'
					}
				}, {
					name: 'MaxTemperature',
					type: 'spline',
					yAxis: 1,
					data: [<?php
						$num = count($arr_max);
						for($i=0;$i<$num;$i++){
							echo $arr_max[$i];
							if($i!=$num-1) echo ',';
						}
					?>],
					tooltip: {
						valueSuffix: '°C'
					}
				},{
					name: 'MinTemperature',
					type: 'spline',
					yAxis: 2,
					data: [<?php
						$num = count($arr_min);
						for($i=0;$i<$num;$i++){
							echo $arr_min[$i];
							if($i!=$num-1) echo ',';
						}
					?>],
					tooltip: {
						valueSuffix: '°C'
					}
				}]
			});
			</script>
		<?php
			}
		?>
	   </div>
	</div>
	 
	<script async defer
    src="http://maps.google.cn/maps/api/js?v=3.20&region=cn&language=zh-CN&key=AIzaSyBAJqbpqO_x9RYrprUVQU8HOdj59T0tSSs&callback=initMap">
    </script>	
</body>
</html>