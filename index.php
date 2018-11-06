<?php

	ob_start();

	require("php/bibli_cuiteur.php");
	require("php/bibli_generale.php");

	$title = "Connectez-vous !";
	$style = "styles/index3.css";
	$bd = bd_connection();
	html_debut($title, $style);

	echo '<header>',
		'<p id="titre">Connectez-vous </p>',
		'<img id="letrait" src="images/trait.png" />',
		'<p id="expli" >Pour vous connecter a Cuiteur, il faut vous identifier :</p>',
	'</header>';

	echo '<aside>',
	'</aside>';

	echo '<div id="connection">';

	if(!isset($_POST['btnValider'])) {
		_aff_form();
	}

	function _aff_form() {

				echo '<form method=POST action="index.php">',
					'<table border=1 cellpadding=5>';
						echo from_ligne("Pseudo", "<input type=text name=txtPseudo size=20 />", "right", "");
						echo from_ligne("Mot de passe", "<input type=password name=txtPasse size=20 />", "right", "");


						echo from_ligne("" , "<input class=lesub type=submit name=btnValider value=Connexion />", "", "right");
					echo  '</table>',
				'</form>';


	}




	if(isset($_POST['btnValider'])) {

		$pseudo = trim($_POST['txtPseudo']);
		$pass = trim($_POST['txtPasse']);



		$S = "SELECT *
			FROM users
			WHERE usPseudo = '$pseudo'
			AND usPasse = '$pass'";


		$R = mysqli_query($bd, $S) or bd_erreur($bd, $S);
		$D = mysqli_fetch_assoc($R);



		$count = mysqli_num_rows($R);


		$erreur = array();

		if($count != 1) {
			$erreur[] = "Probl�me d'authentification";
		}

		if (count($erreur) == 0) {

			session_start();

			$_SESSION['usID'] = $D['usID'];
			$_SESSION['usNom'] = $D['usNom'];
			$_SESSION['usPseudo'] = $D['usPseudo'];
			$_SESSION['usAvecPhoto'] = $D['usAvecPhoto'];

			header ('location: php/cuiteur.php');
			exit();
		}

		else {

				echo '<h3>', $erreur[0], '</h5>';

				$_POST['txtPseudo'] = '';
				$_POST['txtPasse'] = '';

				echo '</br>';
				echo _aff_form();

		}

	}

	echo '</br></br></br><p>Pas encore de compte ? <a href="php/inscription.php">Inscrivez-vous</a> sans plus tarder !</br></br>',

				'Vous h&eacute;sitez &agrave; vous inscrire ? Laissez-vous s&eacute;duire par une </br><a href="html/presentation.html">presentation</a> des possibilit&eacute;s de Cuiteur.</p>';

	echo '</div>';



	aff_footer();
	mysqli_close($bd);
	html_fin() ;


?>
