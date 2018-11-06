 <?php
	require("bibli_cuiteur.php");
	require("bibli_generale.php");

	$bd = bd_connection();
	date_default_timezone_set('Europe/Paris');
	session_start();

	if(!isset($_SESSION['usID'])) {
		header('location: ../index.php');
		exit();
	}

	$id = $_SESSION['usID'];
	$pseudo =$_SESSION['usPseudo'];
	$nom = $_SESSION['usNom'];
	$photo = $_SESSION['usAvecPhoto'];

	html_debut("Cuiteur","../styles/cuiteur.css");


	//Affichage de la partie supérieur
	header_log();


	$form='<form id="frmPublier" action="cuiteur.php" method="POST">
		<textarea id="txtMessage" name="txtMessage">';
		if (isset($_GET['repondre'])){
			$form.='@'.$_GET['repondre'];
		}
	$form.= '</textarea>
		<input id="btnPublier" type="submit" name="btnPublier" value="" title="Publier mon message">
		<a id="btnPieceJointe" href="../../index.html" title="Ajouter une pi&egrave;ce jointe"></a>
		</form>';
	echo $form;
	echo '</header>';



	if(isset($_POST['btnPublier'])){
		$blTexte=trim($_POST['txtMessage']);
		$len=strlen($blTexte);
		if($len!=0 && $len<=255){

			$tags=tag($blTexte);
			$mentions=dist($blTexte);
			$blTexte=mysqli_real_escape_string($bd, $blTexte);
			$blDate=date('Ymd');
			$blHeure=date('H:i:s');
			$sql = "INSERT INTO blablas
					(blIDAuteur, blDate, blHeure, blTexte)
					VALUES ('$id', '$blDate', '$blHeure', '$blTexte')";
			$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
			$id_blbl=mysqli_insert_id($bd);

			for ($z = 0; $z < count($tags); $z++){
					$taID=mysqli_real_escape_string($bd, substr($tags[$z][0], 1));
					$sql = "INSERT INTO tags
					(taID, taIDBlabla)
					VALUES ('$taID', '$id_blbl')";
					$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
			}

			for ($z = 0; $z < count($mentions); $z++){
					$sql="SELECT usID
						FROM users
						WHERE usPseudo='mentions[$z][0]'";
					$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
					$enr = mysqli_fetch_assoc($r);
					$meIDUser=htmlentities($enr['usID'],ENT_QUOTES,'ISO-8859-1');
					$sql = "INSERT INTO mentions
					(meIDUser, meIDBlabla)
					VALUES ('$meIDUser', '$id_blbl')";

			}
		}
	}


	aff_aside_log($bd, $id, $pseudo, $nom, $photo, 4, 2);


	$sql="SELECT  DISTINCT * FROM
				(SELECT usID, usPseudo, usAvecPhoto, usNom, blTexte, blDate, blHeure, blID
				FROM blablas, users
				WHERE usID='$id'
				AND usID=blIDAuteur

			UNION ALL

				SELECT usID, usPseudo, usAvecPhoto, usNom, blTexte, blDate, blHeure, blID
				FROM blablas, users, estabonne
				WHERE eaIDUser='$id'
				AND blIDAuteur=eaIDAbonne
				AND usID=blIDAuteur
				AND blAvecCible=0

			UNION ALL

				SELECT usID, usPseudo, usNom, usAvecPhoto, blTexte, blDate, blHeure, blID
				FROM blablas, users, mentions
				WHERE meIDUser='$id'
				AND blID=meIDBlabla
				AND usID=blIDAuteur) results
				ORDER BY blID DESC";

		$rep=1;
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$nb_blablas=4;

	if(isset($_GET['supprimer'])){
		$bl_id=$_GET['supprimer'];
		$sql="DELETE FROM blablas
			WHERE blID='$bl_id'";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		header('location: cuiteur.php');
		exit();

	}

	if(isset($_GET['recuiter'])){
		$bl_id=$_GET['recuiter'];
		$sql="SELECT blTexte
			FROM blablas
			WHERE blID='$bl_id'";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$texte=$enr['blTexte'];
		$blDate=date('Ymd');
		$blHeure=date('H:i:s');
		$sql = "INSERT INTO blablas
			(blIDAuteur, blDate, blHeure, blTexte, blIDOriginal)
			VALUES ('$id', '$blDate', '$blHeure', '$texte', '$bl_id')";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);

		header('location: cuiteur.php');
		exit();

	}

	if (isset($_GET['plusBlbl'])){
		$nb_blablas=$_GET['plusBlbl'];
	}

	if($rep!=0){
			$aff_plus_blbl=aff_blablas($r, $nb_blablas, $id, $bd);
			if($aff_plus_blbl ===1){
				echo '<li id="bcPlus">
					<a href=?plusBlbl=',$nb_blablas+4,'><strong>Plus de blablas</strong></a>
				</li>';
			}
		echo '</ul>';
	}
	/*end*/
	aff_footer();
	mysqli_close($bd);
	html_fin();
	ob_end_flush();
?>
