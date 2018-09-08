<?php
	header("Content-type:text/html;charset=utf-8");
	$conn=mysqli_connect("localhost","root","root","azure") or die("连接数据库失败！".mysqli_error());
	mysqli_query($conn, "set names utf8");
	 
?>
