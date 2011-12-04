<?php

include 'config.php';

/*
* Useful variables
*/
$type = $_POST['type'];
$id = $_POST['id'];

/*
* Postgres query
*/
if( ($_POST['checkbox'] == 'true') && ($_SESSION['identitystring'] != '') ) // si l'on a choisi d'appliquer le filtre � l'affichage des roches, et que le filtre est bien renseign�
    $filter = " AND UPPER(figure.identity) LIKE UPPER('".$_SESSION['identitystring']."') ";
else // else nothing is filtered
    $filter = '';

if ($type == 'r') {   // rock analysis
    $query_fig = "
        SELECT figure_number, face, identity, alternative_identity, '/photos/Z' || zone_number || '/G' || group_number || '/R' || rock_number || '/Fig' || figure_number || '.jpg' AS image".'
        FROM zone, "group", rock, figure
        WHERE figure.rock_id = '.$id.'
        AND rock.rock_id = '.$id.'
        AND rock.group_id = "group".group_id
        AND "group".zone_id = zone.zone_id'.$filter.'
        ORDER BY face, figure_number';
} elseif ($type == 'z') {   // zone analysis
    $query_fig = "
        SELECT group_number, rock_number, figure_number, face, identity, alternative_identity, '/photos/Z' || zone_number || '/G' || group_number || '/R' || rock_number || '/Fig' || figure_number || '.jpg' AS image".'
        FROM zone, "group", rock, figure
        WHERE figure.rock_id = rock.rock_id
        AND rock.group_id = "group".group_id
        AND "group".zone_id = '.$id.'
        AND zone.zone_id = '.$id.$filter.'
        ORDER BY group_number, rock_number, face, figure_number';

}

$result = pg_query($sessionpg, $query_fig);

/*
* JSON response
*/
$data = pg_fetch_all($result);
echo json_encode($data);

?>