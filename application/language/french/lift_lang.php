<?php
//french file
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['lift']['name']		= 'Remontée mécanique';
$lang['lift']['diff_type_column']		= 'Type';
$lang['lift']['length_speed_column']		= 'Vitesse';
$lang['lift']['throughput']		= 'Débit';
$lang['lift']['capacity_seats']		= 'Capacité (places)';
$lang['lift']['speed_unit']		= 'm/s';
$lang['lift']['throughput_unit']		= 'skieurs par heure';
$lang['lift']['build_info']		= 'Information remontée:';
$lang['lift']['build_title']		= 'Construire une nouvelle remontée dans le secteur ';
$lang['lift']['build']		= 'Construire une nouvelle remontée';
$lang['lift']['bad_level']		= '<div class="alert alert-danger text-center">Tu ne peux pas passer à ce niveau.</div>';
$lang['lift']['bad_status']		= '<div class="alert alert-danger text-center">Tu ne peux pas mettre à niveau pendant une maintenance ou pendant la construction.</div>';
$lang['lift']['lift_upgraded']		= '<div class="alert alert-success text-center">La mise à niveau de la remontée a commencé.</div>';
$lang['lift']['lift_built']		= '<div class="alert alert-success text-center">La construction de la remontée a commencé.</div>';
$lang['lift']['lift_edit_successful']		= '<div class="alert alert-success text-center">Remontée éditée avec succès</div>';
$lang['lift']['lift_built_failed']		= '<div class="alert alert-danger text-center">La construction de la remontée n\'a pas pu commencer. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['lift_upgraded_failed']		= '<div class="alert alert-danger text-center">La mise à niveau de la remontée n\'a pas pu commencer. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['repair_failed']		= '<div class="alert alert-danger text-center">La réparation de la remontée n\'a pas pu commencer. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['not_found']		= '<div class="alert alert-danger text-center">La remontée est introuvable. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'</div>';
$lang['lift']['no_deserving_lift']		= 'Il n\'y a pas de remontée desservant cette piste.';
$lang['lift']['and']		= ' et ';
$lang['lift']['no_assigned_skipatrol']		= 'Il n\'y a pas de pisteur affecté à cette piste';
$lang['lift']['no_deserving_mechanic']		= 'Il n\'y a pas de mécanicien affecté à cette remontée';
$lang['lift']['assigned_skipatrol_sing']	= 'Il y a un pisteur affecté à cette piste';
$lang['lift']['deserving_lift_sing']		= 'Il y a un mécanicien affecté à cette remontée';
$lang['lift']['deserving_lift_part1_plur']		= 'Il y a ';
$lang['lift']['deserving_mechanic_sing']		= ' mécanicien affecté à cette remontée';
$lang['lift']['assigned_skipatrol_part2_plur']		= ' pisteurs affecté à cette piste';
$lang['lift']['deserving_lift_part2_plur']		= ' remontées desservant cette piste';
$lang['lift']['deserving_mechanic_part2_plur']		= ' mécaniciens affecté à cette remontée';
$lang['lift']['name_edit']		= 'Nom de la remontée:';
$lang['lift']['name_changed']		= '<div class="alert alert-success text-center">Nom mis à jour.</div>';
$lang['lift']['repaired']		= 'La remontée a été placée en mode maintenance pendant la réparation.';
$lang['lift']['no_mechanics']		= 'Il n\'y a pas de mécanicien affecté à cette remontée. Tu dois affecter un mécanicien pour le réparer.';
$lang['lift']['ongoing_construction_lift']		= '<div class="alert alert-warning text-center">Une autre remontée est en cours de construction ou de réparation. Tu ne peux pas construire plus d\'une remontée à la fois.</div>';
$lang['lift']['out_of_order']		= 'Hors service';
$lang['lift']['out_of_order_text']		= 'La condition de cette remontée est inférieure à 20% ce qui l\'empêche de fonctionner. Assure toi qu\'un mécanicien est affecté à cette remontée et répare la.';
$lang['lift']['repair']		= 'Réparer';
$lang['lift']['no_mechanics_assigned']		= 'Il n\'y a pas de mécanicien affecté à cette remonfortée. Affecte un mécanicien pour le réparer. Ceci est possible depuis la page de vue d\'ensemble du personnel.';
$lang['lift']['confirm_destroy_item_part1']           = 'Veux-tu réparer cette remontée pour ';
$lang['lift']['confirm_destroy_item_part2']           = '€? La maintenance va durer 6 heures. Après la maintenance, la remontée sera automatiquement fermée et tu devras l\'ouvrir à nouveau.';
$lang['lift']['repair_lift']           = 'Réparer la remontée';
$lang['lift']['choose_name']		= 'Choisis un nom pour ta nouvelle remontée';
$lang['lift']['grip_type']		= 'Type de pinces';


