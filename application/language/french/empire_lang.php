<?php
// French language file for Multi-Mountain Ownership (Empire)
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['empire']['title']                    = 'Empire de stations';
$lang['empire']['intro']                    = 'Développez votre empire du ski en acquérant des stations subsidiaires. Chaque propriété génère des revenus passifs quotidiens et amplifie votre portée marketing au sein du groupe.';

// Subsidiary type names
$lang['empire']['nearby_resort_name']       = 'Station voisine plus petite';
$lang['empire']['glacier_resort_name']      = 'Station glacier en haute altitude';
$lang['empire']['budget_ski_hill_name']     = 'Domaine skiable économique';

// Subsidiary type descriptions
$lang['empire']['nearby_resort_desc']       = 'Une petite station de ski proche de votre établissement principal. Partage vos campagnes marketing et bénéficie de la proximité de votre clientèle existante.';
$lang['empire']['glacier_resort_desc']      = 'Une station glacier exclusive en haute altitude, ouverte toute l\'année. Renforce considérablement le prestige de votre marque et votre portée marketing.';
$lang['empire']['budget_ski_hill_desc']     = 'Un domaine ski abordable ciblant les débutants et les voyageurs à petit budget. Faibles coûts d\'exploitation avec des revenus passifs réguliers.';

// Purchase form
$lang['empire']['purchase_title']           = 'Acquérir une nouvelle propriété';
$lang['empire']['choose_type']              = 'Type de propriété';
$lang['empire']['choose_name']              = 'Nommer votre nouvelle propriété';
$lang['empire']['name_placeholder']         = 'ex. Pics Alpins';
$lang['empire']['btn_purchase']             = 'Acquérir la propriété';
$lang['empire']['purchase_price']           = 'Coût d\'acquisition';
$lang['empire']['daily_revenue']            = 'Revenus quotidiens';
$lang['empire']['marketing_bonus']          = 'Bonus marketing';

// Owned subsidiaries table
$lang['empire']['owned_title']              = 'Vos propriétés';
$lang['empire']['col_name']                 = 'Nom';
$lang['empire']['col_type']                 = 'Type';
$lang['empire']['col_daily_revenue']        = 'Revenu quotidien (€)';
$lang['empire']['col_marketing_bonus']      = 'Bonus marketing';
$lang['empire']['col_purchased_at']         = 'Acquis le';
$lang['empire']['no_subsidiaries']          = 'Vous ne possédez pas encore de station subsidiaire. Acquérez votre première propriété ci-dessous !';

// Empire stats panel
$lang['empire']['stats_title']              = 'Vue d\'ensemble de l\'empire';
$lang['empire']['stats_total_properties']   = 'Propriétés détenues';
$lang['empire']['stats_total_daily_rev']    = 'Revenu quotidien combiné';
$lang['empire']['stats_marketing_bonus']    = 'Bonus marketing combiné';

// Shared finances panel
$lang['empire']['finances_title']           = 'Finances partagées';
$lang['empire']['finances_intro']           = 'Les chiffres ci-dessous montrent le revenu passif combiné généré par toutes vos propriétés subsidiaires. Ces revenus sont ajoutés au trésor de votre station principale chaque jour de jeu.';
$lang['empire']['main_resort']              = 'Station principale';

// Shared marketing panel
$lang['empire']['marketing_title']          = 'Marketing partagé';
$lang['empire']['marketing_intro']          = 'Posséder des stations subsidiaires augmente l\'efficacité de vos campagnes marketing. Le bonus combiné ci-dessous s\'applique en plus de vos retours marketing standards.';

// Feedback messages
$lang['empire']['purchase_success']         = '<div class="alert alert-success text-center">Propriété acquise avec succès ! Elle commencera à générer des revenus dès demain.</div>';
$lang['empire']['purchase_failed']          = '<div class="alert alert-danger text-center">L\'acquisition a échoué. Veuillez réessayer ou nous contacter à contact@ski-manager.net.</div>';
$lang['empire']['purchase_insufficient_funds'] = '<div class="alert alert-danger text-center">Vous n\'avez pas suffisamment de fonds pour acquérir cette propriété.</div>';
$lang['empire']['purchase_invalid_type']    = '<div class="alert alert-danger text-center">Type de propriété sélectionné invalide.</div>';
$lang['empire']['purchase_invalid_name']    = '<div class="alert alert-danger text-center">Veuillez saisir un nom valide pour la propriété (lettres, chiffres, espaces et tirets uniquement).</div>';
$lang['empire']['no_resort']               = '<div class="alert alert-warning text-center">Vous devez créer une station avant de gérer votre empire.</div>';
