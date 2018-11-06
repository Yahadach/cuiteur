<?php 

	/**
	 * Affiche une ligne d'un tableau. Cette fonction
	 * a été modifié pour pouvoir afficher les élements
	 * du tableaux centré eu centre.
	 *
	 * @param	string		$colG			String de se qui doit être affiché dans la colonne Gauche
	 * @param	string		$colD			String de se qui doit être affiché dans la colonne Droite
	 * @param	string		$alignL			Choix de l'alignement de la colonne de Gauche
	 * @param	string		$alignR			Choix de l'alignement de la colonne de Droite
	 *
	 * @return 	string						Retourne la ligne du tableau en string.
	 */

	function from_ligne($colG, $colD, $alignL, $alignR) {
		return "<tr><td align=$alignL>$colG</td><td align=$alignR>$colD</td></tr>";
	}

	/**
	 * Fonction qui permet de retourner n'importe quelle
	 * type de input grâce aux paramètres
	 *
	 * @param	string		$type			Type de l'input
	 * @param	string		$nom			Nom de l'input
	 * @param	string		$value			Value de l'input
	 * @param	integer		$taille			Taille du input si celui ci est type text
	 *
	 * @return 	string						Retourne la ligne du tableau en string.
	 */

	function form_input($type, $nom, $value, $taille) {
		return "<input type=$type name=$nom value=$value size=$taille />";
	}

	/**
	 * Fonction qui va pouvoir nous donner le formulaire
	 * pour afficher une date. Avec en plus un nom pour
	 * récuperer l'information avec $_POST par exemple.
	 *
	 * @param	string		$nom			Nom qu'auront les 3 select pour récuperer les informations
	 * @param	string		$jour			Jour donné pour pouvoir mettre par défaut ce jour dans le formulaire
	 * @param	string		$mois			Mois donné pour pouvoir mettre par défaut ce mois dans le formulaire
	 * @param	string		$jour			Année donné pour pouvoir mettre par défaut cette année dans le formulaire
	 *
	 * @return 	string						Retourne la ligne du tableau en string.
	 */

	function form_date($nom, $jour, $mois, $annee) {
		$ma = array('Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre');
		$nomj = $nom."_j";
		$nomm = $nom."_m";
		$noma = $nom."_a";

		$date = date('Y-m-d');
		$curr_year = date("Y");
		$curr_month = date("m");
		$curr_day = date("d");



		$res = "<select name=$nomj>";
				for ($i = 1; $i <= 31; $i++) {
					if ($jour == 0 && $i == $curr_day)
						$res .= '<option value='.$i.' selected="selected">'.$i.'</option>';
					else
						$res .= '<option value='.$i.'>'.$i.'</option>';
				}
		$res .= '</select>';
		$res .= "<select name=$nomm>";
				for ($i = 1; $i <= 12; $i++) {
					if ($mois == 0 && $i == $curr_month)
						$res .= '<option value='.$i.' selected="selected">'.$ma[$i-1].'</option>';
					else
						$res .= '<option value='.$i.'>'.$ma[$i-1].'</option>';
				}
		$res .= '</select>';
		$res .= "<select name=$noma>";
				for ($i = 0; $i < 100; $i++) {
					if ($annee == 0 && $i == $curr_year)
						$res .= '<option value='.($curr_year-$i).'>'.($curr_year-$i).'</option>';
					else
						$res .= '<option value='.($curr_year-$i).'>'.($curr_year-$i).'</option>';
				}
		$res .= '</select>';

		return $res;

	}




?>
