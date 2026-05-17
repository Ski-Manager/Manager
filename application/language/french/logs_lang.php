<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// top
$lang['logs']['title']	= 'Activité';
$lang['logs']['intro']	= 'Aperçu des derniers événements dans ta station.';
$lang['logs']['type']	= 'Type';
$lang['logs']['datetime']	= 'Date';
$lang['logs']['resort_opened']	= 'Tu as ouvert ta station.';
$lang['logs']['resort_closed']	= 'Tu as fermé ta station.';
$lang['logs']['account_created']	= 'Ton compte a été créé.';
$lang['logs']['account_finalized']	= 'Tu as finalisé l\'inscription avec ton compte Facebook.';
$lang['logs']['account_merged']	= 'Tu as fusionné ton compte avec ton compte Facebook.';
$lang['logs']['account_merged_failed']	= 'Nous n\'avons pas pu fusionner ton compte avec ton compte Facebook. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'</div>';
$lang['logs']['account_activated']	= 'Tu as activé ton compte.';
$lang['logs']['resort_created']	= 'Tu as créé ta station ou édité ses détails.';
$lang['logs']['construction_of']	= 'La construction de ';
$lang['logs']['upgrade_of']	= 'La mise à niveu de ';
$lang['logs']['repair_of']	= 'La réparation de ';
$lang['logs']['has_started']	= ' a commencé.';
$lang['logs']['is_completed']	= ' est terminée.';
$lang['logs']['ordered']	= ' a été commandé.';
$lang['logs']['sold']	= ' a été vendu.';
$lang['logs']['delivered']	= ' a été livré.';
$lang['logs']['put_in_maintenance']	= ' a été mis en mode maintenance à cause de son mauvais état. Répare la remontée à partir de la page Remontées mécaniques et assigne un mécanicien pour qu\'elle reste en bon état au futur.';
$lang['logs']['was_recruited']	= ' a été embauché.';
$lang['logs']['was_fired']	= ' a été renvoyé.';
$lang['logs']['destroyed']	= ' a été détruite.';
$lang['logs']['login_season']	= 'Saison ';
$lang['logs']['todays_weather']	= 'La météo du jour est ';
$lang['logs']['increased']	= 'va augmenter de ';
$lang['logs']['decreased']	= 'va diminuer de ';
$lang['logs']['no_change']	= 'n\'a pas changé.';
$lang['logs']['snow_level']	= 'Le niveau de neige dans la station ';
$lang['logs']['taken_salary']	= 'ont été débités pour payer le salaire de vos employés aujourd\'hui.';
$lang['logs']['taken_loan']	= 'ont été débités pour payer ton prêt en cours.';
$lang['logs']['subscribed_loan_1']	= 'Tu as souscrit à un ';
$lang['logs']['subscribed_loan_2']	= 'prêt avec la ';
$lang['logs']['subscribed_loan_3']	= 'Tu vas rembourser';
$lang['logs']['subscribed_loan_4']	= 'pour les prochains';
$lang['logs']['payoff_loan_1']	= 'Tu as remboursé ton prêt à une date anticipée. Le coût de';
$lang['logs']['payoff_loan_2']	= 'a été retiré de ton compte.';
$lang['logs']['visitors_today']	= ' touristes ont visité ta station aujourd\'hui et le revenue généré est de ';
$lang['logs']['no_lost_injuries']	= 'Aucun blessé dan ta station aujourd\'hui. Réputation non changée.';
$lang['logs']['your_resort_lost']	= 'Ta station a perdu ';
$lang['logs']['reputation_points']	= ' points de réputation ';
$lang['logs']['due_to']                 = 'en raison de ';    // no space before
$lang['logs']['your_resort_earned']     = 'Ta station a gagné ';
$lang['logs']['thanks_to']              = 'grâce à ';
$lang['logs']['injuries_today']                 = ' blessures aujourd\'hui.';
$lang['logs']['subscribed_forecast1']	= 'Tu as souscrit à un bulletin météo à 14 jours pour les 60 prochains jours. '.COST_EXT_FORECAST.' Génépis ont été débité de ton compte.';
$lang['logs']['snow_reset']	= 'La quantité de neige dans ta station a été réinitialisée à '.START_SNOW.'cm pour la nouvelle saison.';
$lang['logs']['cannon_added_total']	= 'Tes canons à neige ont ajouté un total de';
$lang['logs']['cm_of_snow']	= 'cm de neige';
$lang['logs']['night_skiing_enabled']	= 'Le ski nocturne a été activé.';
$lang['logs']['night_skiing_disabled']	= 'Le ski nocturne a été désactivé.';
$lang['logs']['night_skiing_revenue']	= 'Revenus bonus du ski nocturne : ';
$lang['logs']['night_skiing_electricity_cost']	= 'coût électricité : ';
$lang['logs']['energy_solar_panel_bought']    = 'Un panneau solaire a été acheté.';
$lang['logs']['energy_solar_panel_sold']      = 'Un panneau solaire a été vendu.';
$lang['logs']['energy_hydro_built']           = 'La centrale hydroélectrique a été construite.';
$lang['logs']['energy_hydro_demolished']      = 'La centrale hydroélectrique a été démolie.';
$lang['logs']['energy_grid_cost']             = 'Coût électricité réseau (remontées & canons) : ';


