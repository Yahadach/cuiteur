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

	$tend='';
	if(isset($_GET['tend'])) {
		$tend = $_GET['tend'];
	}




	$title = "Tendances";
	$style = "../styles/general.css";

	html_debut($title, $style);


	//Affichage de la partie supérieur
	header_log();

	if ($tend == '')
		echo "<p id=titre>Top 10 </p>";
	else
		echo "<p id=titre>$tend </p>";
		echo '<img id="letrait" src="../images/trait.png" />',

	'</header>';

	//Affichage de la partie supérieur
	aff_aside_log($bd, $id, $pseudo, $nom, $photo, 4, 2);




		if ($tend == '') {
			echo '<div id="tendances">';
				echo '<h3>Top 10 du jour</h3>',
				_top10("DAY");

				echo '<h3>Top 10 de le semaine</h3>',
				_top10("WEEK");

				echo '<h3>Top 10 du mois</h3>',
				_top10("MONTH");

				echo '<h3>Top 10 de l\'ann&eacute;e</h3>',
				_top10("YEAR");
			echo '</div>';
		}
		else {
			$sql="SELECT usID, usPseudo, usNom, usAvecPhoto, blTexte, blDate, blHeure, blID
					FROM blablas, users, tags
					WHERE taID = '$tend'
					AND taIDBlabla = blID
					AND blIDAuteur = usID";

			$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);

			$nb_blablas=4;
			if (isset($_GET['plusBlbl'])){
				$nb_blablas=$_GET['plusBlbl'];
			}
			$aff_plus_blbl=aff_blablas($r, $nb_blablas, $id, $bd);
			if($aff_plus_blbl===1){
				echo '<li id="bcPlus">
					<a href="tendances.php?tend=', $tend,'&amp;plusBlbl=',$nb_blablas+4,'" title="Plus de blablas"><strong>Plus de blablas</strong></a>
				</li>';
			}
			echo '</ul>';
		}




	/**
	* Fonction qui va afficher les tops 10
	* des tendances
	*
	*
	* @param 	string		$date		Sur quelle date on veut faire la requête (DAY || WEEK || MONTH || YEAR)
	* @global 	resource	$bd			onnecteur sur la bd ouverte
	*
	*/


	function _top10 ($date) {
		global $bd;


		echo '<ol>';
		//REQUETE POUR AVOIR LE TOP 10 EN FONCTION DE LA DATE.
		$sql="SELECT taID, count(taID) as nombre
				FROM tags, blablas
				WHERE taIDBlabla = blID
				AND $date(blDate) = $date(CURRENT_DATE)
				GROUP BY taID
				ORDER BY nombre desc
				limit 10";

		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$num = mysqli_num_rows($r);

		//SI IL N'A AUCUN RESULTAT
		if ($num == 0) {
			echo '<p>Aucune tendances...</p>';
		}
		//SINON ON AFFICHE LES RESULTATS EN LIEN
		else {
			while ($enr = mysqli_fetch_assoc($r)) {
				$tag = htmlentities($enr['taID'],ENT_QUOTES,'ISO-8859-1');
				$nombre = htmlentities($enr['nombre'],ENT_QUOTES,'ISO-8859-1');
				echo '<li><a href=tendances.php?tend=', $tag, ' title="Voir">', $tag ,' (', $nombre,')',' </a></li>';
			}
		}
		echo '</ol>';

	}

	aff_footer();
	mysqli_close($bd);
	html_fin();
	ob_end_flush();

?>
