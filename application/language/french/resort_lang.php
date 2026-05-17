<?php
//french file
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['resort']['text']		= 'Bienvenue à ';
$lang['resort']['no_resort']		= '<legend>Tu n\'as pas encore de station, crée-en une.</legend>';
$lang['resort']['choose_name']		= 'Choisis le nom de ta station:';
$lang['resort']['name_field']		= 'Nom';
$lang['resort']['name_field_error']		= 'Nom';
$lang['resort']['difficulty_field']		= 'Difficulté';
$lang['resort']['difficulty_field_error']		= 'Difficulté';
$lang['resort']['resort_country']		= 'Choisis dans quel pays la station sera située:';
$lang['resort']['country_field']		= 'Pays';
$lang['resort']['country_field_error']		= 'Pays';
$lang['resort']['description']		= 'Entre une courte présentation pour ta station:';
$lang['resort']['description_field']		= 'Entre une courte présentation pour ta station';
$lang['resort']['description_field_error']		= 'Description';
$lang['resort']['create']		= 'Créer ma station';
$lang['resort']['update']		= 'Mettre à jour ma station';
$lang['resort']['creation_failed']		= '<div class="alert alert-danger text-center">La station n\'a pas été créée. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'.</div>';
$lang['resort']['creation_successful']		= '<div class="alert alert-success text-center">La station a été créée. Tu peux consulter les détails ci-dessous.<br>Il est maintenant temps de commencer à construire! Pour bien démarrer, il est conseillé de construire un télésiège desservant deux pistes. La prochaine étape consistera à embaucher quelques employés, puis à construire deux hôtels standard. Jète un coup d’œil aux objectifs si tu ne sais pas quoi faire par la suite!</div>';
$lang['resort']['update_failed']		= '<div class="alert alert-danger text-center">La station n\'a pas été mise à jour. Vérifie les champs ci-dessous ou contacte-nous à '.CONST_ADMIN_EMAIL .'.</div>';
$lang['resort']['update_successful']		= '<div class="alert alert-success text-center">La station a été mise à jour. Tu peux consulter les détails ci-dessous.</div>';
$lang['resort']['location_show']		= 'Emplacement: ';
$lang['resort']['description_show']		= 'Description: ';
$lang['resort']['sector_unlocked']		= 'Dévérouillé';
$lang['resort']['locked']		= 'Vérouillé';
$lang['resort']['sector_locked']		= '<span class="alert alert-danger text-center">Ce secteur est verrouillé, tu ne peux donc pas construire cette piste</span>';
$lang['resort']['sector_locked_lift']		= '<span class="alert alert-danger text-center">Ce secteur est verrouillé, tu ne peux donc pas construire cette remontée</span>';
$lang['resort']['bad_action']		= '<div class="alert alert-danger text-center">Cette action ne peut pas être effectuée.</div>';
$lang['resort']['lift_unsellable']		= 'Tu ne peux pas vendre une remontée ou détruire une piste lorsqu\'elle est en maintenance ou en construction.';
$lang['resort']['item_not_sold']		= 'La remontée n\'a pas pu être vendue';
$lang['resort']['item_opened']		= '<div class="alert alert-success text-center">Ouvert avec succès</div>';
$lang['resort']['item_closed']		= '<div class="alert alert-success text-center">Fermé avec succès</div>';

$lang['resort']['sell_tooltip']		= 'Lors de la vente d\'une remontée, seuls 10% de la valeur sont remboursés. Cette action ne peut pas être annulée.';
$lang['resort']['destroy_tooltip']		= 'Lors de la destruction d\'une piste, tu ne récupères pas d’argent mais aucun coût ne sera débité. Cette action ne peut pas être annulée.';
$lang['resort']['destroy_item']		= 'Détruire';
$lang['resort']['confirm_sell_item']           = 'Es-tu sûr de vouloir vendre cette remontée?<br>Tu ne sera crédité que de 10% de sa valeur. Cela comprend le démantèlement et la revente de la remontée.';
$lang['resort']['confirm_destroy_item']           = 'Es-tu sûr de vouloir détruire cette piste?<br>Tu ne récupérera pas d\'argent mais aucun coût ne sera débité.';
$lang['resort']['item_sold']            = 'La remontée a été vendue.';
$lang['resort']['item_destroyed']            = 'La piste a été détruite.';
$lang['alpha_dash_space_resort']    = "Le champ Nom ne peut contenir que des caractères alphanumériques, underscores et des tirets/espaces.";
$lang['resort']['invalid_resort']    = "Le champ Nom contient des caractères non valides.<br>";
$lang['resort']['missing_resort']    = "Tu doit renseigner nom de station.";
$lang['resort']['info_title_loc']		= ' est situé dans le Secteur ';