// Skipass price
$lang['logs']['daily_type']	= 'Le prix du forfait journée ';
$lang['logs']['weekly_type']	= 'Le prix du forfait semaine ';
$lang['logs']['not_opened']	= 'Il n\'y a pas de piste ouverte, donc aucun forfait n\'a été vendu.';
$lang['logs']['very_expensive']	= 'semble très cher pour la taille de ta station. La fréquentation a été affectée négativement.';
$lang['logs']['expensive']	= 'semble assez cher pour la taille de ta station. La fréquentation a été affectée négativement.';
$lang['logs']['adequate']	= 'est adapté à la taille de ta station. La fréquentation n\'a pas été affectée.';
$lang['logs']['cheap']	= 'est très attractif et donne un bonus très élevé (pas nécessairement le revenu le plus élevé).';
$lang['logs']['very_cheap']	= 'est très attractif et donne le bonus maximum (pas nécessairement le revenu le plus élevé).';

// Types
$lang['logs']['revenues']	= 'Revenues';
$lang['logs']['salaries']	= 'Salaires';
$lang['logs']['progress']	= 'Progrès';
$lang['logs']['weather']	= 'Météo';
$lang['logs']['building']	= 'Bâtiment';
$lang['logs']['lift']           = 'Remontée';
$lang['logs']['slope']          = 'Piste';
$lang['logs']['equipment']	= 'Équipment';
$lang['logs']['staff']          = 'Employés';
$lang['logs']['resort']         = 'Station';
$lang['logs']['injuries']         = 'Blessures';
$lang['logs']['skipasses']         = 'Forfaits';
$lang['logs']['loan']         = 'Prêt';
$lang['logs']['taxes']         = 'Taxes';
$lang['logs']['gift']         = 'Cadeau';
$lang['logs']['genepis']         = 'Génepis';
$lang['logs']['marketing']         = 'Marketing';
$lang['logs']['analysis']         = 'Analyse';
$lang['logs']['tournaments']         = 'Tournois';

$lang['logs']['level']         = 'niveau ';
$lang['logs']['published']         = 'publiée';
$lang['logs']['your']         = 'Ton';
$lang['logs']['fully_reimbursed']         = 'prêt est maintenant entièrement remboursé';
$lang['logs']['genepis_credited']         = 'Génépis ont été crédités sur ton compte.';



$lang['logs']['resort_safely_closed']         = 'Ta station a été fermée pour des raisons de sécurité en raison de mauvaises conditions météorologiques. Elle sera rouverte automatiquement demain matin. Ceci est un des bénéfices \'avoir souscrit aux prévisions météorologiques à 14 jours.';
$lang['logs']['resort_safely_opened']         = 'Ta station a été rouverte après les mauvaises conditions météorologiques. Ceci est un des bénéfices \'avoir souscrit aux prévisions météorologiques à 14 jours.';
$lang['logs']['resort_not_safely_closed']         = 'De graves blessures ont eu lieu dans ta station à cause des mauvaises conditions météorologiques. Par de telles conditions, il est recommandé de fermer la station pour la journée, et ce, avant 23h30. Tu peux également opter pour une prévision météorologique étendue et bénéficier d\'une fermeture automatique en cas de mauvais temps.';

$lang['logs']['set_to_vacation_mode']         = 'Ton compte a été mis en mode vacances suite 14 jours d\'inactivité. Cela signifie que tout progrès est interrompu et que ta station restera fermée jusqu\'à ce que tu te connecte à nouveau.';
$lang['logs']['disabled_vacation_mode']         = 'Le mode vacances a été désactivé suite à ta nouvelle connexion.';

$lang['logs']['received_gift']         = 'Tu as reçu un cadeau de';
$lang['logs']['has_received_gift']         = 'a reçu un cadeau de';