$lang['lift']['rush_completed']		= '<div class="alert alert-success text-center">Tu as réussi à accélérer la construction/amélioration de la remontée.</div>';
$lang['lift']['already_completed']		= '<div class="alert alert-warning text-center">La construction/amélioration est déjà terminée.</div>';
$lang['lift']['not_enough_genepis']		= '<div class="alert alert-warning text-center">Tu n\'as pas assez de Génépis pour réaliser cette action.</div>';


$lang['lift']['no_lift_or_patrol_needed_slope_type']		= 'Pas de remontée ou pisteur nécessaire pour ce type de piste.';

$lang['lift']['modular_upgrades_title']  = 'Améliorations modulaires';
$lang['lift']['modular_installed']       = 'Installé';
$lang['lift']['modular_install_btn']     = 'Installer';
$lang['lift']['modular_already_installed'] = 'Déjà installé';
$lang['lift']['modular_not_available']   = 'Indisponible pendant la construction ou la maintenance.';
$lang['lift']['modular_installed_ok']    = '<div class="alert alert-success text-center">Module installé avec succès.</div>';
$lang['lift']['modular_installed_fail']  = '<div class="alert alert-danger text-center">L\'installation du module a échoué. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL.'</div>';
$lang['lift']['modular_already_exists']  = '<div class="alert alert-warning text-center">Ce module est déjà installé sur cette remontée.</div>';
$lang['lift']['modular_not_enough_money'] = '<div class="alert alert-danger text-center">Tu n\'as pas assez d\'argent pour installer ce module.</div>';
$lang['lift']['modular_bad_lift_status'] = '<div class="alert alert-danger text-center">Tu ne peux pas installer un module pendant que la remontée est en construction ou maintenance.</div>';
$lang['lift']['modular_cost']            = 'Coût';
$lang['lift']['modular_speed_bonus']     = 'Bonus de vitesse';
$lang['lift']['modular_throughput_bonus'] = 'Bonus de débit';
$lang['lift']['modular_capacity_bonus']  = 'Bonus de capacité';
$lang['lift']['modular_reputation_bonus'] = 'Bonus de réputation';
$lang['lift']['modular_daily_cost']      = 'Coût journalier supplémentaire';

$lang['lift']['age']                        = 'Âge';
$lang['lift']['age_unit']                   = 'saison(s)';
$lang['lift']['wear']                       = 'Usure';
$lang['lift']['efficiency_penalty']         = 'Baisse d\'efficacité (âge)';
$lang['lift']['maintenance_multiplier']     = 'Facteur coût de maintenance';
$lang['lift']['end_of_life_badge']          = 'Fin de vie';
$lang['lift']['end_of_life_notification']   = ' a atteint la fin de sa vie et doit être remplacée.';
$lang['lift']['end_of_life_notification']   = ' a atteint la fin de sa vie et doit être remplacée.';
$lang['lift']['end_of_life_notification']   = ' a atteint la fin de sa vie et doit être remplacée.';

// Système d'efficacité du réseau de remontées
$lang['lift']['network_title']                 = 'Efficacité du réseau de remontées';
$lang['lift']['network_intro']                 = 'Analysez l\'efficacité avec laquelle votre réseau de remontées transporte les clients. Un mauvais agencement réduit la satisfaction des clients — utilisez ces indicateurs pour optimiser votre implantation.';
$lang['lift']['network_no_lifts']              = 'Aucune remontée n\'a encore été construite. Construisez des remontées pour voir les indicateurs d\'efficacité du réseau.';
$lang['lift']['network_total_lifts']           = 'Remontées construites au total';
$lang['lift']['network_open_lifts']            = 'Remontées ouvertes';

