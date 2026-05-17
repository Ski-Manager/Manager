<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['visitor_needs']['page_title']            = 'Besoins des Visiteurs';
$lang['visitor_needs']['page_intro']            = 'Chaque nuit, le système évalue dans quelle mesure votre station satisfait les quatre besoins fondamentaux des visiteurs. Des scores plus élevés attirent plus de visiteurs et augmentent les revenus.';

$lang['visitor_needs']['hunger_title']          = 'Faim';
$lang['visitor_needs']['hunger_desc']           = 'Dans quelle mesure vos visiteurs se sentent rassasiés. Construisez et développez les bâtiments <strong>restaurant</strong> pour améliorer ce score.';

$lang['visitor_needs']['fatigue_title']         = 'Fatigue';
$lang['visitor_needs']['fatigue_desc']          = 'Dans quelle mesure vos visiteurs se sentent reposés. Les bâtiments <strong>médicaux</strong> et <strong>hôteliers</strong> réduisent la fatigue et améliorent ce score.';

$lang['visitor_needs']['warmth_title']          = 'Chaleur';
$lang['visitor_needs']['warmth_desc']           = 'Dans quelle mesure vos visiteurs se sentent au chaud. Un temps froid réduit ce score. Les bâtiments <strong>luxe</strong> (lounges chauffés, spas) contrebalancent le froid.';

$lang['visitor_needs']['fun_title']             = 'Niveau de Plaisir';
$lang['visitor_needs']['fun_desc']              = 'Dans quelle mesure vos visiteurs s\'amusent. Les bâtiments <strong>loisirs</strong> et les pistes ouvertes augmentent le plaisir.';

$lang['visitor_needs']['overall_title']         = 'Satisfaction Globale';
$lang['visitor_needs']['overall_desc']          = 'Moyenne pondérée égale des quatre besoins. Cela affecte directement votre multiplicateur de revenus journaliers.';

$lang['visitor_needs']['revenue_label']         = 'Multiplicateur de revenus lié à la satisfaction des visiteurs';
$lang['visitor_needs']['last_updated']          = 'Les scores sont actualisés chaque nuit lors de l\'exécution nocturne.';
$lang['visitor_needs']['no_data']               = 'Pas encore de données sur les besoins des visiteurs. Les scores sont calculés chaque nuit. Ouvrez votre station pour voir les résultats ici.';

$lang['visitor_needs']['score_excellent']       = 'Excellent';
$lang['visitor_needs']['score_good']            = 'Bon';
$lang['visitor_needs']['score_average']         = 'Moyen';
$lang['visitor_needs']['score_poor']            = 'Mauvais';

$lang['visitor_needs']['tip_hunger']            = 'Conseil : Chaque restaurant ajoute jusqu\'à +10 points à la satisfaction alimentaire.';
$lang['visitor_needs']['tip_fatigue']           = 'Conseil : Les bâtiments médicaux ajoutent +10 points ; les hôtels ajoutent +5 par bâtiment.';
$lang['visitor_needs']['tip_warmth']            = 'Conseil : Le froid (en dessous de 0°C) enlève 3 points par °C. Les bâtiments luxe ajoutent +8 points chacun.';
$lang['visitor_needs']['tip_fun']               = 'Conseil : Chaque bâtiment loisirs ajoute +10 points ; chaque piste ouverte ajoute +5 points.';
