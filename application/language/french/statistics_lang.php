<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['statistics']['title']    = 'Tableau de bord statistiques';
$lang['statistics']['intro']    = 'Graphiques détaillés offrant une vue approfondie des performances de ta station. Les données sont mises à jour chaque nuit.';

// Chart titles
$lang['statistics']['chart_lift_usage_title']       = 'Utilisation maximale des remontées';
$lang['statistics']['chart_revenue_per_lift_title'] = 'Revenu par remontée';
$lang['statistics']['chart_slope_popularity_title'] = 'Pistes les plus populaires';
$lang['statistics']['chart_satisfaction_title']     = 'Satisfaction des clients';
$lang['statistics']['chart_weather_title']          = 'Historique météo';

// New charts
$lang['statistics']['chart_visitor_count_title']    = 'Fréquentation journalière';
$lang['statistics']['chart_visitor_count_desc']     = 'Nombre de visiteurs par jour sur la saison en cours ou les 7 derniers jours.';
$lang['statistics']['chart_revenue_expenses_title'] = 'Revenus vs Dépenses journaliers';
$lang['statistics']['chart_revenue_expenses_desc']  = 'Revenus et dépenses par jour sur la saison en cours ou les 7 derniers jours.';

// Chart descriptions
$lang['statistics']['chart_lift_usage_desc']        = 'Débit passagers effectif de chaque remontée ouverte, ajusté selon l\'état actuel.';
$lang['statistics']['chart_revenue_per_lift_desc']  = 'Contribution estimée aux revenus forfaits et coût opérationnel par remontée selon le débit.';
$lang['statistics']['chart_slope_popularity_desc']  = 'Pistes ouvertes classées par état actuel — un état élevé reflète une utilisation et un entretien intensifs.';
$lang['statistics']['chart_satisfaction_desc']      = 'Réputation de la station dans le temps, reflétant la satisfaction globale des clients.';
$lang['statistics']['chart_weather_desc']           = 'Historique du niveau de neige (cm) sur la saison en cours ou les 7 derniers jours.';

// Labels
$lang['statistics']['lift']               = 'Remontée';
$lang['statistics']['slope']              = 'Piste';
$lang['statistics']['date']               = 'Date';
$lang['statistics']['yesterday']          = 'Hier';
$lang['statistics']['reputation']         = 'Réputation';
$lang['statistics']['effective_throughput'] = 'Débit effectif (personnes/h)';
$lang['statistics']['estimated_revenue']  = 'Revenu estimé (€)';
$lang['statistics']['operating_cost']     = 'Coût opérationnel (€)';
$lang['statistics']['condition']          = 'État (%)';
$lang['statistics']['snow_level']         = 'Niveau de neige';
$lang['statistics']['persons_per_hour']   = 'Personnes / heure';
$lang['statistics']['euros']              = 'Montant (€)';
$lang['statistics']['condition_pct']      = 'État (%)';
$lang['statistics']['snow_level_cm']      = 'Niveau de neige (cm)';
$lang['statistics']['visitors']           = 'Visiteurs';
$lang['statistics']['visitors_label']     = 'Visiteurs (personnes / jour)';
$lang['statistics']['revenue_label']      = 'Revenus (€)';
$lang['statistics']['expenses_label']     = 'Dépenses (€)';
