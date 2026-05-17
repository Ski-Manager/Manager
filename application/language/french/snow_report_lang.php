<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// -----------------------------------------------------------------------
// Snow Report page strings (French)
// -----------------------------------------------------------------------

$lang['snow_report']['title']  = 'Bulletin de Neige';
$lang['snow_report']['intro']  = 'Publiez un bulletin d\'enneigement quotidien pour votre station. Un rapport de qualité améliore votre réputation et informe les visiteurs des conditions sur les pistes.';

// Form labels
$lang['snow_report']['publish_heading']  = 'Publier le Bulletin du Jour';
$lang['snow_report']['snow_depth_label'] = 'Épaisseur moyenne de neige (cm)';
$lang['snow_report']['fresh_snow_label'] = 'Neige fraîche en 24 h (cm)';
$lang['snow_report']['conditions_label'] = 'Conditions générales';
$lang['snow_report']['coverage_label']   = 'Ouverture des pistes (%)';
$lang['snow_report']['note_label']       = 'Note pour les visiteurs (facultatif)';
$lang['snow_report']['note_placeholder'] = 'Ex. : Poudreuse ce matin sur la face nord. Tous les remontées opérationnelles.';
$lang['snow_report']['publish_button']   = 'Publier le Bulletin';

// Conditions options
$lang['snow_report']['conditions_poor']      = 'Mauvaises';
$lang['snow_report']['conditions_fair']      = 'Correctes';
$lang['snow_report']['conditions_good']      = 'Bonnes';
$lang['snow_report']['conditions_excellent'] = 'Excellentes';

// Reputation bonuses per tier (for display)
$lang['snow_report']['rep_bonus_poor']      = 'Aucun bonus';
$lang['snow_report']['rep_bonus_fair']      = '+2 réputation';
$lang['snow_report']['rep_bonus_good']      = '+5 réputation';
$lang['snow_report']['rep_bonus_excellent'] = '+10 réputation';

// Status messages
$lang['snow_report']['already_published_today'] = 'Vous avez déjà publié un bulletin aujourd\'hui. Revenez demain !';
$lang['snow_report']['publish_error']           = 'Une erreur s\'est produite lors de l\'enregistrement de votre bulletin. Veuillez réessayer.';
$lang['snow_report']['publish_success_no_bonus']= 'Bulletin de neige publié avec succès !';
$lang['snow_report']['publish_success_bonus']   = 'Bulletin publié ! Votre station a gagné +%d points de réputation.';

// Latest / history section
$lang['snow_report']['latest_heading']   = 'Dernier Bulletin';
$lang['snow_report']['history_heading']  = 'Historique des Bulletins';
$lang['snow_report']['no_reports_yet']   = 'Aucun bulletin publié pour l\'instant. Informez vos visiteurs des conditions actuelles !';
$lang['snow_report']['date_col']         = 'Date';
$lang['snow_report']['depth_col']        = 'Épaisseur (cm)';
$lang['snow_report']['fresh_col']        = 'Fraîche (cm)';
$lang['snow_report']['conditions_col']   = 'Conditions';
$lang['snow_report']['coverage_col']     = 'Ouverture (%)';
$lang['snow_report']['rep_bonus_col']    = 'Bonus réput.';
$lang['snow_report']['note_col']         = 'Note';

// Tips panel
$lang['snow_report']['tips_heading']  = 'Conseils';
$lang['snow_report']['tip_1']         = 'Publier un bulletin chaque jour renforce la confiance des visiteurs et maintient votre station dans l\'actualité.';
$lang['snow_report']['tip_2']         = 'Des conditions excellentes rapportent +10 de réputation à la publication.';
$lang['snow_report']['tip_3']         = 'Un rapport honnête, en accord avec les conditions réelles, améliore la satisfaction des clients sur le long terme.';

// Current game data panel
$lang['snow_report']['game_data_heading']     = 'Données de Jeu Actuelles';
$lang['snow_report']['game_snow_level_label'] = 'Niveau de neige';
$lang['snow_report']['game_fresh_snow_label'] = 'Chutes aujourd\'hui';
$lang['snow_report']['game_temperature_label']= 'Température';
$lang['snow_report']['game_weather_label']    = 'Météo';
$lang['snow_report']['game_data_hint']        = 'Ces valeurs sont pré-remplies depuis les données actuelles de votre station. Vous pouvez les ajuster avant de publier.';

// Activity log
$lang['snow_report']['log_type']      = 'Bulletin de Neige';