$lang['logs']['received_bonus1']         = 'Tu as reçu un bonus de ';
$lang['logs']['received_bonus2']         = ' € pour a nouvelle saison';
$lang['logs']['has_received_bonus']      = ' a reçu un bonus de ';

$lang['logs']['seasonal_objectives_reward']      = 'Récompenses des objectifs saisonniers créditées : ';
$lang['logs']['seasonal_objectives_prestige']    = ' prestige, ';
$lang['logs']['seasonal_objectives_cash']        = ' €, ';
$lang['logs']['seasonal_objectives_genepis']     = ' Génépis.';

$lang['logs']['referral_confirmed']         = 'Un de vos amis invité à confirmé son compte. Tu viens d\'être récompensé de 30 Génépis.';


$lang['logs']['you_have_purchased']         = 'Tu as finalisé l\'achat de';
$lang['logs']['has_purchased']         = 'a finalisé l\'achat de';

$lang['logs']['max_number_referrals']         = 'Tu as atteint la limite de génépis octroyés lors de l\'invitation d\'amis.';

$lang['logs']['your_report']         = 'Ton rapport du ';
$lang['logs']['is_ready']         = 'est disponible sur la page <a href="'.base_url().'reporting_controller">d\'analyse de la station</a>.';

$lang['logs']['taxes_accounted']         = 'Un montant de';
$lang['logs']['of_yesterdays_revenues']         = 'a été payé en taxes en raison des revenues de la veille';

// tournaments
$lang['logs']['visitors_tournament']         = 'visiteurs ont assisté à l\'événement d\'hier de';
$lang['logs']['generating_revenue_of']         = 'généré un revenue de';
$lang['logs']['is_over_generating']         = 'est terminé et a généré un chiffre d\'affaires total de';
$lang['logs']['over_the']         = 'Sur les';
$lang['logs']['days_comma']         = 'jours,';
$lang['logs']['day_comma']         = 'jour,';
$lang['logs']['visitors_attended']         = 'visiteurs ont assisté au tournoi.';
$lang['logs']['revenue_added']         = 'Les revenus ont été ajoutés à ton solde de trésorerie. Tu as également gagné';
$lang['logs']['prestige_points']         = 'points de prestige';

$lang['logs']['your_visitors_spent']         = 'Tes visiteurs ont dépensé un montant additionel de';
$lang['logs']['thanks_to_prestige']         = 'grâce au bonus accordé par le prestige de ta station.';

$lang['logs']['legacy_rating_earned']        = 'Ta station a obtenu une note historique de ';
$lang['logs']['legendary_mountain_unlocked'] = 'Ta station a atteint le statut de Montagne Légendaire ! Un bonus en argent a été réservé pour ta prochaine station.';
$lang['logs']['legendary_mountain_bonus_applied'] = 'a été ajouté à ton capital de départ en tant que bonus Legs de ta précédente station Légendaire.';

// special events
$lang['logs']['special_events']              = 'Événements spéciaux';
$lang['logs']['visitors_special_event']      = 'visiteurs ont assisté à l\'événement spécial d\'hier de';
$lang['logs']['visitors_attended_event']     = 'visiteurs ont assisté à l\'événement spécial.';
$lang['logs']['revenue_added_event']         = 'Les revenus ont été ajoutés à ton solde de trésorerie. Tu as également gagné';
$lang['logs']['reputation_points_log']       = 'points de réputation';

$lang['logs']['slopes_too_crowded']         = 'Ils y a trop de skieurs sur tes slopes. Le coefficient de skieurs pour les pistes standardes est';
$lang['logs']['and_the_one_crosscountry']         = 'et celui pour les piste de ski de fond est';
$lang['logs']['try_to_keep_below_1000']         = 'Essaye de garder ces coefficients en dessous de 1000 pour éviter l\'impact négatif sur le nombre de touristes.<br>Il est possible de faire cela en construisant plus de pistes et de remontées mécaniques et en évitant de construire plus d\'hôtels ou d\'améliorer l\'accès à ta station. Tu peux également essayer d\'augmenter le prix du forfait.';

