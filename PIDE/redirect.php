<?php
session_start();
if(!isset($_SESSION['1'])) {
	header("Location: SISPIDE.php");
	die();
}else if(!isset($_POST['page_name'])){
	header("Location: SISPIDE.php");
	die();
}
?>
<head>
	<style>
		#loading {
			background: url('loadgif4.gif') no-repeat center center;
			background-size: 60px 60px;
			position: absolute;
			top: 0;
			left: 0;
			height: 100%;
			width: 100%;
			z-index: 9999999;
		}
	</style>	
</head>
<body onload="load()">
	<form action="http://www.psi.gob.pe/PIDE/1" method="post" style="display: none;" name="integral">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="http://www.psi.gob.pe/PIDE/2" method="post" style="display: none;" name="sunat">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="http://www.psi.gob.pe/PIDE/3" method="post" style="display: none;" name="reniec">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="http://www.psi.gob.pe/PIDE/4" method="post" style="display: none;" name="sunarp">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="temp.php" method="post" style="display: none;" name="osce">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="http://www.psi.gob.pe/PIDE/6" method="post" style="display: none;" name="sunedu">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="http://www.psi.gob.pe/PIDE/7" method="post" style="display: none;" name="policiales">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="http://www.psi.gob.pe/PIDE/8" method="post" style="display: none;" name="judiciales">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="http://www.psi.gob.pe/PIDE/9" method="post" style="display: none;" name="penales">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<form action="temp.php" method="post" style="display: none;" name="tipocambio">
		<input type="text" name="user" value="<?php echo $_SESSION['user'];?>">
		<input type="text" name="name" value="<?php echo $_SESSION['name'];?>">
		<input type="text" name="dni" value="<?php echo $_SESSION['dni'];?>">
	</form>
	<div id="loading">
	</div>
</body>
<script>
	function load(){
		document.<?= $_POST['page_name']?>.submit()
	}
</script>