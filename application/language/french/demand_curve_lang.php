<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['demand_curve']['title']                  = 'Courbe de demande dynamique';
$lang['demand_curve']['intro']                  = 'Le nombre de visiteurs dans ta station chaque jour est influencé par plusieurs facteurs. Comprendre cette courbe de demande te permet de fixer le bon prix du forfait pendant les périodes de pointe pour maximiser tes revenus.';

$lang['demand_curve']['current_factors']        = 'Facteurs de demande d\'aujourd\'hui';
$lang['demand_curve']['factor']                 = 'Facteur';
$lang['demand_curve']['value']                  = 'Valeur actuelle';
$lang['demand_curve']['multiplier']             = 'Multiplicateur';
$lang['demand_curve']['effect']                 = 'Effet sur les visiteurs';

$lang['demand_curve']['weather']                = 'Météo';
$lang['demand_curve']['reputation']             = 'Réputation';
$lang['demand_curve']['peak_season']            = 'Saison / Vacances';
$lang['demand_curve']['price']                  = 'Prix du forfait';
$lang['demand_curve']['competition']            = 'Concurrence';

$lang['demand_curve']['day_of_season']          = 'Jour';
$lang['demand_curve']['unknown']                = 'Inconnu';

$lang['demand_curve']['price_in_slope_calc']    = 'Intégré';
$lang['demand_curve']['price_tooltip']          = 'Le coefficient de prix est appliqué directement dans le calcul des visiteurs des pistes. Des prix plus bas attirent plus de visiteurs ; des prix plus élevés réduisent la demande.';
$lang['demand_curve']['coming_soon']            = 'Bientôt disponible';
$lang['demand_curve']['competition_tooltip']    = 'Les stations concurrentes réduiront ta part de visiteurs. Ce facteur sera actif une fois le système de stations concurrentes activé.';

$lang['demand_curve']['combined_multiplier']    = 'Multiplicateur de demande combiné (météo × réputation × saison)';
$lang['demand_curve']['combined_note']          = 'Le coefficient de prix et le plafond d\'affluence sont appliqués séparément dans les calculs des pistes.';

$lang['demand_curve']['daily']                  = 'Journalier';
$lang['demand_curve']['weekly']                 = 'Hebdomadaire';

// Calendrier de la haute saison
$lang['demand_curve']['season_schedule']        = 'Calendrier de demande de la haute saison';
$lang['demand_curve']['season_schedule_intro']  = 'La demande des visiteurs suit une courbe réaliste pour une station de ski sur les 135 jours de la saison. Planifie les prix de ton forfait en fonction de ces périodes pour maximiser tes revenus.';

$lang['demand_curve']['period']                 = 'Période';
$lang['demand_curve']['days']                   = 'Jours';
$lang['demand_curve']['status']                 = 'Statut';
$lang['demand_curve']['current']                = 'Actuel';

$lang['demand_curve']['season_opening']         = 'Ouverture de la saison';
$lang['demand_curve']['shoulder_period']        = 'Période creuse';
$lang['demand_curve']['christmas_peak']         = 'Pic de Noël / Nouvel An';
$lang['demand_curve']['post_christmas']         = 'Creux post-Noël';
$lang['demand_curve']['feb_holidays']           = 'Vacances scolaires de février';
$lang['demand_curve']['late_season']            = 'Fin de saison';
$lang['demand_curve']['season_closing']         = 'Fermeture de la saison';

$lang['demand_curve']['pricing_tip']            = '💡 Conseil : Augmente ton prix de forfait pendant le pic de Noël (jours 41–50) et les vacances scolaires de février (jours 61–90) pour maximiser tes revenus lorsque la demande est élevée.';