// climate change
$lang['logs']['climate']                          = 'Climat';
$lang['logs']['climate_level_increased']          = 'Le niveau climatique a augmenté à';
$lang['logs']['climate_effects_active']           = 'Le changement climatique affecte désormais ta station. Consulte la page Changement climatique.';
$lang['logs']['climate_snow_reduced']             = 'Les chutes de neige naturelles sont réduites par le changement climatique.';
// Lift line management
$lang['logs']['lift_queue_wait']         = 'longues files d\'attente aux remontées mécaniques';
// Système de gestion de la fréquentation
$lang['logs']['crowding']                = 'Fréquentation';
$lang['logs']['crowding_overcrowded']    = 'surpopulation de la station';
$lang['logs']['crowding_rep_bonus']      = 'Votre station a gagné de la réputation pour une gestion exemplaire de la fréquentation (entrée programmée active).';
// Système environnemental
$lang['logs']['environment']                    = 'Environnement';
$lang['logs']['env_pollution_fine']             = 'Amende environnementale appliquée pour pollution élevée :';
$lang['logs']['env_expansion_restricted']       = 'Expansion de la station restreinte en raison d\'une empreinte carbone excessive.';
$lang['logs']['env_expansion_restored']         = 'Les restrictions d\'expansion ont été levées. L\'empreinte carbone est revenue sous le seuil limite.';
$lang['logs']['env_noise_fine']                 = 'Amende pour pollution sonore appliquée près de la zone naturelle :';

// Risques naturels
$lang['logs']['natural_hazards']                = 'Risque naturel';
$lang['logs']['avalanche_slope']                = 'Une avalanche a touché la piste ';
$lang['logs']['avalanche_condition_drop']       = '. La condition de la piste a chuté fortement. Vérifie la page des pistes pour évaluer les dégâts.';
$lang['logs']['storm_lift_damage']              = 'La tempête a endommagé la remontée ';
$lang['logs']['storm_lift_condition_drop']      = '. La condition de la remontée s\'est dégradée. Inspecte et répare si nécessaire.';
$lang['logs']['ice_accumulation']               = 'La glace s\'est accumulée sur les pistes en raison des conditions de gel. La condition des pistes a diminué et le risque de blessures a augmenté.';

// Événements de crise
$lang['logs']['crisis']                          = 'Crise';
$lang['logs']['crisis_title']                    = 'Événements de crise';
$lang['logs']['crisis_intro']                    = 'Surveille les événements de crise rares mais percutants qui touchent ta station. Réagis rapidement pour limiter les dommages sur ta réputation et tes finances.';
$lang['logs']['crisis_active_events']            = 'Crises actives';
$lang['logs']['crisis_history']                  = 'Historique des crises';
$lang['logs']['crisis_no_active']                = 'Aucune crise active. Ta station fonctionne normalement !';
$lang['logs']['crisis_no_history']               = 'Aucun événement de crise enregistré pour l\'instant.';
$lang['logs']['crisis_impact']                   = 'Impact';
$lang['logs']['crisis_status']                   = 'Statut';
$lang['logs']['crisis_resolved']                 = 'Résolu';
$lang['logs']['crisis_unresolved']               = 'Actif';
$lang['logs']['crisis_mark_resolved']            = 'Marquer comme résolu';

// Libellés des types de crise
$lang['logs']['crisis_type_lift_failure']        = 'Panne majeure de remontée';
$lang['logs']['crisis_type_avalanche']           = 'Incident d\'avalanche';
$lang['logs']['crisis_type_power_outage']        = 'Panne de courant';
$lang['logs']['crisis_type_viral_negative']      = 'Buzz négatif dans les médias';

// Messages de notification de crise
$lang['logs']['crisis_lift_failure_msg']         = 'CRISE : Une panne mécanique majeure a touché une de tes remontées ! Elle a été mise hors service pour réparations d\'urgence. Réputation -10.';
$lang['logs']['crisis_avalanche_msg']            = 'CRISE : Une avalanche a fermé une de tes pistes et réduit l\'enneigement ! Les secours sont sur place. Réputation -15.';
$lang['logs']['crisis_power_outage_msg']         = 'CRISE : Une panne de courant a frappé ta station ! Des frais d\'urgence ont été engagés. Réputation -5.';
$lang['logs']['crisis_viral_negative_msg']       = 'CRISE : Un article négatif viral sur ta station se répand sur les réseaux sociaux ! Ta réputation a subi un coup dur. Réputation -20.';
$lang['logs']['ice_accumulation']               = 'La glace s\'est accumulée sur les pistes en raison des conditions de gel. La condition des pistes a diminué et le risque de blessures a augmenté.';

