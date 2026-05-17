<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['climate_change']['title']                  = 'Changement climatique';
$lang['climate_change']['intro']                  = 'Au fil des années, le changement climatique affecte ta station. Surveille le réchauffement et investis dans des stratégies d\'adaptation pour rester compétitif.';
$lang['climate_change']['climate_change']         = 'Changement climatique';
$lang['climate_change']['climate_level_label']    = 'Niveau climatique';
$lang['climate_change']['current_season_label']   = 'Saison actuelle';

// Level descriptions
$lang['climate_change']['level_desc_0']           = 'Les conditions climatiques sont normales. Aucun impact significatif sur ta station pour l\'instant.';
$lang['climate_change']['level_desc_1']           = 'Légère hausse des températures détectée. Les hivers sont légèrement plus chauds et la saison est raccourcie de 2 jours.';
$lang['climate_change']['level_desc_2']           = 'Réchauffement modéré. Les chutes de neige naturelles diminuent et les coûts d\'enneigement augmentent.';
$lang['climate_change']['level_desc_3']           = 'Stress climatique sévère. Les glaciers reculent, les saisons raccourcissent. L\'investissement dans l\'adaptation est fortement recommandé.';

// Effects
$lang['climate_change']['active_effects']         = 'Effets actifs';
$lang['climate_change']['effect_snow_penalty']    = 'Réduction de la neige naturelle';
$lang['climate_change']['effect_cost_mult']       = 'Multiplicateur de coût d\'enneigement';
$lang['climate_change']['effect_glacier']         = 'Perte glaciaire (dégradation des pistes)';
$lang['climate_change']['effect_season']          = 'Saison plus courte';
$lang['climate_change']['no_effect']              = 'Aucun';

// Adaptation investments
$lang['climate_change']['adaptation_title']       = 'Stratégies d\'adaptation';
$lang['climate_change']['adaptation_intro']       = 'Investis dans les stratégies suivantes pour réduire l\'impact du changement climatique sur ta station.';

$lang['climate_change']['snowmaking_invest_label'] = 'Développer l\'enneigement artificiel';
$lang['climate_change']['snowmaking_invest_desc']  = 'Investis dans une infrastructure d\'enneigement haute efficacité. Réduit de 50 % la pénalité de coût d\'enneigement.';

$lang['climate_change']['altitude_invest_label']   = 'Monter en altitude';
$lang['climate_change']['altitude_invest_desc']    = 'Développe des pistes en haute altitude pour accéder à une neige plus fiable. Réduit de 50 % la pénalité de neige naturelle.';

$lang['climate_change']['diversify_invest_label']  = 'Diversifier les revenus';
$lang['climate_change']['diversify_invest_desc']   = 'Investis dans des activités non liées au ski (tourisme estival, événements, bien-être). Réduit de 50 % l\'impact sur les visiteurs lorsque les effets climatiques sont actifs.';

$lang['climate_change']['cost']                   = 'Coût';
$lang['climate_change']['invest_btn']             = 'Investir';
$lang['climate_change']['invested']               = 'Investi';
$lang['climate_change']['invested_in']            = 'Investi dans';

// Messages
$lang['climate_change']['invest_success']         = 'Investissement réussi ! Ta stratégie d\'adaptation est maintenant active.';
$lang['climate_change']['invest_failed']          = 'L\'investissement a échoué. Veuillez réessayer.';
$lang['climate_change']['already_invested']       = 'Tu as déjà réalisé cet investissement.';
$lang['climate_change']['not_enough_cash']        = 'Tu n\'as pas assez de liquidités pour cet investissement.';
$lang['climate_change']['invalid_invest']         = 'Type d\'investissement invalide.';

// Notification logs
$lang['climate_change']['climate_level_up']       = 'Niveau climatique augmenté à';
$lang['climate_change']['climate_effects_msg']    = 'Le changement climatique affecte ta station. Consulte la page Changement climatique pour plus de détails.';
$lang['climate_change']['season_shorter']         = 'En raison du changement climatique, cette saison est plus courte.';
