<?php

ob_start();
if(!isset($_SESSION['usID'])) {
		header('location: ../index.php');
		exit();
	}

session_unset();
session_destroy();
header('location: ../index.php');
exit();

echo '</div></body> </html>';

?>
