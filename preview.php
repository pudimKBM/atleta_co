<!DOCTYPE html>
<html>
	<head>
		<title>Time Slots Booking Calendar by PHPJabbers.com</title>
		<meta charset="utf-8">
		<meta name="fragment" content="!">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link href="core/framework/libs/pj/css/pj.bootstrap.min.css" type="text/css" rel="stylesheet" />
	    <link href="index.php?controller=pjFrontEnd&action=pjActionLoadCss&cid=<?php echo @$_GET['cid']; ?><?php echo isset($_GET['theme']) && !empty($_GET['theme']) ? '&theme=' . $_GET['theme'] : NULL; ?><?php echo isset($_GET['layout']) && (int) $_GET['layout'] > 0 ? '&layout=' . $_GET['layout'] : NULL; ?><?php echo isset($_GET['switch']) ? '&switch=1' : '&switch=0'; ?><?php echo isset($_GET['multi']) ? '&multi=1' : '&multi=0'; ?>" type="text/css" rel="stylesheet" />
	<head>
	<body>
		<div style="margin: 0 auto; max-width: 630px">
			<script type="text/javascript" src="index.php?controller=pjFrontEnd&action=pjActionLoad&cid=<?php echo @$_GET['cid']; ?><?php echo isset($_GET['theme']) && !empty($_GET['theme']) ? '&theme=' . $_GET['theme'] : NULL; ?><?php echo isset($_GET['layout']) && (int) $_GET['layout'] > 0 ? '&layout=' . $_GET['layout'] : NULL; ?><?php echo isset($_GET['switch']) ? '&switch=1' : '&switch=0'; ?><?php echo isset($_GET['multi']) ? '&multi=1' : '&multi=0'; ?>"></script>
		</div>
	</body>
</html>