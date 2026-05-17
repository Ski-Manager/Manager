<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['data_dashboard']['title']    = 'Tableau de Bord de Données';
$lang['data_dashboard']['intro']    = 'Données de gestion en temps réel pour piloter ta station comme un pro. Les graphiques sont basés sur les données de la saison en cours.';

// Password gate
$lang['data_dashboard']['password_title']       = 'Tableau de Bord de Données – Accès Restreint';
$lang['data_dashboard']['password_intro']       = 'Cette section est protégée. Veuillez entrer le mot de passe secret pour continuer.';
$lang['data_dashboard']['password_placeholder'] = 'Entrez le mot de passe…';
$lang['data_dashboard']['password_submit']      = 'Déverrouiller';
$lang['data_dashboard']['password_error']       = 'Mot de passe incorrect. Veuillez réessayer.';

// Traffic Heatmap
$lang['data_dashboard']['chart_traffic_heatmap_title'] = 'Carte de Chaleur du Trafic';
$lang['data_dashboard']['chart_traffic_heatmap_desc']  = 'Intensité relative du trafic sur tes remontées et pistes. Les barres les plus hautes indiquent une utilisation plus intensive — planifie les améliorations et le déploiement du personnel en conséquence.';
$lang['data_dashboard']['traffic_zone']                = 'Zone';
$lang['data_dashboard']['traffic_intensity']           = 'Intensité du trafic (%)';

// Profit Breakdown
$lang['data_dashboard']['chart_profit_breakdown_title'] = 'Répartition des Bénéfices';
$lang['data_dashboard']['chart_profit_breakdown_desc']  = 'Revenus d\'hier répartis par source. Identifie tes flux de revenus les plus forts et les plus faibles.';
$lang['data_dashboard']['profit_source']               = 'Source de revenus';
$lang['data_dashboard']['profit_amount']               = 'Montant (€)';
$lang['data_dashboard']['src_skipass']                 = 'Forfaits ski';
$lang['data_dashboard']['src_restaurant']              = 'Restaurants';
$lang['data_dashboard']['src_hotel']                   = 'Hôtels';
$lang['data_dashboard']['src_rental']                  = 'Location de ski';
$lang['data_dashboard']['src_leisure']                 = 'Loisirs';
$lang['data_dashboard']['src_luxury']                  = 'Luxe';
$lang['data_dashboard']['src_medical']                 = 'Médical';
$lang['data_dashboard']['src_skibus']                  = 'Navettes ski';
$lang['data_dashboard']['src_instructor']              = 'Moniteurs';
$lang['data_dashboard']['src_parking']                 = 'Parking';
$lang['data_dashboard']['src_other']                   = 'Autre';

// Visitor Segmentation
$lang['data_dashboard']['chart_visitor_segmentation_title'] = 'Segmentation des Visiteurs';
$lang['data_dashboard']['chart_visitor_segmentation_desc']  = 'Répartition des visiteurs journaliers par préférence de difficulté de piste. Comprends la composition de tes clients pour adapter tes offres.';
$lang['data_dashboard']['seg_green']                        = 'Débutant (Vert)';
$lang['data_dashboard']['seg_blue']                         = 'Intermédiaire (Bleu)';
$lang['data_dashboard']['seg_red']                          = 'Avancé (Rouge)';
$lang['data_dashboard']['seg_black']                        = 'Expert (Noir)';
$lang['data_dashboard']['seg_label']                        = 'Catégorie de difficulté';
$lang['data_dashboard']['visitors_label']                   = 'Visiteurs';

// Cartes KPI
$lang['data_dashboard']['kpi_cash']         = '💰 Cash';
$lang['data_dashboard']['kpi_reputation']   = '⭐ Réputation';
$lang['data_dashboard']['kpi_snow_level']   = '🏔 Enneigement';
$lang['data_dashboard']['kpi_visitors']     = '👥 Visiteurs';
$lang['data_dashboard']['kpi_slopes']       = '🎿 Pistes';
$lang['data_dashboard']['kpi_lifts']        = '🚡 Remontées';
$lang['data_dashboard']['kpi_per_day']      = 'par jour';
$lang['data_dashboard']['kpi_open']         = 'ouvert(es)';
$lang['data_dashboard']['kpi_active_runs']  = 'pistes actives';
$lang['data_dashboard']['kpi_operating']    = 'en service';
$lang['data_dashboard']['last_updated']     = 'Dernière mise à jour';

// Accident Probability
$lang['data_dashboard']['chart_accident_probability_title'] = 'Indicateurs de Probabilité d\'Accident';
$lang['data_dashboard']['chart_accident_probability_desc']  = 'Risque d\'accident estimé par piste ouverte (0–100). Le risque est déterminé par la difficulté de la piste et son état actuel — ferme ou prépare rapidement les pistes à risque élevé.';
$lang['data_dashboard']['slope_label']                      = 'Piste';
$lang['data_dashboard']['risk_score']                       = 'Score de risque (0–100)';
$lang['data_dashboard']['risk_low']                         = 'Faible';
$lang['data_dashboard']['risk_medium']                      = 'Moyen';
$lang['data_dashboard']['risk_high']                        = 'Élevé';
