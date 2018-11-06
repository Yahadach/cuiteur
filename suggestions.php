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

	$sql="SELECT DISTINCT usID, usPseudo, usNom
		FROM users, estabonne
		WHERE usID = eaIDAbonne
		AND eaIDUser <> $id
		AND eaIDAbonne <> $id
		AND eaIDUser = ANY
			(SELECT DISTINCT eaIDAbonne
			FROM estabonne
			WHERE eaIDUser=$id)";
	$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);

	//SI LE BOUTON VALIDER
	if(isset($_POST['btnValider'])) {

		while ($enr = mysqli_fetch_assoc($r)) {
			$abonne_id = htmlentities($enr['usID'],ENT_QUOTES,'ISO-8859-1');
			$abonne_pseudo = htmlentities($enr['usPseudo'],ENT_QUOTES,'ISO-8859-1');
			$abonne_nom = htmlentities($enr['usNom'],ENT_QUOTES,'ISO-8859-1');

			//On regarde si l'id de l'abonnés n'est pas le même que l'utilisateur courant
			if ($abonne_id != $id) {
				//Si la case a été cochée

				if (isset($_POST[$abonne_id])) {

					$deja_abonne = deja_abonne($bd, $abonne_id, $id);


					$dateActuelle = date('Y-m-d');
					if(strcmp($deja_abonne, "S'abonner") == 0) {
						$S = "INSERT INTO estabonne
							(eaIDUser, eaIDAbonne, eaDate)
								VALUES
							('$id','$abonne_id','$dateActuelle')";

						$r = mysqli_query($bd, $S) or bd_erreur($bd, $S);
					}
					else {
						$S = "DELETE FROM estabonne
							WHERE eaIDUser = $id
							AND eaIDAbonne = $abonne_id";

						$r = mysqli_query($bd, $S) or bd_erreur($bd, $S);

					}
				}
			}
		}
		header('location: cuiteur.php');
	}


	$title = "Abonnements";
	$style = "../styles/general.css";

	html_debut($title, $style);



	//Affichage de la partie supérieur
	header_log();

	echo "<p id=titre>Suggestions </p>",
		'<img id="letrait" src="../images/trait.png" />',
	'</header>';


	//Affichage de la partie supérieur
	aff_aside_log($bd, $id, $pseudo, $nom, $photo, 4, 2);


	echo '<div id="abonner">',
		'</br></br>';


		$id_encrypted = encrypt_url($profil_id);
		$pseudo_encrypted = encrypt_url($profil_pseudo);
		$nom_encrypted = encrypt_url($profil_nom);
		echo '<form method="POST" action="abonnes.php?profil=',$id_encrypted,'&amp;pseudo=',$pseudo_encrypted,'&amp;nom=',$nom_encrypted,'">';
		echo '<ul id="bcMessages">';
			while ($enr = mysqli_fetch_assoc($r)) {
					$abonne_id = htmlentities($enr['usID'],ENT_QUOTES,'ISO-8859-1');
					$abonne_pseudo = htmlentities($enr['usPseudo'],ENT_QUOTES,'ISO-8859-1');
					$abonne_nom = htmlentities($enr['usNom'],ENT_QUOTES,'ISO-8859-1');

					$deja_abonne = deja_abonne($bd, $abonne_id, $id);

					$blabla_u = blabla_nb($bd, $abonne_id);
					$mentions_u = mention_nb($bd, $abonne_id);
					$abonnes_u = abonne_nb($bd, $abonne_id);
					$abonnements_u = abonnement_nb($bd, $abonne_id);
					$photo_a = photo($bd, $abonne_id);

						if ($photo_a == 1) {
							echo '<li><img src="../upload/', $abonne_id,'.jpg" class="imgAuteur"> ';
						}
						else {
							echo '<li><img src="../images/anonyme.jpg" class="imgAuteur"> ';
						}
						aff_user($id, htmlentities($enr['usPseudo'],ENT_QUOTES,'ISO-8859-1'), htmlentities($enr['usNom'],ENT_QUOTES,'ISO-8859-1'), "bold");
						echo '</br>';
						aff_lien("blablas", $abonne_id, $abonne_pseudo, $abonne_nom, $blabla_u, "blablas", "-");
						aff_lien("mentions", $abonne_id, $abonne_pseudo, $abonne_nom, $mentions_u, "mentions", "-");
						aff_lien("abonnes", $abonne_id, $abonne_pseudo, $abonne_nom, $abonnes_u, "abonn&eacute;s", "-");
						aff_lien("abonnements", $abonne_id, $abonne_pseudo, $abonne_nom, $abonnements_u, "abonnements");
						echo '</br>';
						if ($abonne_id != $id) {
							echo '<label id="abonneright"><input type="checkbox" name="',$abonne_id,'" value="',$deja_abonne,'" >', $deja_abonne, '</label><br>';
						}
						else {
							echo '<br>';
						}
						echo '</br>';
					echo '</li>';



			}


		echo '</ul>';


			echo "<input style=float:right; class=lesub type=submit name=btnValider value=Valider />";
		echo '</form>';

	echo '</div>';








	aff_footer();
	mysqli_close($bd);
	html_fin();
	ob_end_flush();
?>