$lang['resort']['show_sectors']		= 'Montrer les secteurs';
$lang['resort']['map_credits']		= 'Carte de courtoisie de Mapsynergy.';

$lang['resort']['trail_map']		= 'Plan des pistes';
$lang['resort']['click_start_building']		= 'Clique sur le plan des pistes<br>pour commencer à construire!';
$lang['resort']['built_in_sector']		= 'construit dans ce secteur';

// resort map password gate
$lang['resort_map']['password_title']       = 'Plan des pistes – Accès restreint';
$lang['resort_map']['password_intro']       = 'Cette section est actuellement restreinte. Veuillez entrer le mot de passe pour accéder au plan des pistes.';
$lang['resort_map']['password_placeholder'] = 'Entrer le mot de passe…';
$lang['resort_map']['password_submit']      = 'Déverrouiller';
$lang['resort_map']['password_error']       = 'Mot de passe incorrect. Veuillez réessayer.';
$lang['resort_map']['sector_6_building']    = 'Nous construisons actuellement &amp; ajoutons le Secteur 6 — une toute nouvelle zone de la station.';

// resort map
$lang['resort_map']['resort_map_title']		= 'Plan des pistes';
$lang['resort_map']['to_build_lift_title']		= 'Construire une nouvelle remontée mécanique';
$lang['resort_map']['to_build_lift_instructions']		= 'Clique sur le bouton Remontées ci-dessous, choisis le type de remontée, le type de fixation et le nombre de sièges. TU peux ensuite les caractéristiques de base dans le tableau. Une fois que tu es satisfait de ta remontée, place-la sur la carte en cliquant sur une ligne pointillée violette.';
$lang['resort_map']['to_build_slope_title']		= 'Construire une nouvelle piste';
$lang['resort_map']['to_build_slope_instructions']		= 'Clique sur le bouton Pistes ci-dessous, choisis le type de piste, choisis la difficulté et clique ensuite sur une ligne pointillée violette sur la carte pour sélectionner la piste à construire.';
$lang['resort_map']['to_build_title']		= 'Validation';
$lang['resort_map']['to_build_instructions']		= 'Une fois que ta remontée/piste est placée sur la carte, tu peux voir le temps de construction, le coût et la longueur estimée sous la carte.<br>Clique sur Construire pour lancer la construction.';
$lang['resort_map']['to_build_tips_title']		= 'Conseils';
$lang['resort_map']['to_build_tips_instructions']		= 'Tu peux zoomer/dézoomer à l\'aide de la molette de la souris ou des boutons -/+.<br>Tu peux afficher/masquer les secteurs à l\'aide du bouton de couches situé à droite du plan des pistes.';

$lang['resort_map']['selected_segment_id']		= 'ID du segment sélectionné';
$lang['resort_map']['approx_length']		= 'Longueur approximative';
$lang['resort_map']['approx_building_time']		= 'Temps de construction estimé';
$lang['resort_map']['approx_price']		= 'Coût approximatif';
$lang['resort_map']['build_lift_page_title']	= 'Construire une nouvelle remontée mécanique';
$lang['resort_map']['build_slope_page_title']	= 'Construire une nouvelle piste';

$lang['resort']['grooming_requirements']		= 'Besoins de damage';
$lang['resort']['grooming_cap_avail']		= 'Capacité de damage disponible';
$lang['resort']['groomers_available']		= 'Dameuse(s) disponible(s)';
$lang['resort']['required']		= 'requis';
$lang['resort']['available']		= 'disponible';

$lang['resort']['summary_intro']		= '<h4>Le tableau ci-dessous donne un aperçu de tous les bâtiments disponibles dans ta station.<br>Tu peux également voir la capacité totale de chaque type de bâtiment et le nombre maximum de touristes qui peuvent visiter ta station avant que les bâtiments ne soient surpeuplés.</h4>';