// Insurance
$lang['logs']['insurance']                   = 'Assurance';
$lang['logs']['insurance_premium_charged']   = 'Prime d\'assurance prélevée :';
$lang['logs']['insurance_lift_claim']        = 'Sinistre remboursé (accident de remontée) :';
$lang['logs']['insurance_storm_claim']       = 'Sinistre remboursé (dommage tempête) :';
// Entrées de journal R&D expérimental
$lang['logs']['rd_log_type']         = 'R&D expérimentale';
$lang['logs']['rd_started']          = 'Projet R&D démarré';
$lang['logs']['rd_completed']        = 'Projet R&D terminé';
$lang['logs']['rd_failed']           = 'Projet R&D ÉCHOUÉ – accident survenu';
// Celebrity / VIP visits
$lang['logs']['celebrity_visit']                 = 'Visite Célébrité';
$lang['logs']['celebrity_visit_influencer']      = 'Influenceur';
$lang['logs']['celebrity_visit_pro_skier']       = 'Skieur Pro';
$lang['logs']['celebrity_visit_film_crew']       = 'Équipe de tournage';
$lang['logs']['celebrity_visit_good_slopes_msg'] = 'Un(e) %s a visité votre station aujourd\'hui ! Vos pistes étaient en excellent état — votre réputation a grimpé ! Réputation +%d.';
$lang['logs']['celebrity_visit_avg_slopes_msg']  = 'Un(e) %s a visité votre station aujourd\'hui ! Un petit boost de réputation. Réputation +%d.';
$lang['logs']['celebrity_visit_lift_fail_msg']   = 'Un(e) %s a visité votre station aujourd\'hui, mais une remontée était en panne ! Ce désastre de relations publiques a fortement nui à votre réputation. Réputation %d.';
$lang['logs']['transport_rep_bonus']             = 'services d\'accessibilité et de transport';
$lang['logs']['scenic_lift_revenue_log']         = 'Revenu net journalier du téléphérique panoramique :';
// Webcams de montagne
$lang['logs']['mountain_cam_saved']    = 'Paramètres des webcams de montagne enregistrés.';
$lang['logs']['mountain_cam_revenue']  = 'Bonus de réputation journalier des webcams de montagne appliqué.';

// Bonus de connexion quotidien
$lang['logs']['daily_bonus']             = 'Bonus de connexion quotidien';
$lang['logs']['daily_bonus_claimed']     = 'Bonus de connexion quotidien collecté : +';
$lang['logs']['daily_bonus_streak']      = ' (série jour ';
$lang['logs']['daily_bonus_streak_end']  = ')';
// Revenus passifs (idle income)
$lang['logs']['idle_income']             = 'Revenus passifs';
$lang['logs']['idle_income_collected']   = 'Votre station a généré des revenus pendant votre absence : +';
// Mini-jeux
$lang['logs']['minigames'] = 'Mini-jeux';
// Micro-événements
$lang['logs']['micro_events'] = 'Décision rapide';
// Saisons dynamiques
$lang['logs']['seasonal_melt_log'] = 'Fonte naturelle saisonnière de neige : -';
// Système d'urgence et de secours
$lang['logs']['emergency']              = 'Urgences & Secours';
$lang['logs']['emergency_fast_response'] = 'Excellent temps de réponse d\'urgence — vos clients se sentent en sécurité. +' . EMERGENCY_REP_FAST_RESPONSE_BONUS . ' réputation.';
$lang['logs']['emergency_poor_response'] = 'Temps de réponse d\'urgence lent — inquiétudes de sécurité soulevées. ' . EMERGENCY_REP_POOR_RESPONSE_PENALTY . ' réputation.';
$lang['logs']['emergency_incident']     = 'Un incident d\'urgence s\'est produit dans votre station.';
// Commerce & Boutiques
$lang['logs']['retail']         = 'Commerce & Boutiques';
$lang['logs']['retail_revenue'] = 'Revenu journalier des boutiques en bord de piste :';
// Gouvernement & Réglementations
$lang['logs']['government']              = 'Gouvernement &amp; Réglementations';
$lang['logs']['gov_audit_pass']          = 'Inspection de sécurité réussie :';
$lang['logs']['gov_audit_fail']          = 'Inspection de sécurité échouée – amende :';
$lang['logs']['gov_expansion_blocked']   = 'Extension bloquée par le gouvernement (faible conformité).';
$lang['logs']['gov_expansion_unblocked'] = 'Restriction d\'extension levée (conformité rétablie).';
$lang['logs']['gov_tax_charged']         = 'Taxe réglementaire prélevée :';
$lang['logs']['gov_subsidy_awarded']     = 'Subvention écologique disponible :';
$lang['logs']['gov_tax_rate_updated']    = 'Taux de taxe réglementaire mis à jour pour la nouvelle saison :';
