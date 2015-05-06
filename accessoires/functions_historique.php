<?php
/**
 * Created by PhpStorm.
 * User: Maillard
 * Date: 06/05/2015
 * Time: 14:29
 */

//Fonction pour notifier d'une modification (table historique)
function create_historique($table_historique, $text_historique, $id_user)
{
    require("connect_bdd.php");
    $sql="INSERT INTO historique	  ( id_user,  table_historique,  text_historique, date_historique )
		  VALUES 				      (:id_user, :table_historique, :text_historique, NOW() );";
    $query=$connect->prepare($sql);
    $query->bindParam(':table_historique',$table_historique,PDO::PARAM_INT);
    $query->bindParam(':id_user',$id_user,PDO::PARAM_INT);
    $query->bindParam(':text_historique',$text_historique,PDO::PARAM_STR,100);
    $query->execute();
}

//Fonction pour recupérer l'historique
function recup_historique($restrict, $ordre, $page, $historique_page)
{
    $nb_historique = ($page-1)*$historique_page;
    require("connect_bdd.php");
    $sql="	SELECT id_historique, pseudo, avatars, h.id_user, text_historique, date_historique
			FROM historique h

            JOIN users u
            ON u.id_user = h.id_user

			".$restrict."

			ORDER BY date_historique ".$ordre."

			LIMIT   :nb_historique, :historique_page" ;

    $query=$connect->prepare($sql);
    $query->bindParam(':nb_historique',$nb_historique,PDO::PARAM_INT);
    $query->bindParam(':historique_page',$historique_page,PDO::PARAM_INT);
    $query->execute();
    return $query;
}

//Fonction pour supprimer l'historique à partir de la date actuel
function delete_historique($restrict)
{
    require("connect_bdd.php");
    $sql="  DELETE FROM inscription
            ".$restrict ;

          //WHERE date_historique < fonction d'ajout(SYSDATE, nombre à ajouter )
          //WHERE id_historique IN ( mettre les id_historique)
    $query=$connect->prepare($sql);
    $query->bindParam(':id_event',$id_event,PDO::PARAM_INT);
    $query->execute();
}

function recup_lign_historique($restrict)
{
    require("connect_bdd.php");
    $sql = "SELECT COUNT(*)
			FROM  historique
			".$restrict." ";
    $query=$connect->prepare($sql);
    $query->execute();
    $data=$query->fetchColumn();
    return $data;
}

?>