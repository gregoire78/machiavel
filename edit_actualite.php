<?php
session_start();
session_regenerate_id();

//l'auto connexion
include_once('functions/functions_connect.php');
auto_connexion(NULL,'index.php',2);

//fonctions
include_once('accessoires/menu.php');
include_once("functions/functions_events.php");

$table_historique = 2;
$image_event = "images/jeux/defaut_jeu.png";

//l'auto connexion
//auto_connexion(NULL,'index.php',3);

if (isset($_GET['modifier']))
{
	//On vérifie que la personne à le droit de modifier l'événement
	$id_event = $_GET['modifier'];
	
	$query = recup_event_one($id_event);
	if ($data=$query->fetch(PDO::FETCH_ASSOC))
	{
		$title_event = $data['title_event'];
		$text_event = $data['text_event'];
		$date = date("m/d/Y",strtotime($data['date_event']));
		$hour_begin=date("H",strtotime($data['date_event']));
		$minute_begin=date("i",strtotime($data['date_event']));
        $image_event = $data['image_event'];
        $inscription_event = $data['inscription_event'];
        if($data['id_jeu']==0)
        {
            $title_jeu="Autre";
        }
        else {
            $query = recup_jeu($data['id_jeu']);
            $data = $query->fetch(PDO::FETCH_ASSOC);
            $title_jeu = $data['title_jeu'];
        }
	}
}
//Si on a cliqué sur le bouton pour créer un événement
if(isset($_POST['create'])|| isset ($_POST['update'])){
	
	//On enregistre les données dans les variables
	$title_event=$_POST['title_event'];
	$title_jeu=$_POST['title_jeu'];
	$text_event=$_POST['text_event'];
	$date = $_POST['date'];
	$hour_begin = $_POST['hour_begin'];
	$minute_begin =$_POST['minute_begin'];
	$image_event = NULL; //$_POST['image_event'];
    $inscription_event = $_POST['inscription_event'];
	$date_event=date("Y-m-d H:i:s",strtotime($date.' '.$hour_begin.$minute_begin."00"));
	
	$string=35; //taille de la chaine caractères accepté
    /********************** Vérifications pour le titre de l'actu ****************************/
    if(empty($title_event))
    {
        $error_data[1] = "Veuillez saisir une titre";
    }
    else if(mb_strlen(html_entity_decode($title_event),'utf-8') >= $string)
    {
        $error_data[1] = "Le titre ne doit pas dépasser <b>".$string." caractères</b>";
    }
    //vérifions s'il ya des caracteres speciaux dans le champs titre
    else if(preg_match("/[^0-9A-Za-zàâçéèêëîïôûùüÿñæœ.:!?_\' ]/",$title_event))
    {
        $error_data[1] = "Veuillez n'insérer que des lettres ou chiffres dans votre titre.";
    }
    /******************************** Vérifications pour le texte de l'événement **************************/
    if(empty($text_event))
    {
        $error_data[2] = "Veuillez saisir un texte pour l'événement";
    }
    /****************************** Vérifications pour la date de l'événement *****************************/
    if(empty($date))
    {
        $error_data[3] = "Veuillez renseigner la date";
    }
	/***************************************Si il n'y a aucune erreur**************************************/
	if (empty($error_data))
	{
        include_once("functions/functions_historique.php");
		if(isset($_POST['create']))//On crée un événement
		{
			create_event($title_event, $text_event, $date_event, $title_jeu, $image_event, $inscription_event);
            $query = recup_last_event();
            create_historique($table_historique, "Création de l'événement : ".$title_event, $_SESSION['id_user']);
		}
		else if (isset($_POST['update']))//On met à jour un évènement
		{
            $id_event = $_GET['modifier'];
            create_historique($table_historique, "Mis à jour de l'événement : ".$title_event, $_SESSION['id_user']);
			update_event($id_event, $title_event, $text_event, $date_event, $title_jeu, $image_event, $inscription_event);
		}
	//On redirige vers la page d'actualité pour voir le nouvel événement
	header('Location:actualite.php');
	}
}

$query = recup_title_jeu();

//On enregistre les titres des jeux dans un tableau
$i=0;
while ($data=$query->fetch(PDO::FETCH_ASSOC))
{
    $title[$i] = $data['title_jeu'];
    $i++;
}

//On inclu le fichier html pour la mise en forme
include_once("edit_actualite.html");
?>