//$lang['resort']['slope_type_not_claimed']		= "Bientôt disponible!";
$lang['resort']['slope_type_not_claimed']		= "Collecte la récompense de l\'objectif 'Dévérouille de nouveaux types de pistes' pour débloquer ce type de pistes.";
//$lang['resort']['slope_type_locked']		= "Bientôt disponible!";
$lang['resort']['slope_type_locked']		= "Complète l\'objectif 'Dévérouille de nouveaux types de pistes' pour débloquer ce type de pistes.";
$lang['resort']['welcome_new_user_title'] = 'Bienvenue sur Ski-Manager !';
$lang['resort']['welcome_new_user_body']  = 'Commencez par créer votre station de ski ci-dessous. Si vous êtes nouveau, notre tutoriel pas à pas vous aidera à démarrer rapidement.';

// Altitude & Microclimat
$lang['resort']['altitude_label']       = 'Altitude';
$lang['resort']['altitude_help']        = 'Les stations en haute altitude ont un enneigement plus fiable mais des coûts de construction plus élevés et un risque de vent accru.';
$lang['resort']['altitude_low']         = 'Basse altitude (< 1 000 m)';
$lang['resort']['altitude_medium']      = 'Altitude moyenne (1 000 – 2 000 m)';
$lang['resort']['altitude_high']        = 'Haute altitude (> 2 000 m)';
$lang['resort']['aspect_label']         = 'Exposition des pistes';
$lang['resort']['aspect_help']          = 'Les pistes exposées au sud fondent plus vite ; celles exposées au nord retiennent la neige plus longtemps.';
$lang['resort']['aspect_north']         = 'Exposition nord';
$lang['resort']['aspect_south']         = 'Exposition sud';
$lang['resort']['aspect_east']          = 'Exposition est';
$lang['resort']['aspect_west']          = 'Exposition ouest';
$lang['resort']['microclimate_info']    = 'Microclimat';
$lang['resort']['altitude_build_cost_info'] = 'Multiplicateur de coût de construction lié à l\'altitude';
$lang['resort']['wind_risk_high']       = 'Risque de vent élevé';
$lang['resort']['wind_risk_medium']     = 'Risque de vent modéré';
$lang['resort']['wind_risk_low']        = 'Faible risque de vent';
// Modification du microclimat
$lang['resort']['microclimate_edit_title']       = 'Modifier les paramètres du microclimat';
$lang['resort']['microclimate_first_change_free']= 'Votre première modification est gratuite ! Les modifications suivantes seront de plus en plus coûteuses.';
$lang['resort']['microclimate_change_cost_info'] = 'Cette modification coûtera <strong>%s €</strong>. Votre solde actuel : <strong>%s €</strong>.';
$lang['resort']['microclimate_save_free']        = 'Enregistrer (gratuit)';
$lang['resort']['microclimate_save_cost']        = 'Enregistrer (coûte %s €)';
$lang['resort']['microclimate_update_success']   = '<strong>Paramètres du microclimat mis à jour avec succès.</strong>';
$lang['resort']['microclimate_no_cash']          = 'Vous n\'avez pas assez d\'argent pour modifier les paramètres du microclimat.';
$lang['resort']['microclimate_update_failed']    = 'Les paramètres du microclimat n\'ont pas pu être mis à jour. Veuillez réessayer.';
$lang['resort']['microclimate_edit_via_page']    = 'Pour modifier l\'Altitude ou l\'Exposition des pistes, rendez-vous sur la page <a href="'.base_url('microclimate_controller').'">Microclimat</a>.';
// Système de Legs
$lang['resort']['legacy_rating_label']        = 'Note Historique';
$lang['resort']['legendary_mountain_badge']   = '⭐ Montagne Légendaire';
$lang['resort']['legendary_mountain_desc']    = 'Cette station a atteint le statut de Montagne Légendaire après 20 saisons d\'excellence.';

// Système de Classement par Étoiles
$lang['resort']['star_rating_label']          = 'Classement par Étoiles';
$lang['resort']['star_rating_tooltip']        = 'Votre classement par étoiles est basé sur la réputation de votre station. Gagnez plus de réputation pour débloquer des niveaux d\'étoiles plus élevés.';
// Cartes de statistiques rapides
$lang['resort']['stat_open_slopes']  = 'Pistes ouvertes';
$lang['resort']['stat_open_lifts']   = 'Remontées ouvertes';
$lang['resort']['stat_staff']        = 'Personnel';
$lang['resort']['stat_active_runs']  = 'pistes actives';
$lang['resort']['stat_operating']    = 'en service';
$lang['resort']['stat_hired']        = 'embauchés';

// Partage Social
$lang['resort']['share_resort']      = 'Partager';
$lang['resort']['share_resort_text'] = 'Découvrez ma station de ski "%name%" en %country% sur Ski-Manager ! 🎿 #SkiManager';
