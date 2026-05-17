<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['competitors']['title']   = 'Stations Concurrentes';
$lang['competitors']['intro']   = 'Des stations de ski concurrentes pilotées par l\'IA rivalisent avec vous pour attirer des touristes chaque jour. '
    . 'Elles mènent leurs propres campagnes marketing, proposent des forfaits moins chers et investissent dans des remontées mécaniques géantes pour détourner les skieurs de votre station. '
    . 'Surveillez leur activité et agissez pour protéger votre part de marché. '
    . 'Utilisez le <strong>Contre-marketing</strong> pour contrecarrer leurs efforts publicitaires, ou '
    . '<strong>Investissez dans une remontée géante</strong> pour rendre votre infrastructure plus attractive que la leur. '
    . 'La pression cumulée de tous les concurrents réduit votre nombre de visiteurs quotidiens du pourcentage indiqué ci-dessous.';

// Penalty banner
$lang['competitors']['current_penalty_label'] = 'Pression concurrentielle actuelle sur votre station :';
$lang['competitors']['current_penalty_desc']  = 'réduction de visiteurs par jour due à la concurrence locale.';
$lang['competitors']['no_penalty']            = 'Votre station ne subit actuellement aucune pression concurrentielle — excellent travail !';

// Table columns
$lang['competitors']['col_name']            = 'Concurrent';
$lang['competitors']['col_reputation']      = 'Réputation';
$lang['competitors']['col_ticket_price']    = 'Prix du forfait';
$lang['competitors']['col_marketing']       = 'Campagne marketing';
$lang['competitors']['col_ticket_discount'] = 'Remise sur forfait';
$lang['competitors']['col_lift_investment'] = 'Niveau remontée géante';
$lang['competitors']['col_actions']         = 'Actions';

// Buttons
$lang['competitors']['btn_counter_marketing']       = 'Contre-campagne';
$lang['competitors']['btn_counter_marketing_title'] = 'Lancer une campagne de contre-marketing pour réduire le niveau publicitaire de ce concurrent';
$lang['competitors']['btn_mega_lift']               = 'Remontée géante';
$lang['competitors']['btn_mega_lift_title']         = 'Investir dans une remontée géante pour surclasser l\'infrastructure de cette station';

// Cost display
$lang['competitors']['cost_label']                   = 'Coût des actions :';
$lang['competitors']['cost_counter_marketing_label'] = 'Campagne de contre-marketing :';
$lang['competitors']['cost_mega_lift_label']         = 'Investissement remontée géante :';

// Success messages
$lang['competitors']['success_counter_marketing'] = 'Campagne de contre-marketing lancée ! Le niveau marketing du concurrent a été réduit.';
$lang['competitors']['success_mega_lift']          = 'Investissement dans une remontée géante réalisé ! L\'avantage en remontées mécaniques du concurrent a été réduit.';

// Error messages
$lang['competitors']['error_not_found']       = 'Concurrent introuvable.';
$lang['competitors']['error_not_enough_cash'] = 'Fonds insuffisants pour effectuer cette action.';
$lang['competitors']['error_action_failed']   = 'L\'action n\'a pas pu être effectuée. Veuillez réessayer.';

// No competitors
$lang['competitors']['no_competitors'] = 'Aucune station concurrente assignée pour le moment. Revenez bientôt !';

// Activity log entries
$lang['competitors']['log_counter_marketing'] = 'Campagne de contre-marketing lancée contre une station concurrente. Coût :';
$lang['competitors']['log_mega_lift']          = 'Investissement dans une remontée géante pour surclasser une station rivale. Coût :';