$lang['lift']['transfer_efficiency']           = 'Efficacité de transfert';
$lang['lift']['transfer_efficiency_help']      = 'Part du débit total de remontées actuellement opérationnelle. Une valeur faible signifie que de nombreuses remontées sont fermées, en maintenance ou hors service.';
$lang['lift']['transfer_efficiency_good']      = 'Excellent — la grande majorité de votre capacité de remontée est en service.';
$lang['lift']['transfer_efficiency_warning']   = 'Passable — envisagez d\'ouvrir ou de réparer les remontées inactives.';
$lang['lift']['transfer_efficiency_bad']       = 'Mauvais — trop de remontées sont hors ligne. Les clients ne peuvent pas accéder aux pistes clés, réduisant leur satisfaction.';

$lang['lift']['bottleneck_score']              = 'Score de goulot d\'étranglement';
$lang['lift']['bottleneck_score_help']         = 'Équilibre du débit entre vos différents secteurs. Un score faible signifie qu\'un secteur est nettement moins bien desservi que les autres.';
$lang['lift']['bottleneck_score_good']         = 'Bon équilibre — tous les secteurs sont bien desservis.';
$lang['lift']['bottleneck_score_warning']      = 'Déséquilibre modéré — certains secteurs sont sous-desservis. Envisagez d\'y ajouter de la capacité.';
$lang['lift']['bottleneck_score_bad']          = 'Goulot d\'étranglement sévère — au moins un secteur a un débit bien inférieur aux autres, nuisant au flux des clients.';

$lang['lift']['network_redundancy']            = 'Redondance du réseau';
$lang['lift']['network_redundancy_help']       = 'Part de vos remontées construites qui desservent au moins une piste. Les remontées ne desservant aucune piste sont une infrastructure gaspillée.';
$lang['lift']['network_redundancy_good']       = 'Bien — presque toutes les remontées sont liées à des pistes.';
$lang['lift']['network_redundancy_warning']    = 'Certaines remontées ne sont liées à aucune piste. Assignez des pistes pour maximiser leur valeur.';
$lang['lift']['network_redundancy_bad']        = 'De nombreuses remontées n\'ont aucune piste assignée. Elles ne contribuent pas au flux des clients.';

$lang['lift']['overlap_waste']                 = 'Gaspillage par chevauchement';
$lang['lift']['overlap_waste_help']            = 'Part des assignations piste–remontée qui doublonnent une couverture déjà assurée par une autre remontée. Un chevauchement élevé gaspille des ressources qui pourraient desservir de nouvelles pistes.';
$lang['lift']['overlap_waste_good']            = 'Faible chevauchement — vos remontées couvrent des pistes distinctes de manière efficace.';
$lang['lift']['overlap_waste_warning']         = 'Chevauchement modéré — certaines pistes sont desservies par plusieurs remontées. Vérifiez si c\'est intentionnel.';
$lang['lift']['overlap_waste_bad']             = 'Chevauchement élevé — de nombreuses assignations de pistes sont redondantes. Redistribuez la couverture des remontées vers des zones non explorées.';

$lang['lift']['network_satisfaction_note']     = 'La satisfaction des clients est influencée par la qualité du réseau. Visez une haute efficacité de transfert et un bon score de goulot, une bonne redondance et un gaspillage minimal.';

$lang['lift']['network_score']      = 'Score global du réseau';
$lang['lift']['network_score_help'] = 'Composite pondéré : Efficacité de transfert 30%, Score de goulot 30%, Redondance du réseau 20%, Efficacité de chevauchement 20%. Visez 80% ou plus.';

// Recommandations de capacité
$lang['lift']['network_capacity_title']       = 'Recommandations de capacité';
$lang['lift']['network_capacity_intro']       = 'Remontées ouvertes ne fonctionnant pas à leur capacité maximale. Les améliorer réduit les temps d\'attente et améliore la satisfaction des clients. Les remontées déjà au niveau maximum devraient être envisagées pour un remplacement par un type à plus grande capacité.';
$lang['lift']['network_capacity_none']        = 'Toutes les remontées ouvertes fonctionnent déjà à leur capacité maximale.';
$lang['lift']['network_capacity_current']     = 'Actuel :';
$lang['lift']['network_capacity_riders']      = 'skieurs/h';
$lang['lift']['network_capacity_upgrade_tip'] = 'Capacité max :';
$lang['lift']['network_capacity_upgrade_btn'] = 'Améliorer';
$lang['lift']['network_capacity_replace_tip'] = 'Déjà au niveau maximum — si les files sont longues, envisagez un remplacement par un type de remontée à plus grande capacité.';
