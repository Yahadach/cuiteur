<?php

	ob_start();

	require("bibli_cuiteur.php");
	require("bibli_generale.php");

	$bd = bd_connection();

	session_start();

	if(!isset($_SESSION['usID'])) {
		header('location: ../index.php');
		exit();
	}

	$id = $_SESSION['usID'];
	$pseudo = $_SESSION['usPseudo'];
	$nom = $_SESSION['usNom'];
	$photo = $_SESSION['usAvecPhoto'];

	$jour = 0;
	$mois = 0;
	$annee = 0;





	$profil_id = decrypt_url($_GET['profil']);
	$profil_pseudo = decrypt_url($_GET['pseudo']);
	$profil_nom = decrypt_url($_GET['nom']);


	if(isset($_POST['btnValider'])) {
		$dateActuelle = date('Ymd');
		if(strcmp(deja_abonne($bd, $profil_id, $id), "S'abonner") == 0) {
			$S = "INSERT INTO estabonne
				(eaIDUser, eaIDAbonne, eaDate)
					VALUES
				('$id','$profil_id','$dateActuelle')";

			$r = mysqli_query($bd, $S) or bd_erreur($bd, $S);
		}
		else {
			$S = "DELETE FROM estabonne
				WHERE eaIDUser = $id
				AND eaIDAbonne = $profil_id";

			$r = mysqli_query($bd, $S) or bd_erreur($bd, $S);

		}


		header('location: cuiteur.php');
	}

	$title = "Utilisateur";
	$style = "../styles/general.css";

	html_debut($title, $style);



	$blabla = blabla_nb($bd, $profil_id);
	$mentions = mention_nb($bd, $profil_id);
	$abonnes = abonne_nb($bd, $profil_id);
	$abonnements = abonnement_nb($bd, $profil_id);
	$photo_u = photo($bd, $profil_id);





	//Affichage de la partie supérieur
	header_log();

	echo "<p id=titre>Profile de $profil_pseudo </p>",
		'<img id="letrait" src="../images/trait.png" />';
		if ($photo_u == 1) {
			echo '<p id="profil"><img style="margin-top:-5px;" src="../upload/', $profil_id, '.jpg" class="imgAuteur">', aff_user($profil_id, $profil_pseudo, $profil_nom, "");
		}
		else {
			echo '<p id="profil"><img style="margin-top:-5px;" src="../images/anonyme.jpg" class="imgAuteur">', aff_user($profil_id, $profil_pseudo, $profil_nom, "");
		}
		echo '</br>';
			aff_lien("blablas", $profil_id, $profil_pseudo, $profil_nom, $blabla, "blablas", "-");
			aff_lien("mentions", $profil_id, $profil_pseudo, $profil_nom, $mentions, "mentions", "-");
			aff_lien("abonnes", $profil_id, $profil_pseudo, $profil_nom, $abonnes, "abonn&eacute;s", "-");
			aff_lien("abonnements", $profil_id, $profil_pseudo, $profil_nom, $abonnements, "abonnements");
		echo '</p> ',
	'</header>';


	//Affichage de la partie supérieur
	aff_aside_log($bd, $id, $pseudo, $nom, $photo, 4, 2);


	echo '<div id="abonner">',
		'</br></br></br>';

		//REQUETE VILLE
		$sql="SELECT usVille
			FROM users
			WHERE usID=$profil_id";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$ville = htmlentities($enr['usVille'],ENT_QUOTES,'ISO-8859-1');

		//REQUETE SITE
		$sql="SELECT usWeb
			FROM users
			WHERE usID=$profil_id";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$web = htmlentities($enr['usWeb'],ENT_QUOTES,'ISO-8859-1');

		//REQUETE DATE DE NAISSANCE
		$sql="SELECT usDateNaissance
			FROM users
			WHERE usID=$profil_id";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$date = htmlentities($enr['usDateNaissance'],ENT_QUOTES,'ISO-8859-1');


		$date = amj_clair($date);
		echo "<table style=padding-left:20px;>";
			if ($ville == '')
				echo from_ligne('<p style="font-weight:bold;"> Ville de R&eacute;sidence : </p>', "Non renseign&eacute;", "right", "");
			else
				echo from_ligne('<p style="font-weight:bold;"> Ville de R&eacute;sidence : </p>', $ville, "right", "");

			if ($web == '')
				echo from_ligne('<p style="font-weight:bold;"> Site web : </p>', "Non renseign&eacute;", "right", "");
			else
				echo from_ligne('<p style="font-weight:bold;"> Site web : </p>', $web, "right", "");

			if ($date == 0)
				echo from_ligne('<p style="font-weight:bold;"> Date de naissance : </p>', "Non renseign&eacute;" , "right", "");
			else {

				echo from_ligne('<p style="font-weight:bold;"> Date de naissance : </p>', $date , "right", "");
			}
		echo "</table></br></br>";

		if ($id != $profil_id) {

			//Si l'utilisateur est déjà abonné ou pas
			$val = deja_abonne($bd, $profil_id, $id);

			$id_encrypted = encrypt_url($profil_id);
			$pseudo_encrypted = encrypt_url($profil_pseudo);
			$nom_encrypted = encrypt_url($profil_nom);

			echo '<form method="POST" action="utilisateur.php?profil=',$id_encrypted,'&amp;pseudo=',$pseudo_encrypted,'&amp;nom=',$nom_encrypted,'">';
				echo "<input style=float:right; class=lesub type=submit name=btnValider value=$val />";
			echo '</form>';
		}




	echo '</div>';


	aff_footer();
	mysqli_close($bd);
	html_fin();
	ob_end_flush();

?>
