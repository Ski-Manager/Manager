<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

// Common to all equipments
$lang['common_equipment']['titleMain']		= 'Équipement';
$lang['common_equipment']['buy']                   = 'Acheter';
$lang['common_equipment']['delivering']                   = 'Livraison en cours';
$lang['common_equipment']['equipment_name']                   = 'Nom de l\'équipement';
$lang['common_equipment']['equipment_cost']                   = 'Prix de l\'équipement';
$lang['common_equipment']['sell_equip_tooltip']           = 'Tu peux vendre cet équipement pour la moitié de son prix';
$lang['common_equipment']['confirm_sell_equip']           = 'Es-tu sûr de vouloir vendre cet équipement? Tu obtiendra 50% de sa valeur.';
$lang['common_equipment']['equipment_sold']           = '<div class=\"alert alert-success text-center\">Équipement vendu.</div>';
$lang['common_equipment']['equipment_purchased']           = '<div class="alert alert-success text-center">Équipement acheté.</div>';
$lang['common_equipment']['equipment_not_purchased']           = '<div class="alert alert-danger text-center">L\'équipement n\'a pas pu être acheté.</div>';
$lang['common_equipment']['equipment_not_sold']           = 'L\'équipement n\'a pas pu être vendu.';
$lang['common_equipment']['equipment_not_sold_once_at_a_time']           = '<div class="alert alert-danger text-center">L\'équipement n\'a pas été vendu. Tu ne peux vendre un équipement que s\'il n\'y a pas de livraison ou d\'amélioration en cours.</div>';
$lang['common_equipment']['equipment_upgraded']              = '<div class="alert alert-success text-center">L\'équipement a été amélioré.</div>';
$lang['common_equipment']['equipment_not_upgraded']		= '<div class=\"alert alert-danger text-center\">L\'équipement n\'a pas pu être amélioré.</div>';
$lang['common_equipment']['equipment_one_at_a_time']	= '<div class="alert alert-danger text-center">TU ne peux commander qu\'un seul équipement à la fois. Réessaie lorsque l\'équipement actuel est livré.</div>';
$lang['common_equipment']['equipment_not_built_previous']    = '<div class="alert alert-danger text-center">Tu ne peux pas améliorer l\'équipement avant d\'avoir acheté le niveau précédent!</div>';
$lang['common_equipment']['assigned_to']		= 'affecté au secteur';
$lang['common_equipment']['equipment_sold']            = 'L\'équipement a été vendu.';
$lang['common_equipment']['come_back_delivered']            = 'L\'équipement n\'est pas encore livré. Reviens l\'affecter une fois qu\'il est livré.';

$lang['groomer']['title']		= 'Dameuse';
$lang['groomers']		= 'Dameuses';
$lang['groomer']['intro']		= 'Informations concernant vos dameuses.';
$lang['groomer']['desc']		= 'Une dameuse t\'aide à préparer les pistes pendant la nuit. Le damage se fait automatiquement dès qu\'un mécanicien est assigné au damage et que la dameuse est affectée à un secteur.';
$lang['groomer']['capacity_text']		= 'a une capacité de damage de';
$lang['groomer']['coverage']		= 'Couverture (nombre de pistes)';
$lang['groomer']['assigned_mechanic']		= 'Mécanicien assigné';
$lang['groomer']['no_mechanic']		= 'Aucun mécanicien assigné';
$lang['groomer']['status_operational']		= '<span class="badge bg-success">Opérationnelle</span>';
$lang['groomer']['status_no_mechanic']		= '<span class="badge bg-warning text-dark">Sans mécanicien</span>';
$lang['groomer']['status_no_sector']		= '<span class="badge bg-warning text-dark">Sans secteur</span>';
$lang['groomer']['status_idle']		= '<span class="badge bg-danger">Inactive</span>';
$lang['groomer']['summary_title']		= 'État des dameuses';
$lang['groomer']['summary_total']		= 'dameuse(s) livrée(s)';
$lang['groomer']['summary_operational']		= 'opérationnelle(s) (mécanicien &amp; secteur assignés)';
$lang['groomer']['intensity_label']		= 'Intensité du damage';
$lang['groomer']['intensity_light']		= 'Légère (−25% qualité &amp; coût)';
$lang['groomer']['intensity_standard']		= 'Standard';
$lang['groomer']['intensity_intensive']		= 'Intensive (+50% qualité &amp; coût)';
$lang['groomer']['coverage_analysis']		= 'Analyse de couverture par secteur';
$lang['groomer']['coverage_slopes']		= 'Pistes ouvertes';
$lang['groomer']['coverage_capacity']		= 'Capacité de damage';
$lang['groomer']['coverage_ratio']		= 'Couverture';
$lang['groomer']['coverage_sufficient']		= 'Suffisante';
$lang['groomer']['coverage_insufficient']	= 'Insuffisante';
$lang['groomer']['coverage_no_slopes']		= 'Aucune piste';
$lang['groomer']['coverage_avg_condition']	= 'Cond. moy. des pistes';
$lang['groomer']['active_label']		= 'Damage nocturne';
$lang['groomer']['active_yes']			= 'Actif';
$lang['groomer']['active_standby']		= 'En veille (économise le coût)';
$lang['groomer']['set_all_intensity_label']	= 'Appliquer à toutes les dameuses :';
$lang['groomer']['set_all_intensity_btn']	= 'Appliquer à toutes';
$lang['groomer']['set_all_intensity_ok']	= 'Toutes les dameuses mises à jour.';
$lang['groomer']['set_all_intensity_fail']	= 'Impossible de mettre à jour les dameuses.';

// Skibus
$lang['skibus']['title']		= 'Navette de ski';
$lang['skibuss']		= 'Navettes de ski';
$lang['skibus']['intro']		= 'Informations concernant vos navettes de ski.';
$lang['skibus']['desc']		= 'Les navettes de ski sont un service de transport qui aidera tes visiteurs à se déplacer dans ta station. Chaque navette nécessite un chauffeur et doit être affectée à un secteur.';
$lang['skibus']['capacity_text']		= 'a une capacité de';
$lang['skibus']['coverage']		= 'Capacité (nombre de passagers)';
$lang['skibus']['tourists']		= 'touristes';
$lang['skibus']['assigned_driver']		= 'Chauffeur assigné';
$lang['skibus']['no_driver']		= 'Aucun chauffeur assigné';
$lang['skibus']['status_operational']		= '<span class="badge bg-success">Opérationnelle</span>';
$lang['skibus']['status_no_driver']		= '<span class="badge bg-warning text-dark">Sans chauffeur</span>';
$lang['skibus']['status_no_sector']		= '<span class="badge bg-warning text-dark">Sans secteur</span>';
$lang['skibus']['status_idle']		= '<span class="badge bg-danger">Inactive</span>';
$lang['skibus']['summary_title']		= 'État des navettes de ski';
$lang['skibus']['summary_total']		= 'navette(s) livrée(s)';
$lang['skibus']['summary_operational']		= 'opérationnelle(s) (chauffeur &amp; secteur assignés)';


$lang['common_equipment']['rush_completed']		= '<div class="alert alert-success text-center">Tu as réussi à accélérer la livraison de cet équipement.</div>';
$lang['common_equipment']['already_completed']		= '<div class="alert alert-warning text-center">La livraison est déjà terminée.</div>';
$lang['common_equipment']['not_enough_genepis']		= '<div class="alert alert-warning text-center">Tu n\'as pas assez de Génépis pour réaliser cette action.</div>';
