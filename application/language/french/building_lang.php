<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

// Common to all buildings
$lang['common_buildings']['titleMain']		= 'Bâtiments';

// Access Resort buildings
$lang['access_resort']['title']		= 'Accès & Transport';
$lang['access_resort']['intro']		= 'Information concernant les bâtiments utiles pour accéder à la station. Plus l\'infrastructure de ta station est performante, plus les touristes seront attirés et plus de revenus seront générés.';

// Tourist info
$lang['tourist_info']['title']		= 'Office du tourisme';
$lang['touristinfo']['title']		= 'Office du tourisme';
$lang['touristinfo']['title_sing']		= 'Office du tourisme';
$lang['tourist_info']['title_sing']		= 'Office du tourisme';
$lang['tourist_info']['desc']		= 'L\'office du tourisme doit être construit avant tout autre bâtiment. Il est nécessaire afin d\'attirer les touristes dans la station. C\'est aussi là que tu peux fixer le prix des forfaits de ski et peux ouvrir ou fermer ta station. <br> Quand la station est fermée, aucun tourist ne se rendra dans ta station donc celle-ci ne génèrera aucun revenus et tu n\'auras pas à payer le personnel.';
$lang['tourist_info']['skiPassLabel']                   = 'Prix des forfaits';
$lang['tourist_info']['oneDay']                         = '1 journée';
$lang['tourist_info']['oneWeek']                        = '1 semaine';
$lang['tourist_info']['inEuros']                        = '(en euros)';
$lang['tourist_info']['price_updated']                  = 'Prix mis à jour !';
$lang['tourist_info']['price_not_updated']              = 'Limite atteinte !';
$lang['tourist_info']['dynamic_pricing_saved']          = 'Paramètres de tarification dynamique sauvegardés !';
$lang['tourist_info']['dynamic_pricing_title']          = 'Tarification Dynamique';
$lang['tourist_info']['dynamic_pricing_desc']           = 'Attirez plus de visiteurs avec des remises famille et générez des revenus premium grâce aux passes VIP.';
$lang['tourist_info']['vip_pass_label']                 = 'Prix du pass VIP (€, 0 = désactivé)';
$lang['tourist_info']['vip_pass_help']                  = 'Un pass journalier premium pour ' . (int)(VIP_VISITOR_FRACTION * 100) . '% de vos visiteurs à un prix plus élevé. Mettre à 0 pour désactiver.';
$lang['tourist_info']['family_discount_label']          = 'Remise famille (%, 0 = désactivée)';
$lang['tourist_info']['family_discount_help']           = 'Offrez une remise à ' . (int)(FAMILY_VISITOR_FRACTION * 100) . '% de vos visiteurs journaliers pour attirer plus de familles. Chaque 1% de remise augmente la fréquentation de ' . (FAMILY_DISCOUNT_DEMAND_BONUS) . '%.';
$lang['tourist_info']['group_discount_label']           = 'Remise groupe (%, 0 = désactivée)';
$lang['tourist_info']['group_discount_help']            = 'Offrez une remise à ' . (int)(GROUP_VISITOR_FRACTION * 100) . '% de vos visiteurs arrivant en groupe. Chaque 1% de remise augmente la fréquentation des groupes de ' . (GROUP_DISCOUNT_DEMAND_BONUS) . '%.';
$lang['tourist_info']['save_skipass_price']             = 'Sauvegarder le prix du forfait';
$lang['tourist_info']['save_dynamic_pricing']           = 'Sauvegarder la tarification';
$lang['tourist_info']['current_prices_title']           = 'Prix actuels des forfaits';
$lang['tourist_info']['daily_label']                    = 'Journée';
$lang['tourist_info']['weekly_label']                   = 'Semaine';
$lang['tourist_info']['effective_price_label']          = 'Prix effectif (avec bonus prestige)';
$lang['tourist_info']['resort_open']                    = 'Station ouverte';
$lang['tourist_info']['resort_closed']                  = 'Station fermée';
$lang['tourist_info']['resort_construction']            = 'En construction';
$lang['tourist_info']['resort_closed_warning']          = '⚠️ Votre station est actuellement fermée. Aucun revenu ni visiteur ne sera enregistré jusqu\'à la réouverture.';
$lang['tourist_info']['open_resort_tip']                = 'Ouvrez la station pour commencer à accueillir des touristes et générer des revenus.';
$lang['tourist_info']['close_resort_tip']               = 'Fermez la station pour suspendre les visites touristiques et les paiements du personnel.';

// Resort access
$lang['access']['accessResortTitle']		= 'Accès à la station';
$lang['access']['title']		= 'Bâtiment d\'accès';
$lang['access']['title_sing']		= 'Bâtiment d\'accès';
$lang['access']['accessResortDesc']		= 'La manière dont les touristes accèdent à ta station est vraiment importante. Améliore l\'infrastructure pour attirer plus de touristes.';
$lang['access']['infrastructure']                 = 'Infrastructure';
$lang['access']['current_benefit_title']          = 'Bénéfice actuel';
$lang['access']['visitor_bonus_label']            = 'Bonus visiteurs max';
$lang['access']['no_access_built']                = 'Aucune infrastructure d\'accès construite. Construisez le niveau 1 pour commencer à attirer plus de touristes.';

$lang['access']['thanks_prestige_bonus']                 = 'Grâce au bonus offert par le Prestige de votre station, chaque touriste va dépenser un supplément de';
$lang['access']['after_purchasing_skipass']                 = 'après avoir acheté son forfait. Cela signifie que chaque touriste dépensera';
$lang['access']['for_the_daily_skipass']                 = 'pour le forfait journalier et';
$lang['access']['for_the_weekly_skipass']                 = 'pour le forfait semaine.';

// Parking
$lang['parking']['parkingTitle']                   = 'Parking';
$lang['parking']['title']		= 'Parking';
$lang['parking']['title_sing']		= 'Parking';
$lang['parking']['parkingDesc']		= 'Assure-toi que les touristes qui viennent en voiture peuvent se garer dans ta station et tu gagneras plus d\'argent. Tu ne peux construire qu\'une seule zone de parking pour la station, alors assure-toi de l\'améliorer!';
$lang['parking']['current_income_title']           = 'Revenus actuels';
$lang['parking']['max_daily_income_label']         = 'Revenu journalier max';
$lang['parking']['no_parking_built']               = 'Aucun parking construit. Construisez le niveau 1 pour commencer à percevoir des revenus des voitures garées.';
$lang['parking']['fee_title']                      = 'Tarif de Stationnement';
$lang['parking']['fee_desc']                       = 'Définissez le tarif facturé par véhicule par jour. Un tarif plus élevé rapporte plus par voiture mais réduit la demande. Le tarif optimal est de ' . DEFAULT_PARKING_FEE . ' €.';
$lang['parking']['fee_label']                      = 'Tarif de stationnement (€/véhicule/jour)';
$lang['parking']['fee_help']                       = 'Min ' . MIN_PARKING_FEE . ' €, Max ' . MAX_PARKING_FEE . ' €. Un tarif plus élevé réduit le nombre de voitures qui utilisent votre parking.';
$lang['parking']['fee_save_btn']                   = 'Enregistrer le tarif';
$lang['parking']['fee_updated']                    = 'Tarif de stationnement mis à jour !';
$lang['parking']['fee_not_updated']                = 'Échec de la mise à jour du tarif de stationnement !';

// Building building Actions
$lang['building']['building_opened']                = '<div class="alert alert-success text-center">Le bâtiment a été ouvert avec succès.</div>';
$lang['building']['building_closed']                = '<div class="alert alert-success text-center">Le bâtiment a été fermé avec succès.</div>';
$lang['building']['building_not_opened']		= '<div class="alert alert-danger text-center\">Le bâtiment n\'a pas pu être ouvert.</div>';
$lang['building']['building_not_closed']		= '<div class="alert alert-danger text-center">Le bâtiment n\'a pas pu être fermé.</div>';
$lang['building']['building_not_existing']		= '<div class="alert alert-danger text-center">Ce bâtiment n\'existe pas.</div>';
$lang['building']['build']                          = 'Construire';
$lang['building']['building']                          = 'Construction en cours';
$lang['building']['building_built']                 = '<div class="alert alert-success text-center">La construction du bâtiment a démarré avec succès.</div>';
$lang['building']['building_already_built']		= '<div class="alert alert-danger text-center">Il y a déjà une construction en cours.</div>';
$lang['building']['building_one_at_a_time']		= '<div class="alert alert-danger text-center">Tu ne peux construire qu\'un bâtiment de chaque type simultanément. Réessaie lorsque le bâtiment actuel sera terminé.</div>';
$lang['building']['building_not_built']		= '<div class="alert alert-danger text-center">La construction du bâtiment n\'a pas pu démarrer.</div>';

// Building Upgrade actions
$lang['building']['upgrade']                        = 'Améliorer';
$lang['building']['upgraded']		= 'Amélioré';
$lang['building']['upgrading']		= 'En cours d\'amélioration';
$lang['building']['building_upgraded']              = '<div class="alert alert-success text-center">L\'amélioration du bâtiment a démarré avec succès.</div>';
$lang['building']['building_not_upgraded']		= '<div class="alert alert-danger text-center">L\'amélioration du bâtiment n\'a pas pu démarrer.</div>';
$lang['building']['building_not_built_previous']    = '<div class="alert alert-danger text-center">Tu ne peux pas améliorer à ce niveau avant de construire le précédent.</div>';

$lang['building']['bad_action']		= '<div class="alert alert-danger text-center">Cette action ne peut pas être effectuée.</div>';
// General
$lang['building']['current_level']                  = 'Niveau actuel:';
$lang['building']['there_are']                  = 'Il y a ';
$lang['building']['active_text_cannon']                  = ' canons à neige actifs ';
$lang['building']['inactive_text_cannon']                  = ' canons à neige inactifs ';
$lang['building']['start_all']                  = 'Démarrer tous';
$lang['building']['stop_all']                  = 'Arrêter tous';
$lang['building']['cannons_started']                  = '<div class="alert alert-success text-center">Tous les canons à neige ont été lancés.</div>';
$lang['building']['cannons_stopped']                  = '<div class="alert alert-success text-center">Tous les canons à neige ont été arrêtés.</div>';
$lang['building']['not_enough_money']		= 'Pas assez d\'argent pour effectuer cette action.';

$lang['building']['max_income']                  = 'Revenu quotidien maximum';
$lang['building']['max_bonus_affluence']                  = 'Bonus de fréquentation maximum';
$lang['building']['max_income_per_building']                  = 'Revenu quotidien maximum par bâtiment';
$lang['building']['daily_cost_per_cannon']                  = 'Coût quotidien par canon';
$lang['building']['quantity']                  = 'Quantité';
$lang['building']['tourist_info_required']        = '<div class="alert alert-danger text-center">Tu dois construire l\'office du tourisme avant de construire quoi que ce soit d\'autre. Clique <a href="'.base_url().'building_access_controller">ici</a> pour accéder à la page de l\'office du tourisme.</div>';
$lang['building']['no_tourist_info']        = 'L\'office du tourisme n\'est pas construit. Construis-le pour obtenir le statut de la station.';
$lang['building']['achievement_locked']        = 'Tu as besoin de dévérouiller et collecter quelques objectifs supplémentaires afin de construire ce type de bâtiment. Rends-toi sur la <a href="'.base_url().'achievements_controller">page objectifs</a> pour vérifier ton progrès.';

$lang['building']['the_achievement']        = 'L\'ojectif';
$lang['building']['ach_not_completed']        = 'est requis pour contruire ce type de bâtiment et n\'est pas encore atteint.';
$lang['building']['current_progress_is']        = 'Le progrès actuel est de ';
$lang['building']['ach_not_claimed']        = 'est atteint mais tu n\'as pas encore récolté ta récompense.';
$lang['building']['achievement_link_info']        = 'Vérifie la <a href="'.base_url().'achievements_controller">page des objectifs</a> pour plus de détails.</div>';

// Hotel
$lang['hotel']['title']                 = 'Hôtels';
$lang['hotel']['title_sing']                 = 'Hôtel';
$lang['hotel']['intro']                 = 'Information concernant les hôtels.';
$lang['hotel']['desc']		= 'Les hôtels et résidences offrent un lieu de séjour à tes visiteurs. Tu dois avoir une capacité suffisante pour accueillir tous les touristes. Il est possible de construire plusieurs hôtels et tu peux les améliorer pour qu\'ils soient plus attrayants et pour augmenter la capacité d\'accueil.<br>Tu dois construire l\'office du tourisme avant de construire des hôtels.';

// Restaurant
$lang['restaurant']['title']                 = 'Restaurants';
$lang['restaurant']['title_sing']                 = 'Restaurant';
$lang['restaurant']['intro']                 = 'Information concernant les restaurants.';
$lang['restaurant']['desc']		= 'Les snacks et les restaurants permettent à tes visiteurs de manger partout où ils se trouvent dans ta station; sur les pentes ou dans la vallée. Il est possible de construire plusieurs restaurants et tu peux les améliorer pour attirer différents types de clients.<br>Tu dois construire l\'office du tourisme avant de construire des restaurants.';

// Rental
$lang['rental']['title']                 = 'Location de ski';
$lang['rental']['title_sing']                 = 'Location de ski';
$lang['rental']['intro']                 = 'Information concernant les magasins de location de ski.';
$lang['rental']['desc']		= 'Les magasins de location de ski permettent aux visiteurs de louer du matériel pour leurs vacances. Fournir des skis, des snowboards, des chaussures, des vêtements, des casques, et plus, ils génèrent également beaucoup d\'argent. Il est possible de construire plusieurs magasins de location de skis et tu peux les améliorer pour attirer différents types de clients.<br>Tu dois construire l\'office du tourisme avant de construire des magasins de location.';
// Leisure
$lang['leisure']['title']                 = 'Loisirs';
$lang['leisure']['title_sing']                 = 'Loisirs';
$lang['leisure']['intro']                 = 'Information concernant les centres de loisirs';
$lang['leisure']['desc']		= 'Les centres de loisirs offrent à tes visiteurs de nombreuses activités pour profiter de leur temps libre. Il est possible de construire plusieurs bâtiments de loisir et de les améliorer pour s\'assurer que tous les visiteurs trouvent leur activité favorite.<br>Tu dois construire l\'office du tourisme avant de construire des centres de loisirs.';

// Luxury
$lang['luxury']['title']                 = 'Luxe';
$lang['luxury']['title_sing']                 = 'Établissement de luxe';
$lang['luxury']['intro']                 = 'Information concernant les établissements de luxe.';
$lang['luxury']['desc']		= 'Les établissements de luxe accueillent une clientèle exclusive de clients VIP à hautes dépenses. Propose des chalets VIP, du ski en hélicoptère, des moniteurs de ski privés et des lounges exclusifs pour attirer un petit nombre de clients qui génèrent des profits massifs. Seule une fraction de tes visiteurs recherchera ces services premium, mais le revenu par client dépasse largement celui des bâtiments standards.<br>Tu dois construire l\'office du tourisme avant de construire des établissements de luxe.';

// Medical
$lang['medical']['title']                 = 'Centres médicaux';
$lang['medical']['title_sing']                 = 'Centre médical';
$lang['medical']['intro']                 = 'Information concernant les centres médicaux.';
$lang['medical']['desc']		= 'Les centres médicaux assurent la prise en charge des visiteurs blessés dans de bonnes conditions. Si les blessés ne sont pas soignés à temps, la réputation de ta station pourrait diminuer et tu devras peut-être payer des frais de dédomagement. Il est possible de construire plusieurs centres médicaux et de les améliorer pour augmenter la capacité.<br>Tu dois construire l\'office du tourisme avant de construire des centres médicaux.';

// Resort Facilities
$lang['facility']['title']                 = 'Équipements de la station';
$lang['facility']['title_sing']            = 'Équipement de la station';
$lang['facility']['intro']                 = 'Information concernant les équipements de la station.';
$lang['facility']['desc']		= 'Les équipements de la station comme les spas, centres de bien-être et salles de fitness offrent à vos visiteurs une expérience premium et un endroit pour se détendre après une journée sur les pistes. Ils génèrent des revenus réguliers et améliorent la réputation de votre station. Il est possible de construire plusieurs équipements et de les améliorer pour accueillir davantage de visiteurs.<br>Tu dois construire l\'office du tourisme avant de construire des équipements de station.';

// Snow cannons
$lang['cannon']['title']                 = 'Canons à neige';
$lang['cannon']['title_sing']                 = 'Canon à neige';
$lang['cannon']['mini_title']            = 'canons à neige';
$lang['cannon']['intro']                 = 'Information concernant les canons à neige.';
$lang['cannon']['desc']                 = 'Les canons à neige ajoutent de la neige à ta station tous les soirs. Pour simplifier le méchanisme, le niveau de neige sera augmenté uniformément dans toute la station. Il est possible de construire plusieurs canons à neige et de les améliorer pour augmenter la quantité de neige ajoutée.<br>Tu dois construire l\'office du tourisme avant de construire des centres médicaux.';

$lang['building']['snow_output_per_cannon']       = 'Sortie de neige (cm/nuit)';
$lang['building']['current_snow_level']           = 'Niveau de neige actuel dans la station';
$lang['building']['cm']                           = 'cm';
$lang['building']['cannon_number']                = '#';
$lang['building']['cannon_level_col']             = 'Niveau';
$lang['building']['cannon_snow_output_col']       = 'Sortie de neige (cm/nuit)';
$lang['building']['cannon_daily_cost_col']        = 'Coût quotidien (€)';
$lang['building']['cannon_status_col']            = 'Statut';
$lang['building']['cannon_action_col']            = 'Action';
$lang['building']['cannon_status_active']         = 'Actif';
$lang['building']['cannon_status_inactive']       = 'Inactif';
$lang['building']['cannon_status_construction']   = 'En construction';
$lang['building']['start_cannon']                 = 'Démarrer';
$lang['building']['stop_cannon']                  = 'Arrêter';
$lang['building']['cannon_started']               = '<div class="alert alert-success text-center">Le canon à neige a été démarré.</div>';
$lang['building']['cannon_stopped']               = '<div class="alert alert-success text-center">Le canon à neige a été arrêté.</div>';
$lang['building']['individual_cannons_title']     = 'Gestion individuelle des canons';

// Snow cannon summary
$lang['building']['cannon_summary_title']         = 'Résumé de production (canons actifs)';
$lang['building']['cannon_total_snow_output']     = 'Production de neige totale par nuit';
$lang['building']['cannon_total_daily_cost']      = 'Coût quotidien total de fonctionnement';

// Snow target level
$lang['building']['snow_target_title']            = 'Niveau de neige cible';
$lang['building']['snow_target_current']          = 'Cible actuelle';
$lang['building']['snow_target_none']             = 'Aucune cible définie — les canons ajouteront de la neige chaque nuit jusqu\'au niveau maximum.';
$lang['building']['snow_target_disabled']         = 'sans cible';
$lang['building']['save_snow_target']             = 'Enregistrer la cible';
$lang['building']['snow_target_info']             = 'Lorsque le niveau de neige atteint cette cible, les canons cessent d\'ajouter de la neige la nuit. Mets 0 pour désactiver et toujours ajouter de la neige.';
$lang['building']['snow_target_saved']            = '<div class="alert alert-success text-center">La cible de neige a été enregistrée.</div>';

// Snow auto-start threshold
$lang['building']['snow_auto_start_title']        = 'Seuil de démarrage automatique';
$lang['building']['snow_auto_start_current']      = 'Seuil actuel';
$lang['building']['snow_auto_start_none']         = 'Aucun seuil défini — les canons ne démarreront que manuellement.';
$lang['building']['snow_auto_start_disabled']     = 'sans seuil';
$lang['building']['save_snow_auto_start']         = 'Enregistrer le seuil';
$lang['building']['snow_auto_start_info']         = 'Lorsque le niveau de neige descend en dessous de cette valeur pendant la nuit, tous les canons inactifs démarrent automatiquement. Mets 0 pour désactiver.';
$lang['building']['snow_auto_start_saved']        = '<div class="alert alert-success text-center">Le seuil de démarrage automatique a été enregistré.</div>';

// Snow level history
$lang['building']['snow_history_title']           = 'Historique du niveau de neige (' . SNOW_HISTORY_DAYS . ' derniers jours)';
$lang['building']['snow_history_date']            = 'Date';
$lang['building']['snow_history_level']           = 'Niveau de neige (cm)';
$lang['building']['snow_history_none']            = 'Aucun historique disponible pour le moment.';

// Low snow warning
$lang['building']['low_snow_warning']             = 'Attention : le niveau de neige est dangereusement bas ! Démarre tes canons à neige pour restaurer la couverture neigeuse.';

// Night skiing
$lang['building']['night_skiing_title']           = 'Ski nocturne';
$lang['building']['night_skiing_page_intro']      = 'Le ski nocturne permet à tes pistes de rester ouvertes après la tombée de la nuit, attirant des visiteurs supplémentaires et générant des revenus supplémentaires.';
$lang['building']['night_skiing_desc']            = 'Le ski nocturne permet à tes pistes de rester ouvertes après la tombée de la nuit, attirant des visiteurs supplémentaires et générant des revenus supplémentaires.';
$lang['building']['night_skiing_on']              = 'Ski nocturne : ACTIVÉ';
$lang['building']['night_skiing_off']             = 'Ski nocturne : DÉSACTIVÉ';
$lang['building']['enable_night_skiing']          = 'Activer le ski nocturne';
$lang['building']['disable_night_skiing']         = 'Désactiver le ski nocturne';
$lang['building']['night_skiing_info']            = 'Le ski nocturne génère un bonus sur les revenus de skipass nocturnes. Le bonus et le coût électrique augmentent avec le nombre de pistes ouvertes.';
$lang['building']['night_skiing_enabled']         = '<div class="alert alert-success text-center">Le ski nocturne a été activé. Tes pistes seront maintenant ouvertes après la tombée de la nuit !</div>';
$lang['building']['night_skiing_disabled']        = '<div class="alert alert-warning text-center">Le ski nocturne a été désactivé.</div>';
$lang['building']['night_skiing_needs_cannon']    = '<div class="alert alert-danger text-center">Tu as besoin d\'au moins un canon à neige actif pour activer le ski nocturne.</div>';
$lang['building']['night_skiing_status_label']    = 'Statut actuel';
$lang['building']['night_skiing_key_figures']     = 'Chiffres clés';
$lang['building']['night_skiing_open_slopes_label']   = 'Pistes ouvertes';
$lang['building']['night_skiing_revenue_bonus_label'] = 'Bonus de revenus par nuit';
$lang['building']['night_skiing_electricity_label']   = 'Coût d\'électricité par nuit';
$lang['building']['night_skiing_base_label']          = 'base';
$lang['building']['night_skiing_per_slope_label']     = 'piste';
$lang['building']['night_skiing_skipass_daily_label']  = 'Prix du skipass journalier';
$lang['building']['night_skiing_skipass_weekly_label'] = 'Prix du skipass hebdomadaire';
$lang['building']['night_skiing_how_it_works']    = 'Comment ça fonctionne';
$lang['building']['night_skiing_how_it_works_desc'] = 'Lorsque le ski nocturne est activé, ta station gagne un bonus sur les revenus de skipass nocturnes. Le bonus de base est de ' . (int)(NIGHT_SKIING_REVENUE_BONUS * 100) . ' %, avec ' . (int)(NIGHT_SKIING_SLOPE_REVENUE_FACTOR * 100) . ' % supplémentaire par piste ouverte au-delà de la première. Le coût d\'électricité est de ' . number_format(NIGHT_SKIING_ELECTRICITY_COST, 0, ',', ' ') . ' € de base, plus ' . number_format(NIGHT_SKIING_ELECTRICITY_COST_PER_SLOPE, 0, ',', ' ') . ' € par piste ouverte par nuit. Le ski nocturne peut être activé ou désactivé à tout moment.';
$lang['building']['night_skiing_resort_settings']     = 'Paramètres de la station';
$lang['building']['night_skiing_start_hour_label']    = 'Heure de début';
$lang['building']['night_skiing_end_hour_label']      = 'Heure de fin';
$lang['building']['night_skiing_ticket_price_label']  = 'Prix du forfait nocturne';
$lang['building']['night_skiing_save_settings']       = 'Enregistrer les paramètres';
$lang['building']['night_skiing_trails_enabled_label'] = 'Pistes avec ski nocturne';
$lang['building']['night_skiing_no_open_slopes']      = 'Aucune piste ouverte disponible pour la configuration du ski nocturne.';
$lang['building']['night_skiing_trails_title']        = 'Paramètres d\'éclairage des pistes';
$lang['building']['night_skiing_trails_intro']        = 'Configure l\'éclairage pour chaque piste ouverte. Active le ski nocturne par piste et choisis le type d\'éclairage, l\'intensité et l\'espacement des mâts.';
$lang['building']['night_skiing_trail_name']          = 'Piste';
$lang['building']['night_skiing_trail_enabled']       = 'Ski nocturne';
$lang['building']['night_skiing_light_type_label']    = 'Type d\'éclairage';
$lang['building']['night_skiing_brightness_label']    = 'Intensité lumineuse';
$lang['building']['night_skiing_pole_spacing_label']  = 'Espacement des mâts';
$lang['building']['night_skiing_on_short']            = 'ACTIVÉ';
$lang['building']['night_skiing_off_short']           = 'DÉSACTIVÉ';
$lang['building']['night_skiing_light_led']           = 'LED';
$lang['building']['night_skiing_light_halogen']       = 'Halogène';
$lang['building']['night_skiing_light_metal_halide']  = 'Iodure métallique';
$lang['building']['night_skiing_configure']           = 'Configurer';
$lang['building']['night_skiing_configure_trail']     = 'Configurer la piste';
$lang['building']['night_skiing_light_type_help']     = 'Les LED sont économes en énergie. Les halogènes sont moins chers mais consomment plus. Les iodures métalliques offrent l\'éclairage le plus puissant.';
$lang['building']['night_skiing_brightness_help']     = 'Une intensité plus élevée améliore l\'expérience des visiteurs mais augmente les coûts d\'électricité.';
$lang['building']['night_skiing_pole_spacing_help']   = 'Un espacement plus serré des mâts offre une meilleure couverture mais augmente les coûts.';
$lang['building']['night_skiing_spacing_15']          = 'Dense (15 m)';
$lang['building']['night_skiing_spacing_25']          = 'Standard (25 m)';
$lang['building']['night_skiing_spacing_35']          = 'Clairsemé (35 m)';
$lang['building']['cancel']                           = 'Annuler';
$lang['building']['night_skiing_settings_saved']      = '<div class="alert alert-success text-center">Les paramètres du ski nocturne ont été enregistrés.</div>';
$lang['building']['night_skiing_settings_invalid']    = '<div class="alert alert-danger text-center">Paramètres de ski nocturne invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['night_skiing_trail_saved']         = '<div class="alert alert-success text-center">Les paramètres de ski nocturne pour cette piste ont été enregistrés.</div>';

// Ski nocturne – divertissement et niveau de sécurité
$lang['building']['night_skiing_per_night_label']     = 'nuit';
$lang['building']['night_skiing_entertainment_label'] = 'Divertissement en soirée';
$lang['building']['night_skiing_entertainment_help']  = 'Ajoute des animations pour attirer plus de skieurs nocturnes et augmenter les revenus. Les niveaux supérieurs coûtent plus cher mais offrent un meilleur multiplicateur de revenus.';
$lang['building']['night_skiing_ent_none']    = 'Aucun';
$lang['building']['night_skiing_ent_basic']   = 'Basique (boissons chaudes & musique d\'ambiance)';
$lang['building']['night_skiing_ent_premium'] = 'Premium (musique live & bar complet)';
$lang['building']['night_skiing_safety_label'] = 'Niveau de sécurité';
$lang['building']['night_skiing_safety_help']  = 'Un investissement plus élevé en sécurité réduit le risque d\'accidents, améliore la satisfaction des visiteurs et confère un bonus de réputation chaque nuit.';
$lang['building']['night_skiing_safety_1'] = 'Standard';
$lang['building']['night_skiing_safety_2'] = 'Renforcé';
$lang['building']['night_skiing_safety_3'] = 'Maximum';

// Night skiing -- ecole de ski nocturne
$lang['building']['night_skiing_school_label']          = 'École de ski nocturne';
$lang['building']['night_skiing_school_toggle']         = 'Activer l\'école de ski nocturne';
$lang['building']['night_skiing_school_price_label']    = 'Prix de la leçon par personne';
$lang['building']['night_skiing_school_per_person_label'] = 'personne';
$lang['building']['night_skiing_school_help']           = 'Propose des cours de ski pendant les sessions nocturnes. Environ 8 % des visiteurs nocturnes s\'inscrivent, générant des revenus supplémentaires et un bonus de +1 de réputation par nuit.';

// Night skiing -- suspension automatique par meteo
$lang['building']['night_skiing_weather_suspend_label'] = 'Suspension automatique par temps de pluie';
$lang['building']['night_skiing_weather_suspend_help']  = 'Lorsque cette option est activée, le ski nocturne est automatiquement suspendu les nuits pluvieuses pour protéger la sécurité des visiteurs. Aucun revenu ni coût n\'est engagé lors des nuits suspendues.';

// Night skiing -- descente aux flambeaux
$lang['building']['night_skiing_torchlight_label']      = 'Descente aux flambeaux';
$lang['building']['night_skiing_torchlight_toggle']     = 'Activer la descente aux flambeaux';
$lang['building']['night_skiing_torchlight_help']       = 'Organise une descente aux flambeaux guidée chaque nuit. Coûte 200 €/nuit et attire +15 % de visiteurs nocturnes supplémentaires, rapportant +1 de réputation par nuit.';

// Night skiing -- forfait photographie nocturne
$lang['building']['night_skiing_photo_label']           = 'Forfait photographie nocturne';
$lang['building']['night_skiing_photo_toggle']          = 'Activer le forfait photographie';
$lang['building']['night_skiing_photo_price_label']     = 'Prix du forfait par personne';
$lang['building']['night_skiing_photo_per_person_label'] = 'personne';
$lang['building']['night_skiing_photo_help']            = 'Propose des sessions photo guidées en soirée. Environ 5 % des visiteurs nocturnes s\'inscrivent, générant des revenus supplémentaires et un bonus de +1 de réputation par nuit.';





// Event venues common page
$lang['event_venues_buildings']['titleMain']                 = 'Bâtiments d\'événements';
$lang['event_venues_buildings']['intro']		= 'Tous les bâtiments nécessaires pour organiser des tournois de classe mondiale sont disponibles ici. Consulte la <a href="'.base_url().'tournaments_controller">page des tournois</a> pour vérifier quels bâtiments et niveaux sont requis.';

$lang['housing_complex']['title']		= 'Complexe immobilier';
$lang['housing_complex']['title_sing']		= 'Complexe immobilier';
$lang['housing_complex']['desc']		= 'Ce complexe immobilier peut accueillir tous les athlètes, officiels et entraîneurs participants de tes tournois. En construisant le complexe, tu laisses les hôtels habituels aux touristes visitant ta station. L\'amélioration du complexe au niveau maximum accordera le statut de village olympique.';

$lang['icerink']['title']		= 'Patinoire';
$lang['icerink']['title_sing']		= 'Patinoire';
$lang['icerink']['desc']		= 'La patinoire est une arène composée d\une surface de glace où les athlètes participant à des tournois peuvent pratiquer le patinage sur glace et des activités similaires. La glace de la patinoire est de haute qualité, ce qui justifie les coûts d\'exploitation élevés.';

$lang['curling_center']['title']		= 'Halle de curling';
$lang['curling_center']['title_sing']		= 'Halle de curling';
$lang['curling_center']['desc']		= 'La halle de curling est une arène composée d\'une ou plusieurs surfaces de jeu en glace où les athlètes peuvent jouer au curling pendant les tournois. La glace de la halle de curling n\'est pas de la même qualité que la patinoire, d\'où des coûts d\'exploitation moins élevés.';

$lang['open_stage']['title']		= 'Scène de concert';
$lang['open_stage']['title_sing']		= 'Scène de concert';
$lang['open_stage']['desc']		= 'La scène de concert est le lieu où les célébrations ont lieu lors de tes tournois. L\'amélioration de la scène à des niveaux supérieurs permettra des concerts et des événements musicaux plus importants, avec des artistes célèbres.';












$lang['building']['building_type']                   = 'Type de bâtiment';
$lang['building']['total_capacity']                 = 'Capacité totale';
$lang['building']['max_tourists']                 = 'Touristes maximum';
$lang['building']['total_capacity_help']                 = 'Ceci est la capacité totale pour chaque type de bâtiment';
$lang['building']['max_tourists_help']                 = 'Ceci est le nombre maximum de touristes qui peuvent visiter ta  station avant que le type de bâtiment spécifique ne soit surpeuplé. Assures-toi que ce nombre reste supérieur à tes visiteurs quotidiens.<br>Tous les touristes n\'ont pas besoin d\'une location, d\'un restaurant ou d\'une assistance médicale; C\'est pourquoi les deux dernières colonnes diffèrent dans la plupart des cas.';

$lang['building']['rush_completed']		= '<div class="alert alert-success text-center">Tu as réussi à accélérer la construction/amélioration du bâtiment.</div>';
$lang['building']['already_completed']		= '<div class="alert alert-warning text-center">La construction/amélioration est déjà terminée.</div>';
$lang['building']['not_enough_genepis']		= '<div class="alert alert-warning text-center">Tu n\'as pas assez de Génépis pour réaliser cette action.</div>';

// Enneigement par piste
$lang['building']['trail_sm_title']              = 'Gestion de l\'enneigement';
$lang['building']['trail_sm_intro']              = 'Gérez les opérations d\'enneigement de votre station. Surveillez le niveau de neige, contrôlez les canons à neige, définissez un mode de fonctionnement et consultez la production prévue ce soir.';
$lang['building']['trail_sm_summary_title']      = 'Résumé des équipements actifs';
$lang['building']['trail_sm_total_output']       = 'Production de neige totale par nuit';
$lang['building']['trail_sm_total_daily_cost']   = 'Coût quotidien total de fonctionnement';
$lang['building']['trail_sm_night']              = 'nuit';
$lang['building']['trail_sm_catalogue_title']    = 'Types d\'équipements';
$lang['building']['trail_sm_brands_title']       = 'Marques';
$lang['building']['trail_sm_trails_title']       = 'Gestion de l\'enneigement par piste';
$lang['building']['trail_sm_col_trail']          = 'Piste';
$lang['building']['trail_sm_col_type']           = 'Type';
$lang['building']['trail_sm_col_equip']          = 'Équipement';
$lang['building']['trail_sm_col_brand']          = 'Marque';
$lang['building']['trail_sm_col_output']         = 'Production de neige';
$lang['building']['trail_sm_col_daily']          = 'Coût quotidien';
$lang['building']['trail_sm_col_status']         = 'Statut';
$lang['building']['trail_sm_col_actions']        = 'Actions';
$lang['building']['trail_sm_col_purchase']       = 'Coût d\'achat';
$lang['building']['trail_sm_col_output_mult']    = 'Production';
$lang['building']['trail_sm_col_cost_mult']      = 'Coût';
$lang['building']['trail_sm_none']               = 'Aucun équipement';
$lang['building']['trail_sm_no_slopes']          = 'Tu n\'as pas encore de pistes. Construis d\'abord des pistes pour ajouter des équipements d\'enneigement.';
$lang['building']['trail_sm_type_label']         = 'Type';
$lang['building']['trail_sm_brand_label']        = 'Marque';
$lang['building']['trail_sm_buy']                = 'Acheter';
$lang['building']['trail_sm_active']             = 'Actif';
$lang['building']['trail_sm_inactive']           = 'Inactif';
$lang['building']['trail_sm_start']              = 'Démarrer';
$lang['building']['trail_sm_stop']               = 'Arrêter';
$lang['building']['trail_sm_remove']             = 'Supprimer';
$lang['building']['trail_sm_confirm_remove']     = 'Es-tu sûr de vouloir supprimer cet équipement d\'enneigement ?';
$lang['building']['trail_sm_purchased']          = '<div class="alert alert-success text-center">L\'équipement d\'enneigement a été acheté et installé avec succès.</div>';
$lang['building']['trail_sm_purchased_log']      = 'Achat d\'équipement d\'enneigement par piste.';
$lang['building']['trail_sm_already_equipped']   = '<div class="alert alert-warning text-center">Cette piste a déjà un équipement d\'enneigement. Supprime l\'équipement existant d\'abord.</div>';
$lang['building']['trail_sm_bad_type']           = '<div class="alert alert-danger text-center">Type d\'équipement ou marque invalide sélectionné.</div>';
$lang['building']['trail_sm_started']            = '<div class="alert alert-success text-center">L\'équipement d\'enneigement a été démarré.</div>';
$lang['building']['trail_sm_stopped']            = '<div class="alert alert-warning text-center">L\'équipement d\'enneigement a été arrêté.</div>';
$lang['building']['trail_sm_removed']            = '<div class="alert alert-success text-center">L\'équipement d\'enneigement a été supprimé de la piste.</div>';
// Merged snowmaking page
$lang['building']['trail_sm_cannon_section_title']   = 'Canons à neige (tout le domaine)';
$lang['building']['trail_sm_cannon_link']            = 'Gérer les canons à neige';
$lang['building']['trail_sm_cannon_none']            = 'Aucun canon à neige construit. <a href="%s">Construire des canons à neige</a> pour ajouter une capacité d\'enneigement sur tout le domaine.';
$lang['building']['trail_sm_cannon_active']          = 'Canons actifs';
$lang['building']['trail_sm_cannon_total_output']    = 'Production totale des canons';
$lang['building']['trail_sm_section_title']          = 'Équipements d\'enneigement par piste';
// Temperature feature
$lang['building']['trail_sm_temp_col']               = 'Temp. requise';
$lang['building']['trail_sm_temp_below_freezing']    = '≤ 0 °C (sous le point de gel)';
$lang['building']['trail_sm_temp_any']               = 'Toute température';
$lang['building']['trail_sm_temp_warning']           = 'Météo au-dessus de zéro : les canons à neige ne peuvent pas produire de neige cette nuit.';
$lang['building']['trail_sm_equip_suspended']        = 'Suspendu (trop chaud)';
// Brand why-choose section
$lang['building']['trail_sm_brand_why_title']        = 'Pourquoi choisir chaque marque ?';
$lang['building']['trail_sm_col_brand_desc']         = 'À propos de la marque';
$lang['building']['trail_sm_col_why_choose']         = 'Pourquoi la choisir ?';
// Bulk actions and new features
$lang['building']['trail_sm_start_all']              = 'Tout démarrer';
$lang['building']['trail_sm_stop_all']               = 'Tout arrêter';
$lang['building']['trail_sm_all_started']            = '<div class="alert alert-success text-center">Tous les équipements d\'enneigement par piste ont été démarrés.</div>';
$lang['building']['trail_sm_all_stopped']            = '<div class="alert alert-warning text-center">Tous les équipements d\'enneigement par piste ont été arrêtés.</div>';
$lang['building']['trail_sm_upgrade']                = 'Mettre à niveau';
$lang['building']['trail_sm_upgrade_label']          = 'Changer l\'équipement / la marque';
$lang['building']['trail_sm_upgraded']               = '<div class="alert alert-success text-center">L\'équipement d\'enneigement par piste a été mis à niveau avec succès.</div>';
$lang['building']['trail_sm_upgraded_log']           = 'Mise à niveau de l\'équipement d\'enneigement par piste.';
// Snow level on snowmaking page
$lang['building']['trail_sm_snow_level_title']       = 'Niveau de neige actuel';
$lang['building']['snow_target_label']               = 'Objectif de neige (arrêter l\'enneigement quand atteint)';
// Guest Skill Progression
$lang['building']['guest_skill_title']                  = 'Progression des compétences des visiteurs';
$lang['building']['guest_skill_page_intro']             = 'Au fil des saisons, tes visiteurs améliorent leur niveau de ski. Les plus expérimentés dépensent davantage, boostant tes revenus de skipass.';
$lang['building']['guest_skill_distribution_title']     = 'Répartition des niveaux des visiteurs';
$lang['building']['guest_skill_seasons_played_label']   = 'Saisons complétées : ';
$lang['building']['guest_skill_level']                  = 'Niveau';
$lang['building']['guest_skill_share']                  = 'Part';
$lang['building']['guest_skill_revenue_bonus_label']    = 'Bonus de revenus par visiteur';
$lang['building']['guest_skill_beginner']               = 'Débutant';
$lang['building']['guest_skill_intermediate']           = 'Intermédiaire';
$lang['building']['guest_skill_advanced']               = 'Avancé';
$lang['building']['guest_skill_current_multiplier']     = 'Multiplicateur actuel de revenus skipass :';
$lang['building']['guest_skill_how_it_works']           = 'Comment ça marche';
$lang['building']['guest_skill_levelup_beginner']       = 'Chaque saison, %d%% des visiteurs débutants progressent au niveau Intermédiaire.';
$lang['building']['guest_skill_levelup_intermediate']   = 'Chaque saison, %d%% des visiteurs intermédiaires progressent au niveau Avancé.';
$lang['building']['guest_skill_loyalty_note']           = "Les visiteurs avancés apportent une fidélité à long terme : plus de dépenses en skipass, des retours réguliers et un système d'abonnement saisonnier renforcé.";

// Lift Line Management
$lang['building']['lift_line_title']              = 'Gestion des files de remontées';
$lang['building']['lift_line_page_intro']         = 'Gérez comment votre station gère les files d\'attente des remontées mécaniques. Définissez une tolérance d\'attente, activez une file VIP et réduisez les risques de panne liés à la surcharge.';
$lang['building']['lift_line_how_it_works']       = 'Comment ça fonctionne';
$lang['building']['lift_line_how_it_works_desc']  = 'Chaque nuit, le jeu calcule le temps d\'attente moyen dans les files. Si ce temps dépasse votre seuil de tolérance, votre station perd de la réputation. Une file VIP permet à une partie des visiteurs de passer en priorité, réduisant la pénalité. Les remontées en surcharge ont également un risque de tomber en maintenance.';
$lang['building']['lift_line_mechanic_queue']     = 'Le temps de file est estimé d\'après le débit journalier de vos remontées par rapport au nombre total de visiteurs.';
$lang['building']['lift_line_mechanic_vip']       = 'File VIP : ' . (int)(LIFT_LINE_VIP_BYPASS_RATIO * 100) . '% des visiteurs paient le forfait prioritaire et évitent la file, réduisant la pénalité de réputation de ' . (int)(LIFT_LINE_VIP_REP_REDUCTION * 100) . '%.';
$lang['building']['lift_line_mechanic_breakdown'] = 'Les remontées en surcharge (plus de ' . (int)(LIFT_LINE_OVERLOAD_RATIO * 100) . '% de leur capacité) ont ' . LIFT_LINE_BREAKDOWN_CHANCE . '% de chance par jour d\'être forcées en maintenance.';
$lang['building']['lift_line_mechanic_reputation'] = 'Perte de réputation : ' . LIFT_LINE_REP_PENALTY_PER_MIN . ' point par minute de file au-delà de la tolérance, jusqu\'à ' . LIFT_LINE_MAX_REP_PENALTY . ' points par jour.';
$lang['building']['lift_line_settings_title']     = 'Paramètres de file';
$lang['building']['lift_line_tolerance_label']    = 'Tolérance de file';
$lang['building']['lift_line_tolerance_help']     = 'Les visiteurs acceptent d\'attendre ce nombre de minutes avant de partir. Plus c\'est bas, plus la pénalité est sévère si les files sont longues.';
$lang['building']['lift_line_vip_enable_label']   = 'Activer la file VIP prioritaire';
$lang['building']['lift_line_vip_help']           = 'Quand activée, une partie des visiteurs paient pour éviter la file. Cela réduit les pénalités de réputation liées à l\'attente.';
$lang['building']['lift_line_vip_price_label']    = 'Prix du forfait VIP';
$lang['building']['lift_line_vip_per_guest']      = 'visiteur/jour';
$lang['building']['lift_line_save_btn']           = 'Enregistrer les paramètres';
$lang['building']['lift_line_key_figures']        = 'Paramètres actuels';
$lang['building']['lift_line_vip_status_label']   = 'File VIP';
$lang['building']['lift_line_vip_on']             = 'ACTIVÉE';
$lang['building']['lift_line_vip_off']            = 'DÉSACTIVÉE';
$lang['building']['lift_line_rep_penalty_label']  = 'Pénalité de réputation';
$lang['building']['lift_line_rep_penalty_desc']   = LIFT_LINE_REP_PENALTY_PER_MIN . ' pt / min au-delà de la tolérance (max ' . LIFT_LINE_MAX_REP_PENALTY . ' pt/jour)';
$lang['building']['lift_line_breakdown_label']    = 'Risque de panne';
$lang['building']['lift_line_breakdown_desc']     = LIFT_LINE_BREAKDOWN_CHANCE . '% chance/jour si la remontée est en surcharge';
$lang['building']['lift_line_settings_saved']     = '<div class="alert alert-success text-center">Paramètres de file enregistrés avec succès.</div>';
$lang['building']['lift_line_invalid_settings']   = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['lift_line_save_error']         = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';
// ============================================================
// Système de gestion de la fréquentation
// ============================================================
$lang['building']['crowding_title']                 = 'Gestion de la fréquentation';
$lang['building']['crowding_page_intro']            = 'Contrôlez le nombre de visiteurs que votre station peut accueillir chaque jour. Définissez une limite de capacité, un seuil d\'alerte de fréquentation et activez l\'entrée programmée pour protéger l\'expérience de vos visiteurs et votre réputation.';
$lang['building']['crowding_how_it_works']          = 'Comment ça fonctionne';
$lang['building']['crowding_how_it_works_desc']     = 'Chaque nuit, le jeu compare vos visiteurs quotidiens à votre limite de capacité. Si la fréquentation dépasse votre seuil d\'alerte, votre station perd de la réputation. L\'activation de l\'entrée programmée divise la pénalité par deux et génère un petit bonus de réputation lorsque la station est bien gérée.';
$lang['building']['crowding_mechanic_threshold']    = 'Seuil d\'alerte : la pénalité s\'applique quand les visiteurs dépassent ce pourcentage de votre limite de capacité.';
$lang['building']['crowding_mechanic_timed_entry']  = 'Entrée programmée : réduit la fréquentation effective, divise la pénalité de réputation par deux et génère un bonus de ' . CROWDING_TIMED_ENTRY_REP_BONUS . ' points lorsque la fréquentation est bien gérée.';
$lang['building']['crowding_mechanic_reputation']   = 'Perte de réputation : ' . CROWDING_REP_PENALTY_PER_PCT . ' point par % de visiteurs au-delà du seuil, jusqu\'à ' . CROWDING_MAX_REP_PENALTY . ' points par jour.';
$lang['building']['crowding_mechanic_bonus']        = 'Bonus de réputation : +' . CROWDING_TIMED_ENTRY_REP_BONUS . ' réputation lorsque l\'entrée programmée est activée et la fréquentation reste sous le seuil.';
$lang['building']['crowding_settings_title']        = 'Paramètres de fréquentation';
$lang['building']['crowding_capacity_label']        = 'Limite de capacité journalière';
$lang['building']['crowding_capacity_help']         = 'Nombre maximum de visiteurs ciblé par jour. Les pénalités s\'appliquent quand ce nombre est dépassé au-delà du seuil d\'alerte.';
$lang['building']['crowding_threshold_label']       = 'Seuil d\'alerte de fréquentation';
$lang['building']['crowding_threshold_help']        = 'La pénalité de réputation commence quand les visiteurs quotidiens atteignent ce pourcentage de la limite de capacité. Plus c\'est élevé, plus la tolérance à la surcharge est grande.';
$lang['building']['crowding_timed_entry_label']     = 'Activer l\'entrée programmée';
$lang['building']['crowding_timed_entry_help']      = 'Quand activée, la station gère le flux des visiteurs pour réduire la surcharge. Divise les pénalités par deux et génère un bonus si la fréquentation est maîtrisée.';
$lang['building']['crowding_save_btn']              = 'Enregistrer les paramètres';
$lang['building']['crowding_key_figures']           = 'Paramètres actuels';
$lang['building']['crowding_visitors_per_day']      = 'visiteurs/jour';
$lang['building']['crowding_timed_entry_status_label'] = 'Entrée programmée';
$lang['building']['crowding_timed_entry_on']        = 'ACTIVÉE';
$lang['building']['crowding_timed_entry_off']       = 'DÉSACTIVÉE';
$lang['building']['crowding_rep_penalty_label']     = 'Pénalité de réputation';
$lang['building']['crowding_rep_penalty_desc']      = CROWDING_REP_PENALTY_PER_PCT . ' pt / % au-delà du seuil (max ' . CROWDING_MAX_REP_PENALTY . ' pt/jour)';
$lang['building']['crowding_rep_bonus_label']       = 'Bonus de réputation';
$lang['building']['crowding_rep_bonus_desc']        = '+' . CROWDING_TIMED_ENTRY_REP_BONUS . ' pt quand l\'entrée programmée est activée et la fréquentation reste sous le seuil';
$lang['building']['crowding_settings_saved']        = '<div class="alert alert-success text-center">Paramètres de fréquentation enregistrés avec succès.</div>';
$lang['building']['crowding_invalid_settings']      = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['crowding_save_error']            = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';
// ============================================================
// Système environnemental
// ============================================================
$lang['building']['env_title']              = 'Gestion environnementale';
$lang['building']['env_page_intro']         = 'Surveillez et améliorez l\'impact environnemental de votre station. Maintenez les émissions de carbone et la pollution sonore basses pour éviter les amendes et les restrictions d\'expansion, et investissez dans des technologies vertes pour améliorer votre réputation écologique.';

// Cartes de statut
$lang['building']['env_eco_reputation_title'] = 'Réputation écologique';
$lang['building']['env_eco_reputation_desc']  = 'Votre réputation environnementale globale (0–100). Améliorée par les investissements verts et dégradée par une pollution élevée.';
$lang['building']['env_carbon_title']         = 'Empreinte carbone';
$lang['building']['env_noise_title']          = 'Pollution sonore';

// Seuils / conséquences
$lang['building']['env_carbon_fine_at']       = 'Amende journalière au-dessus de';
$lang['building']['env_carbon_fine_desc']     = 'amende de 500 €';
$lang['building']['env_carbon_restrict_at']   = 'Expansion restreinte au-dessus de';
$lang['building']['env_carbon_restrict_desc'] = 'amende de 1 000 € + nouvelles constructions bloquées';
$lang['building']['env_expansion_restricted_warning'] = '<strong>Expansion restreinte !</strong> Votre empreinte carbone est trop élevée. Réduisez la pollution pour débloquer les nouvelles constructions.';
$lang['building']['env_noise_fine_at']        = 'Amende déclenchée au-dessus de';
$lang['building']['env_noise_fine_desc']      = 'amende de 300 € lorsque la zone naturelle est active';

// Zone naturelle
$lang['building']['env_wildlife_title']   = 'Zone de protection naturelle';
$lang['building']['env_wildlife_desc']    = 'Désignez une partie de votre station comme zone de protection naturelle. Améliore la réputation écologique (+5) mais augmente les amendes si le niveau sonore est élevé.';
$lang['building']['env_wildlife_on']      = 'Zone naturelle : ACTIVE';
$lang['building']['env_wildlife_off']     = 'Zone naturelle : INACTIVE';
$lang['building']['env_wildlife_enable']  = 'Activer la zone naturelle';
$lang['building']['env_wildlife_disable'] = 'Désactiver la zone naturelle';
$lang['building']['env_wildlife_zone_enabled']  = '<div class="alert alert-success text-center">La zone de protection naturelle a été activée.</div>';
$lang['building']['env_wildlife_zone_disabled'] = '<div class="alert alert-warning text-center">La zone de protection naturelle a été désactivée.</div>';

// Investissements verts
$lang['building']['env_investments_title'] = 'Investissements verts';

// Panneaux solaires
$lang['building']['env_solar_title']       = 'Panneaux solaires';
$lang['building']['env_solar_desc']        = 'Installez des panneaux solaires sur vos infrastructures. Réduit l\'empreinte carbone de 20 % et améliore la réputation écologique (+5).';
$lang['building']['env_solar_active']      = 'Panneaux solaires installés';
$lang['building']['env_solar_cost_label']  = 'Coût d\'investissement';
$lang['building']['env_solar_buy']         = 'Installer des panneaux solaires';
$lang['building']['env_solar_installed']   = '<div class="alert alert-success text-center">Les panneaux solaires ont été installés. Votre empreinte carbone diminuera dès cette nuit.</div>';
$lang['building']['env_solar_already_installed'] = '<div class="alert alert-info text-center">Les panneaux solaires sont déjà installés.</div>';

// Dameuses électriques
$lang['building']['env_electric_groomer_title']       = 'Dameuses électriques';
$lang['building']['env_electric_groomer_desc']        = 'Achetez une dameuse électrique. Elle produit beaucoup moins de carbone et de bruit qu\'une dameuse diesel (+2 CO₂e vs +10, +1 dB vs +5).';
$lang['building']['env_electric_groomer_owned']       = 'Dameuses électriques possédées';
$lang['building']['env_electric_groomer_cost_label']  = 'Coût par dameuse';
$lang['building']['env_electric_groomer_buy']         = 'Acheter une dameuse électrique';
$lang['building']['env_electric_groomer_purchased']   = '<div class="alert alert-success text-center">Dameuse électrique achetée. Elle contribuera à réduire l\'empreinte carbone dès cette nuit.</div>';

// Fonds insuffisants
$lang['building']['env_not_enough_cash'] = '<div class="alert alert-warning text-center">Fonds insuffisants pour effectuer cet achat.</div>';

// Tableau de calcul
$lang['building']['env_how_calculated_title'] = 'Comment est-ce calculé ?';
$lang['building']['env_source']               = 'Source';
$lang['building']['env_carbon_impact']        = 'Impact carbone';
$lang['building']['env_noise_impact']         = 'Impact sonore';
$lang['building']['env_source_lift']          = 'Télésièges actifs';
$lang['building']['env_source_cannon']        = 'Canon à neige actif';
$lang['building']['env_source_groomer']       = 'Dameuse diesel';
$lang['building']['env_source_electric_groomer'] = 'Dameuse électrique';
$lang['building']['env_source_solar']         = 'Panneaux solaires';
$lang['building']['env_source_wildlife']      = 'Zone naturelle';
$lang['building']['env_source_wildlife_noise_note'] = 'Amende si bruit > seuil';
$lang['building']['env_source_reforestation']      = 'Reboisement';
$lang['building']['env_source_reforestation_unit'] = 'lot';
$lang['building']['env_source_water_recycling']    = 'Système de recyclage de l\'eau';
$lang['building']['env_source_water_recycling_note'] = '(bruit des canons)';
$lang['building']['env_updated_nightly']      = 'Les valeurs sont recalculées chaque nuit par le système.';

// Certification verte
$lang['building']['env_green_cert_badge']    = 'Certifié Vert';
$lang['building']['env_green_cert_desc']     = 'Accordé lorsque la réputation écologique atteint 80 ou plus.';
$lang['building']['env_green_cert_achieved'] = 'Votre station a obtenu la <strong>Certification Verte</strong> ! Les visiteurs apprécient votre engagement environnemental.';

// Programme de reboisement
$lang['building']['env_reforestation_title']       = 'Programme de reboisement';
$lang['building']['env_reforestation_desc']        = 'Plantez des arbres autour de votre station. Chaque lot absorbe du CO₂ (-'.ENV_TREE_CARBON_REDUCTION.' CO₂e/nuit) et améliore la réputation écologique (+'.ENV_TREE_REP_BONUS.'). Maximum '.ENV_MAX_TREE_COUNT.' lots.';
$lang['building']['env_reforestation_owned']       = 'Lots plantés';
$lang['building']['env_reforestation_cost_label']  = 'Coût par lot';
$lang['building']['env_reforestation_buy']         = 'Planter des arbres';
$lang['building']['env_reforestation_max']         = 'Reboisement maximum atteint';
$lang['building']['env_reforestation_planted']     = '<div class="alert alert-success text-center">Arbres plantés ! Votre empreinte carbone diminuera dès cette nuit.</div>';
$lang['building']['env_reforestation_max_reached'] = '<div class="alert alert-info text-center">Vous avez atteint le nombre maximum de lots de reboisement.</div>';

// Système de recyclage de l'eau
$lang['building']['env_water_recycling_title']             = 'Système de recyclage de l\'eau';
$lang['building']['env_water_recycling_desc']              = 'Installez un système de recyclage qui collecte la neige fondue et la réutilise pour les canons à neige. Réduit le bruit des canons de '.(int)(ENV_WATER_RECYCLING_NOISE_REDUCTION * 100).' % et améliore la réputation écologique (+'.ENV_WATER_RECYCLING_REP_BONUS.').';
$lang['building']['env_water_recycling_active']            = 'Recyclage de l\'eau installé';
$lang['building']['env_water_recycling_cost_label']        = 'Coût d\'investissement';
$lang['building']['env_water_recycling_buy']               = 'Installer le recyclage de l\'eau';
$lang['building']['env_water_recycling_installed']         = '<div class="alert alert-success text-center">Système de recyclage de l\'eau installé. Le bruit des canons diminuera dès cette nuit.</div>';
$lang['building']['env_water_recycling_already_installed'] = '<div class="alert alert-info text-center">Le système de recyclage de l\'eau est déjà installé.</div>';

// ---------------------------------------------------------------
// Gestion Hors-Saison
// ---------------------------------------------------------------
$lang['off_season']['title']              = 'Activités hors-saison';
$lang['off_season']['title_sing']         = 'Activité hors-saison';
$lang['off_season']['intro']              = 'Fais prospérer ta station tout au long de l\'année ! Construis des activités estivales pour générer des revenus même lorsque les pistes sont fermées. Ces équipements attirent des visiteurs hors-saison et réduisent la dépendance aux revenus hivernaux.';
$lang['off_season']['max_daily_income']   = 'Revenu journalier max';

// VTT
$lang['mountain_biking']['title']         = 'VTT';
$lang['mountain_biking']['title_sing']    = 'Installation VTT';
$lang['mountain_biking']['desc']          = 'Les pistes et parcs de VTT attirent les amateurs de sensations fortes pendant les mois d\'été. Construis un réseau de pistes pour accueillir les passionnés et générer des revenus hors-saison.<br>Tu dois construire l\'office du tourisme avant de construire des installations VTT.';

// Randonnée
$lang['hiking']['title']                  = 'Randonnée';
$lang['hiking']['title_sing']             = 'Sentier de randonnée';
$lang['hiking']['desc']                   = 'Les sentiers de randonnée ouvrent ta station aux amoureux de la nature pendant l\'été. Un réseau de randonnées bien développé attire les familles et les amateurs de plein air, générant des revenus hors-saison réguliers.<br>Tu dois construire l\'office du tourisme avant de construire des sentiers de randonnée.';

// Festival
$lang['festival']['title']                = 'Festivals';
$lang['festival']['title_sing']           = 'Lieu de festival';
$lang['festival']['desc']                 = 'Les terrains de festival et les scènes en plein air transforment ta station en hub estival de divertissement. Organiser des concerts, des festivals gastronomiques et des événements culturels attire de grandes foules et augmente considérablement les revenus hors-saison.<br>Tu dois construire l\'office du tourisme avant de construire des lieux de festival.';

// Lieu de mariage
$lang['wedding_venue']['title']           = 'Mariages';
$lang['wedding_venue']['title_sing']      = 'Salle de mariage';
$lang['wedding_venue']['desc']            = 'Le cadre montagnard époustouflant fait de ta station la destination parfaite pour les mariages. Des cérémonies intimistes en jardin aux célébrations alpines de luxe, les lieux de mariage génèrent des revenus hors-saison premium avec une clientèle réduite mais à forte dépense.<br>Tu dois construire l\'office du tourisme avant de construire des salles de mariage.';

// Luge alpine
$lang['alpine_coaster']['title']          = 'Luge alpine';
$lang['alpine_coaster']['title_sing']     = 'Luge alpine';
$lang['alpine_coaster']['desc']           = 'Une luge alpine est une attraction estivale palpitante adorée des familles et des amateurs d\'adrénaline. Fonctionnant sur des rails fixes le long de la montagne, elle opère du printemps à l\'automne et génère des revenus hors-saison réguliers.<br>Tu dois construire l\'office du tourisme avant de construire une luge alpine.';
// Système Mountain Master Plan
$lang['building']['plan_title']                    = 'Plan directeur de la montagne';
$lang['building']['plan_intro']                    = 'Avant d\'entreprendre des expansions majeures, définissez une stratégie de développement à long terme pour votre station. Votre plan doit recevoir l\'approbation du gouvernement avant d\'entrer en vigueur.';
$lang['building']['plan_how_it_works']             = 'Comment ça marche';
$lang['building']['plan_how_it_works_desc']        = 'Le Plan directeur de la montagne définit votre vision de développement sur 5 ans, couvrant les limites de zonage, les considérations environnementales et les objectifs d\'infrastructure.';
$lang['building']['plan_step_draft']               = 'Rédigez votre plan : nom, stratégie d\'expansion, notes environnementales et limites de zonage.';
$lang['building']['plan_step_submit']              = 'Soumettez pour examen gouvernemental : coût';
$lang['building']['plan_step_review']              = 'Délai d\'examen gouvernemental';
$lang['building']['plan_step_activate']            = 'Activez le plan approuvé pour commencer votre expansion.';
$lang['building']['plan_step_expire']              = 'Les plans expirent automatiquement après';
$lang['building']['plan_days']                     = 'jours';
$lang['building']['plan_revision_warning']         = 'Attention :';
$lang['building']['plan_revision_desc']            = 'Modifier un plan après approbation coûte';
$lang['building']['plan_reputation']               = 'réputation';
$lang['building']['plan_create_new']               = '+ Nouveau plan';
$lang['building']['plan_none']                     = 'Vous n\'avez pas encore de plan de développement. Créez votre premier plan pour commencer.';
$lang['building']['plan_new_title']                = 'Créer un nouveau Plan directeur de la montagne';
$lang['building']['plan_edit_title']               = 'Modifier le Plan directeur de la montagne';
$lang['building']['plan_field_name']               = 'Nom du plan';
$lang['building']['plan_field_name_help']          = 'Donnez à votre plan un nom concis et descriptif (100 caractères max).';
$lang['building']['plan_expansion_strategy']       = 'Stratégie d\'expansion sur 5 ans';
$lang['building']['plan_expansion_strategy_help']  = 'Décrivez les objectifs de développement à long terme : nouvelles pistes, remontées, bâtiments et infrastructures prévus sur les 5 prochaines années.';
$lang['building']['plan_environmental_notes']      = 'Notes d\'approbation environnementale';
$lang['building']['plan_environmental_notes_help'] = 'Décrivez les mesures environnementales que vous prendrez : protection de la faune, limites de déforestation, impact de l\'enneigement artificiel, etc.';
$lang['building']['plan_zoning_limits']            = 'Limites de zonage (ajouts maximum sur 5 ans)';
$lang['building']['plan_zoning_slopes']            = 'Pistes';
$lang['building']['plan_zoning_lifts']             = 'Remontées';
$lang['building']['plan_zoning_buildings']         = 'Bâtiments';
$lang['building']['plan_zoning_limits_help']       = 'Définissez le nombre maximum de nouvelles pistes, remontées et bâtiments autorisés dans ce plan.';
$lang['building']['plan_btn_save']                 = 'Enregistrer le plan';
$lang['building']['plan_btn_edit']                 = 'Modifier';
$lang['building']['plan_btn_submit']               = 'Soumettre pour examen';
$lang['building']['plan_btn_delete']               = 'Supprimer';
$lang['building']['plan_btn_activate']             = 'Activer le plan';
$lang['building']['plan_btn_revise']               = 'Réviser le plan';
$lang['building']['plan_btn_withdraw']             = 'Retirer';
$lang['building']['plan_btn_duplicate']            = 'Dupliquer';
$lang['building']['plan_confirm_submit']           = 'Soumettre ce plan pour examen gouvernemental ?';
$lang['building']['plan_confirm_delete']           = 'Supprimer ce plan brouillon ? Cette action est irréversible.';
$lang['building']['plan_confirm_activate']         = 'Activer ce plan approuvé ?';
$lang['building']['plan_confirm_revise']           = 'Réviser ce plan ? Il redeviendra brouillon et des pénalités de coût et de réputation s\'appliqueront.';
$lang['building']['plan_confirm_withdraw']         = 'Retirer ce plan de l\'examen gouvernemental ? Les frais de soumission ne sont pas remboursables.';
$lang['building']['plan_confirm_duplicate']        = 'Créer une nouvelle copie brouillon de ce plan ?';
$lang['building']['plan_status_draft']             = 'Brouillon';
$lang['building']['plan_status_submitted']         = 'Soumis';
$lang['building']['plan_status_approved']          = 'Approuvé';
$lang['building']['plan_status_active']            = 'Actif';
$lang['building']['plan_status_expired']           = 'Expiré';
$lang['building']['plan_revised_badge']            = 'Révisé';
$lang['building']['plan_created_on']               = 'Créé le';
$lang['building']['plan_submitted_on']             = 'Soumis le';
$lang['building']['plan_approved_on']              = 'Approuvé le';
$lang['building']['plan_activated_on']             = 'Activé le';
$lang['building']['plan_expires_on']               = 'Expire le :';
$lang['building']['plan_expired_on']               = 'Expiré le :';
$lang['building']['plan_review_pending']           = 'Examen gouvernemental en cours. Approbation sous';
$lang['building']['plan_currently_active']         = '✓ Actuellement actif';
// Messages de retour
$lang['building']['plan_created']                  = '<div class="alert alert-success text-center">Votre plan de développement a été créé avec succès.</div>';
$lang['building']['plan_saved']                    = '<div class="alert alert-success text-center">Votre plan de développement a été enregistré.</div>';
$lang['building']['plan_deleted']                  = '<div class="alert alert-success text-center">Le plan brouillon a été supprimé.</div>';
$lang['building']['plan_submitted']                = '<div class="alert alert-success text-center">Votre plan a été soumis pour examen gouvernemental. L\'examen prend jusqu\'à ' . MASTER_PLAN_APPROVAL_DAYS . ' jours.</div>';
$lang['building']['plan_activated']                = '<div class="alert alert-success text-center">Votre plan de développement est maintenant actif. Bonne chance pour votre expansion !</div>';
$lang['building']['plan_revised']                  = '<div class="alert alert-warning text-center">Votre plan a été remis en brouillon pour révision. Les pénalités ont été appliquées.</div>';
$lang['building']['plan_withdrawn']                = '<div class="alert alert-warning text-center">Votre plan a été retiré de l\'examen gouvernemental et remis en brouillon. Les frais de soumission ne sont pas remboursables.</div>';
$lang['building']['plan_duplicated']               = '<div class="alert alert-success text-center">Une nouvelle copie brouillon de votre plan a été créée.</div>';
$lang['building']['plan_not_editable']             = '<div class="alert alert-danger text-center">Ce plan ne peut pas être modifié. Seuls les brouillons peuvent être modifiés.</div>';
$lang['building']['plan_not_submittable']          = '<div class="alert alert-danger text-center">Ce plan ne peut pas être soumis. Seuls les brouillons peuvent être soumis.</div>';
$lang['building']['plan_not_activatable']          = '<div class="alert alert-danger text-center">Ce plan ne peut pas être activé. Seuls les plans approuvés peuvent être activés.</div>';
$lang['building']['plan_not_revisable']            = '<div class="alert alert-danger text-center">Ce plan ne peut pas être révisé. Seuls les plans approuvés ou actifs peuvent être révisés.</div>';
$lang['building']['plan_not_deletable']            = '<div class="alert alert-danger text-center">Ce plan ne peut pas être supprimé. Seuls les brouillons peuvent être supprimés.</div>';
$lang['building']['plan_not_withdrawable']         = '<div class="alert alert-danger text-center">Ce plan ne peut pas être retiré. Seuls les plans soumis peuvent être retirés.</div>';
$lang['building']['plan_not_enough_cash']          = '<div class="alert alert-danger text-center">Vous n\'avez pas assez de liquidités pour effectuer cette action.</div>';
$lang['building']['plan_validation_error']         = '<div class="alert alert-danger text-center">Données du plan invalides. Veuillez vérifier tous les champs et réessayer.</div>';
// Messages de journal
$lang['building']['plan_log_created']              = 'Création d\'un nouveau Plan directeur de la montagne.';
$lang['building']['plan_log_submitted']            = 'Soumission du Plan directeur pour examen gouvernemental.';
$lang['building']['plan_log_activated']            = 'Activation du Plan directeur de la montagne.';
$lang['building']['plan_log_revised']              = 'Révision du Plan directeur de la montagne (pénalités appliquées).';
$lang['building']['plan_log_withdrawn']            = 'Retrait du Plan directeur de l\'examen gouvernemental.';
$lang['building']['plan_log_duplicated']           = 'Duplication du Plan directeur de la montagne en nouveau brouillon.';

// ---------------------------------------------------------------------------
// Système d'énergie et d'électricité
// ---------------------------------------------------------------------------
$lang['building']['energy_title']             = 'Gestion de l\'énergie';
$lang['building']['energy_page_intro']        = 'Gérez la production et la consommation d\'électricité de votre station. Les remontées mécaniques et les canons à neige consomment de l\'énergie chaque jour. Construisez des sources d\'énergie renouvelable pour réduire votre facture de réseau.';

// Section bilan
$lang['building']['energy_balance_title']     = 'Bilan énergétique quotidien';
$lang['building']['energy_consumption_label'] = 'Consommation';
$lang['building']['energy_production_label']  = 'Production';
$lang['building']['energy_lifts_label']       = 'Remontées mécaniques (ouvertes)';
$lang['building']['energy_cannons_label']     = 'Canons à neige (actifs)';
$lang['building']['energy_total_consumption'] = 'Consommation totale';
$lang['building']['energy_solar_label']       = 'Panneaux solaires';
$lang['building']['energy_hydro_label']       = 'Centrale hydroélectrique';
$lang['building']['energy_total_production']  = 'Production totale';
$lang['building']['energy_grid_section']      = 'Réseau électrique (secours)';
$lang['building']['energy_grid_kwh_label']    = 'Acheté sur le réseau';
$lang['building']['energy_grid_rate_label']   = 'Tarif réseau';
$lang['building']['energy_daily_grid_cost']   = 'Coût réseau quotidien';
$lang['building']['energy_daily_savings']     = 'Économies vs 100 % réseau';
$lang['building']['energy_unit_lifts']        = 'remontées';
$lang['building']['energy_unit_cannons']      = 'canons';
$lang['building']['energy_unit_panels']       = 'panneaux';
$lang['building']['energy_day']               = 'jour';
$lang['building']['energy_built']             = 'Construit';
$lang['building']['energy_not_built']         = 'Non construit';
$lang['building']['energy_night_skiing_note'] = 'Remarque : l\'électricité du ski nocturne est facturée séparément sur la page Ski nocturne.';

// Panneaux solaires
$lang['building']['energy_solar_manage_title']    = 'Panneaux solaires';
$lang['building']['energy_solar_desc']            = 'Chaque unité de panneaux solaires produit de l\'électricité propre chaque jour, réduisant votre facture réseau. Maximum ' . ENERGY_SOLAR_PANEL_MAX . ' unités.';
$lang['building']['energy_solar_current']         = 'Panneaux actuels';
$lang['building']['energy_solar_output_per_panel']= 'Production par panneau';
$lang['building']['energy_solar_cost_per_panel']  = 'Coût par panneau';
$lang['building']['energy_solar_buy_btn']         = 'Acheter 1 panneau solaire';
$lang['building']['energy_solar_sell_btn']        = 'Vendre 1 panneau solaire';
$lang['building']['energy_solar_sell_confirm']    = 'Vendre un panneau solaire avec un remboursement à 50 % ?';
$lang['building']['energy_refund']                = 'remboursement';
$lang['building']['energy_solar_max_reached']     = '<div class="alert alert-warning text-center">Vous avez atteint le nombre maximum de panneaux solaires (' . ENERGY_SOLAR_PANEL_MAX . ').</div>';
$lang['building']['energy_solar_none_to_sell']    = '<div class="alert alert-warning text-center">Vous n\'avez aucun panneau solaire à vendre.</div>';
$lang['building']['energy_solar_panel_bought']    = '<div class="alert alert-success text-center">Panneau solaire acheté avec succès.</div>';
$lang['building']['energy_solar_panel_sold']      = '<div class="alert alert-success text-center">Panneau solaire vendu. Le remboursement a été ajouté à votre trésorerie.</div>';

// Centrale hydroélectrique
$lang['building']['energy_hydro_manage_title']    = 'Centrale hydroélectrique';
$lang['building']['energy_hydro_desc']            = 'Une centrale hydroélectrique produit une grande quantité d\'électricité chaque jour grâce à l\'eau. Investissement unique, sans coût d\'exploitation.';
$lang['building']['energy_hydro_status_label']    = 'Statut';
$lang['building']['energy_hydro_output']          = 'Production quotidienne';
$lang['building']['energy_hydro_cost_label']      = 'Coût de construction';
$lang['building']['energy_hydro_build_btn']       = 'Construire la centrale hydroélectrique';
$lang['building']['energy_hydro_build_confirm']   = 'Construire la centrale hydroélectrique ? Il s\'agit d\'un investissement unique sans remboursement en cas de démolition.';
$lang['building']['energy_hydro_demolish_btn']    = 'Démolir la centrale hydroélectrique';
$lang['building']['energy_hydro_demolish_confirm']= 'Démolir la centrale hydroélectrique ? Vous ne recevrez aucun remboursement.';
$lang['building']['energy_hydro_already_built']   = '<div class="alert alert-warning text-center">La centrale hydroélectrique est déjà construite.</div>';
$lang['building']['energy_hydro_not_built']       = '<div class="alert alert-warning text-center">Il n\'y a pas de centrale hydroélectrique à démolir.</div>';
$lang['building']['energy_hydro_built']           = '<div class="alert alert-success text-center">Centrale hydroélectrique construite ! Elle commencera à produire de l\'électricité cette nuit.</div>';
$lang['building']['energy_hydro_demolished']      = '<div class="alert alert-success text-center">Centrale hydroélectrique démolie.</div>';

// Réseau électrique
$lang['building']['energy_grid_title']     = 'Réseau électrique';
$lang['building']['energy_grid_desc']      = 'Le réseau électrique est toujours disponible en secours. Vous êtes automatiquement facturé pour toute l\'électricité dont votre station a besoin au-delà de ce que vos propres sources produisent.';
$lang['building']['energy_grid_always_on'] = 'Toujours disponible';
// Realistic Snowmaking System — water reservoir, electricity, staff, temperature
$lang['building']['snowmaking_requirements_title']   = 'Conditions d\'enneigement';
$lang['building']['snowmaking_water_label']          = 'Réservoir d\'eau';
$lang['building']['snowmaking_water_low']            = '<div class="alert alert-warning">⚠️ Le réservoir d\'eau est faible ! La production de neige sera réduite. Les nuits de neige ou de pluie rechargent le réservoir automatiquement.</div>';
$lang['building']['snowmaking_water_empty']          = '<div class="alert alert-danger">🚫 Le réservoir d\'eau est vide ! Aucune neige artificielle ne sera produite ce soir. Attendez des précipitations pour le remplir.</div>';
$lang['building']['snowmaking_staff_label']          = 'Opérateurs d\'enneigement';
$lang['building']['snowmaking_staff_missing']        = '<div class="alert alert-danger">🚫 Aucun opérateur d\'enneigement embauché ! Embauchez au moins '.SNOWMAKING_MIN_STAFF.' "Opérateur d\'enneigement" pour faire fonctionner les canons. <a href="'.base_url().'hire_staff_controller">Aller à l\'embauche du personnel →</a></div>';
$lang['building']['snowmaking_staff_ok']             = 'Opérateurs d\'enneigement en service';
$lang['building']['snowmaking_electricity_label']    = 'Coût d\'électricité par nuit';
$lang['building']['snowmaking_temp_label']           = 'Condition de température';
$lang['building']['snowmaking_temp_ok']              = 'En dessous de zéro — production active';
$lang['building']['snowmaking_temp_blocked']         = '<div class="alert alert-warning">🌡️ Température au-dessus de zéro — les canons à neige ne peuvent pas produire de neige ce soir.</div>';
$lang['building']['snowmaking_cannon_elec_per']      = '€ par canon actif par nuit';
$lang['building']['snowmaking_trail_elec_per']       = '€ par équipement de piste actif par nuit';
$lang['building']['snowmaking_water_refill_info']    = 'Une fois acheté, le réservoir se recharge automatiquement : +'.SNOWMAKING_WATER_REFILL_SNOW.'% les nuits de neige, +'.SNOWMAKING_WATER_REFILL_RAIN.'% les nuits de pluie.';
$lang['building']['water_reservoir_not_purchased']   = '<div class="alert alert-danger">🚫 Aucun réservoir d\'eau ! Vous devez acheter un réservoir d\'eau avant que tout équipement d\'enneigement fonctionne. Voir la section "Réservoir d\'eau" ci-dessous.</div>';
$lang['building']['water_reservoir_buy_title']       = 'Réservoir d\'eau';
$lang['building']['water_reservoir_buy_desc']        = 'Un réservoir d\'eau est nécessaire pour toutes les opérations d\'enneigement (canons à neige). Sans lui, aucune neige artificielle ne peut être produite. Une fois acheté, il est permanent et se recharge automatiquement avec les précipitations.';
$lang['building']['water_reservoir_buy_cost']        = 'Coût d\'achat';
$lang['building']['water_reservoir_buy_btn']         = 'Acheter le réservoir d\'eau';
$lang['building']['water_reservoir_buy_confirm']     = 'Acheter un réservoir d\'eau pour '.number_format(WATER_RESERVOIR_COST, 0, ',', ' ').' € ? C\'est un coût unique.';
$lang['building']['water_reservoir_purchased']       = '<div class="alert alert-success">✅ Réservoir d\'eau acheté ! Vos équipements d\'enneigement peuvent maintenant fonctionner.</div>';
$lang['building']['water_reservoir_already_purchased'] = '<div class="alert alert-warning text-center">Le réservoir d\'eau est déjà acheté.</div>';
$lang['building']['water_reservoir_required_for_snowmaking'] = '<div class="alert alert-info">Achetez un réservoir d\'eau (voir section ci-dessus) pour débloquer les équipements d\'enneigement.</div>';
$lang['building']['water_reservoir_purchased_log']   = 'Réservoir d\'eau acheté.';
// Snowmaking Mode
$lang['building']['snowmaking_mode_title']           = 'Mode d\'enneigement';
$lang['building']['snowmaking_mode_intro']           = 'Choisissez le mode de fonctionnement de vos canons à neige. Le mode Éco réduit la production et économise les coûts ; le mode Boost maximise la production de neige à un coût électrique plus élevé. Le mode actif est mis en évidence.';
$lang['building']['snowmaking_mode_normal']          = 'Normal (100% production, 100% coût)';
$lang['building']['snowmaking_mode_eco']             = 'Éco (70% production, 70% coût)';
$lang['building']['snowmaking_mode_boost']           = 'Boost (140% production, 160% coût)';
$lang['building']['snowmaking_mode_col_mode']        = 'Mode';
$lang['building']['snowmaking_mode_col_output']      = 'Production de neige';
$lang['building']['snowmaking_mode_col_cost']        = 'Coût électrique';
$lang['building']['save_snowmaking_mode']            = 'Enregistrer le mode';
$lang['building']['snowmaking_mode_saved']           = '<div class="alert alert-success text-center">Mode d\'enneigement enregistré.</div>';
// Projection de production ce soir
$lang['building']['snowmaking_projected_title']              = 'Production d\'enneigement prévue ce soir';
$lang['building']['snowmaking_projected_output']             = 'Ajout de neige prévu';
$lang['building']['snowmaking_projected_elec']               = 'Coût électrique ce soir';
$lang['building']['snowmaking_projected_blocked_temp']       = 'La température est au-dessus de zéro — les canons à neige ne peuvent pas fonctionner ce soir.';
$lang['building']['snowmaking_projected_blocked_staff']      = 'Aucun opérateur d\'enneigement embauché — les canons ne peuvent pas fonctionner. Embauchez au moins '.SNOWMAKING_MIN_STAFF.' opérateur.';
$lang['building']['snowmaking_projected_blocked_water_empty'] = 'Le réservoir d\'eau est vide — aucune neige ne sera produite ce soir.';
$lang['building']['snowmaking_projected_blocked_no_reservoir'] = 'Aucun réservoir d\'eau acheté — les canons ne peuvent pas fonctionner. Achetez d\'abord un réservoir d\'eau.';
$lang['building']['snowmaking_page_link']            = 'Gestion de l\'enneigement';
// Snowmaking Efficiency
$lang['building']['snowmaking_efficiency_title']     = 'Efficacité de l\'enneigement';
$lang['building']['snowmaking_efficiency_intro']     = 'L\'efficacité indique la quantité de neige (en cm) produite par vos canons pour 100 € d\'électricité cette nuit. Plus c\'est élevé, mieux c\'est.';
$lang['building']['snowmaking_efficiency_label']     = 'Efficacité';
$lang['building']['snowmaking_efficiency_unit']      = 'cm pour 100 €';
// Snowmaking Schedule
$lang['building']['snowmaking_schedule_title']       = 'Calendrier d\'enneigement';
$lang['building']['snowmaking_schedule_intro']       = 'Sélectionnez les nuits de la semaine pendant lesquelles vos canons à neige sont autorisés à fonctionner. Désactiver certaines nuits permet d\'économiser de l\'électricité.';
$lang['building']['save_snowmaking_schedule']        = 'Sauvegarder le calendrier';
$lang['building']['snowmaking_schedule_saved']       = '<div class="alert alert-success text-center">Calendrier d\'enneigement sauvegardé.</div>';
$lang['building']['snowmaking_schedule_days']        = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
// Recharge d'urgence au réseau municipal
$lang['building']['municipal_refill_title']             = 'Recharge d\'urgence au réseau municipal';
$lang['building']['municipal_refill_intro']             = 'En cas de sécheresse ou de faible fonte nivale, vous pouvez utiliser l\'eau du réseau municipal pour recharger instantanément le réservoir. Cette opération est coûteuse et nuit à votre réputation écologique.';
$lang['building']['municipal_refill_locked_msg']        = 'Verrouillé — nécessite le Niveau %d de la station (%d remontées ouvertes requises). Développez votre station pour déverrouiller cette fonctionnalité.';
$lang['building']['municipal_refill_no_reservoir_msg']  = 'Vous devez acheter un réservoir d\'eau avant d\'utiliser l\'eau municipale.';
$lang['building']['municipal_refill_not_needed_msg']    = 'La recharge municipale n\'est disponible qu\'en cas d\'urgence, lorsque le réservoir est en dessous de %d%%. Votre réservoir est actuellement au-dessus de ce seuil.';
$lang['building']['municipal_refill_available_title']   = 'Recharge d\'urgence disponible';
$lang['building']['municipal_refill_available_desc']    = 'Ajouter +%d%% au réservoir pour <strong>%s €</strong>. Impact environnemental : −%d réputation écologique, −%d réputation de station.';
$lang['building']['municipal_refill_confirm']           = 'Utiliser l\'eau municipale pour recharger le réservoir pour %s € ? Des pénalités écologiques et de réputation seront appliquées.';
$lang['building']['municipal_refill_btn']               = 'Utiliser l\'eau municipale (+'.MUNICIPAL_WATER_REFILL_AMOUNT.'%)';
$lang['building']['municipal_refill_notes']             = 'L\'utilisation de l\'eau municipale est 2 à 3 fois plus coûteuse que les précipitations naturelles et a un impact négatif sur l\'environnement. À utiliser uniquement en dernier recours.';
$lang['building']['municipal_refill_success']           = '<div class="alert alert-warning">⚠️ Eau municipale utilisée en urgence. Le réservoir a été rechargé (+'.MUNICIPAL_WATER_REFILL_AMOUNT.'%). Pénalités écologiques et de réputation appliquées.</div>';
$lang['building']['municipal_refill_locked']            = '<div class="alert alert-danger">🔒 Recharge municipale verrouillée. Nécessite le Niveau '.MUNICIPAL_WATER_UNLOCK_LIFTS.'+.</div>';
$lang['building']['municipal_refill_no_reservoir']      = '<div class="alert alert-danger">🚫 Achetez d\'abord un réservoir d\'eau avant d\'utiliser l\'eau municipale.</div>';
$lang['building']['municipal_refill_not_needed']        = '<div class="alert alert-info">ℹ️ La recharge municipale n\'est disponible que si le réservoir est en dessous de '.MUNICIPAL_WATER_MAX_RESERVOIR_PCT.'%.</div>';
$lang['building']['municipal_refill_log']               = 'Utilisation d\'eau municipale d\'urgence pour recharger le réservoir d\'enneigement.';
// Real Estate Development
$lang['building']['real_estate_title']                 = 'Promotion immobilière privée';
$lang['building']['real_estate_intro']                 = 'Développez des propriétés ski-in ski-out, des chalets de luxe et des appartements. Vendez-les pour un revenu ponctuel ou conservez-les pour générer des revenus locatifs passifs à long terme.';
$lang['building']['real_estate_develop_title']         = 'Développer une nouvelle propriété';
$lang['building']['real_estate_develop_intro']         = 'Choisissez un type de propriété pour lancer la construction. Une seule propriété peut être construite à la fois.';
$lang['building']['real_estate_my_properties']         = 'Mes propriétés';
$lang['building']['real_estate_no_properties']         = 'Vous n\'avez pas encore développé de propriétés. Commencez par en construire une ci-dessus !';
$lang['building']['real_estate_how_it_works']          = 'Comment ça marche';
$lang['building']['real_estate_how_it_works_desc']     = 'Développez des propriétés en payant le coût de construction. Après la période de construction, la propriété génère des revenus locatifs journaliers automatiquement (versés chaque nuit). Vous pouvez vendre n\'importe quelle propriété en location à tout moment pour un revenu unique. Une taxe foncière est déduite du loyer journalier. Une seule propriété peut être en construction à la fois.';
// Property types
$lang['building']['real_estate_type_ski_in_ski_out']   = 'Propriété ski-in / ski-out';
$lang['building']['real_estate_type_luxury_chalet']    = 'Chalet de luxe';
$lang['building']['real_estate_type_condo']            = 'Appartement';
// Property detail labels
$lang['building']['real_estate_build_cost']            = 'Coût de construction';
$lang['building']['real_estate_build_time']            = 'Durée de construction';
$lang['building']['real_estate_sale_price']            = 'Prix de vente (unique)';
$lang['building']['real_estate_daily_rent']            = 'Loyer journalier (net)';
$lang['building']['real_estate_after_tax']             = 'après taxe de';
$lang['building']['real_estate_days']                  = 'jours';
$lang['building']['real_estate_day']                   = 'jour';
// Status labels
$lang['building']['real_estate_status_under_construction'] = 'En construction';
$lang['building']['real_estate_status_renting']        = 'En location';
$lang['building']['real_estate_status_for_sale']       = 'À vendre';
$lang['building']['real_estate_status_sold']           = 'Vendue';
// Table columns
$lang['building']['real_estate_col_type']              = 'Type';
$lang['building']['real_estate_col_status']            = 'Statut';
$lang['building']['real_estate_col_completion']        = 'Date de fin';
$lang['building']['real_estate_col_net_rent']          = 'Loyer net journalier';
$lang['building']['real_estate_col_actions']           = 'Actions';
// Buttons
$lang['building']['real_estate_develop_btn']           = 'Développer';
$lang['building']['real_estate_sell_btn']              = 'Vendre';
$lang['building']['real_estate_keep_for_rent']         = 'Garder en location';
// Confirm messages
$lang['building']['real_estate_confirm_develop']       = 'Êtes-vous sûr de vouloir lancer la construction ?';
$lang['building']['real_estate_confirm_sell']          = 'Êtes-vous sûr de vouloir vendre cette propriété ?';
// Action result messages
$lang['building']['real_estate_construction_started']  = '<div class="alert alert-success text-center">Construction lancée ! La propriété sera prête à louer ou vendre une fois terminée.</div>';
$lang['building']['real_estate_construction_ongoing']  = '<div class="alert alert-warning text-center">Une propriété est déjà en construction. Attendez qu\'elle soit terminée avant d\'en lancer une nouvelle.</div>';
$lang['building']['real_estate_construction_in_progress'] = 'Une propriété est actuellement en construction. Vous pourrez en lancer une nouvelle une fois celle-ci terminée.';
$lang['building']['real_estate_not_enough_money']      = '<div class="alert alert-danger text-center">Vous n\'avez pas assez d\'argent pour développer cette propriété.</div>';
$lang['building']['real_estate_bad_type']              = '<div class="alert alert-danger text-center">Type de propriété invalide sélectionné.</div>';
$lang['building']['real_estate_bad_action']            = '<div class="alert alert-danger text-center">Cette action ne peut pas être effectuée.</div>';
$lang['building']['real_estate_sold']                  = '<div class="alert alert-success text-center">Propriété vendue avec succès ! Le revenu a été ajouté à votre compte.</div>';
$lang['building']['real_estate_set_renting']           = '<div class="alert alert-success text-center">La propriété est maintenant en location et génèrera des revenus journaliers.</div>';
$lang['building']['real_estate_set_for_sale']          = '<div class="alert alert-info text-center">La propriété est maintenant listée à la vente.</div>';
// Log messages
$lang['building']['real_estate_develop_log']           = 'Construction lancée d\'une';
$lang['building']['real_estate_sold_log']              = 'Vente d\'une';
$lang['building']['real_estate_rent_log']              = 'Revenus locatifs passifs de l\'immobilier :';
// Require tourist info
$lang['building']['tourist_info_required']             = '<div class="alert alert-warning text-center">Vous devez construire un Office du tourisme avant de développer de l\'immobilier.</div>';
// Town Development
$lang['building']['town_title']                   = 'Développement du village local';
$lang['building']['town_intro']                   = 'Le village voisin grandit avec votre station. Ouvrez des hôtels, construisez votre réputation et maintenez la vitalité du village pour débloquer des bonus d\'infrastructure et augmenter la valeur immobilière.';
$lang['building']['town_status_label']            = 'État actuel du village';
$lang['building']['town_level_label']             = 'Niveau';
$lang['building']['town_level_0']                 = 'Aucun village';
$lang['building']['town_level_1']                 = 'Hameau';
$lang['building']['town_level_2']                 = 'Village';
$lang['building']['town_level_3']                 = 'Bourg';
$lang['building']['town_level_4']                 = 'Ville de villégiature';
$lang['building']['town_level_5']                 = 'Cité alpine';
$lang['building']['town_max_level']               = 'Niveau maximum atteint – votre station possède une Cité alpine florissante !';
$lang['building']['town_progress_label']          = 'Points de croissance :';
$lang['building']['town_points_label']            = 'pts';
$lang['building']['town_points_needed']           = 'pts manquants';
$lang['building']['town_next_level_label']        = 'Prochain niveau';
$lang['building']['town_key_figures']             = 'Statistiques du village';
$lang['building']['town_property_value_label']    = 'Indice de valeur immobilière';
$lang['building']['town_property_value_help']     = '100 % = référence ; augmente à chaque niveau';
$lang['building']['town_infrastructure_label']    = 'Niveau d\'infrastructure';
$lang['building']['town_open_hotels_label']       = 'Hôtels ouverts';
$lang['building']['town_levels_title']            = 'Aperçu des niveaux du village';
$lang['building']['town_col_level']               = 'Niveau';
$lang['building']['town_col_name']                = 'Nom';
$lang['building']['town_col_points']              = 'Points';
$lang['building']['town_col_property']            = 'Valeur immobilière';
$lang['building']['town_how_it_works']            = 'Comment le village se développe';
$lang['building']['town_how_it_works_desc']       = 'Chaque nuit, le calcul nocturne recalcule la croissance de votre village selon l\'activité de votre station :';
$lang['building']['town_growth_hotel_tip']        = 'Chaque hôtel ouvert ajoute <strong>%d points de croissance</strong> par nuit.';
$lang['building']['town_growth_reputation_tip']   = 'Chaque point de réputation ajoute <strong>%.1f point de croissance</strong> par nuit.';
$lang['building']['town_neglect_tip']             = 'Si aucun hôtel n\'est ouvert, le village se détériore et la réputation de votre station est pénalisée chaque nuit.';
$lang['building']['town_neglect_warning_title']   = 'Alerte : village à l\'abandon !';
$lang['building']['town_neglect_warning_desc']    = 'Votre village est à l\'abandon — aucun hôtel n\'est ouvert. Le village se détériore et votre station perd <strong>%d points de réputation</strong> par nuit jusqu\'à l\'ouverture d\'hôtels.';

// Insurance
$lang['building']['insurance_title']                  = 'Assurance';
$lang['building']['insurance_page_intro']             = 'Protégez votre station contre les pertes financières causées par des accidents de remontées mécaniques ou des dommages liés aux tempêtes. Choisissez un plan, payez une prime journalière et laissez l\'assurance couvrir une partie des coûts lors d\'incidents.';
$lang['building']['insurance_how_it_works']           = 'Fonctionnement';
$lang['building']['insurance_how_it_works_desc']      = 'Chaque nuit, une prime est prélevée selon votre plan. En cas d\'incident couvert, l\'assurance verse directement un montant sur le compte de votre station.';
$lang['building']['insurance_mechanic_premium']       = 'Une prime journalière est déduite de votre trésorerie tant que votre plan est actif.';
$lang['building']['insurance_mechanic_lift_accident'] = 'Basic &amp; Premium : un sinistre sur une remontée mécanique déclenche un versement forfaitaire pour aider à couvrir les réparations.';
$lang['building']['insurance_mechanic_storm']         = 'Premium uniquement : chaque remontée endommagée par une tempête déclenche un versement supplémentaire.';
$lang['building']['insurance_mechanic_claims']        = 'Chaque sinistre est enregistré et impacte vos finances — suivez-les sur cette page.';
$lang['building']['insurance_plans_title']            = 'Comparaison des plans';
$lang['building']['insurance_col_plan']               = 'Plan';
$lang['building']['insurance_col_premium']            = 'Prime journalière';
$lang['building']['insurance_col_lift_payout']        = 'Remboursement accident remontée';
$lang['building']['insurance_col_storm_payout']       = 'Remboursement tempête (par remontée)';
$lang['building']['insurance_plan_none']              = 'Aucun';
$lang['building']['insurance_plan_basic']             = 'Basic';
$lang['building']['insurance_plan_premium']           = 'Premium';
$lang['building']['insurance_plan_none_desc']         = 'Pas de couverture, pas de coût journalier.';
$lang['building']['insurance_plan_basic_desc']        = 'Couvre les accidents de remontées. Prime : ' . number_format(INSURANCE_DAILY_PREMIUM_BASIC, 0, '.', ' ') . ' €/jour. Remboursement : ' . number_format(INSURANCE_LIFT_PAYOUT_BASIC, 0, '.', ' ') . ' €/sinistre.';
$lang['building']['insurance_plan_premium_desc']      = 'Couvre les accidents de remontées et les tempêtes. Prime : ' . number_format(INSURANCE_DAILY_PREMIUM_PREMIUM, 0, '.', ' ') . ' €/jour. Remboursement : ' . number_format(INSURANCE_LIFT_PAYOUT_PREMIUM, 0, '.', ' ') . ' €/accident + ' . number_format(INSURANCE_STORM_PAYOUT_PER_LIFT, 0, '.', ' ') . ' €/remontée endommagée.';
$lang['building']['insurance_per_day']                = 'jour';
$lang['building']['insurance_per_lift']               = 'remontée';
$lang['building']['insurance_select_plan_title']      = 'Choisir votre plan';
$lang['building']['insurance_save_btn']               = 'Enregistrer le plan';
$lang['building']['insurance_status_title']           = 'Statut de l\'assurance';
$lang['building']['insurance_active_plan_label']      = 'Plan actif';
$lang['building']['insurance_daily_cost_label']       = 'Prime journalière';
$lang['building']['insurance_total_claims_label']     = 'Total des sinistres';
$lang['building']['insurance_total_claimed_label']    = 'Montant total remboursé';
$lang['building']['insurance_settings_saved']         = '<div class="alert alert-success text-center">Plan d\'assurance enregistré avec succès.</div>';
$lang['building']['insurance_save_error']             = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';
// VIP & Loyalty Programs
$lang['building']['vip_loyalty_title']                  = 'Programmes VIP & Fidélité';
$lang['building']['vip_loyalty_page_intro']             = 'Récompensez vos skieurs les plus fidèles et proposez des services premium à vos clients VIP. Un bon programme de fidélité attire des visiteurs réguliers, tandis que les services VIP génèrent des revenus et de la réputation supplémentaires.';
$lang['building']['vip_loyalty_how_it_works']           = 'Comment ça fonctionne';
$lang['building']['vip_loyalty_how_it_works_desc']      = 'Chaque nuit, le jeu évalue vos paramètres VIP et fidélité et applique les changements de revenus, coûts et réputation correspondants. Activer davantage de services augmente la complexité et les coûts, mais améliore également la réputation et les revenus.';
$lang['building']['vip_loyalty_mechanic_loyalty']       = 'Programme de fidélité : ' . (int)(VIP_LOYALTY_VISITOR_PCT * 100) . '% des visiteurs quotidiens sont considérés comme des clients fidèles et bénéficient de votre remise configurée. Cela coûte des revenus mais rapporte +' . VIP_LOYALTY_REP_BONUS . ' réputation par nuit.';
$lang['building']['vip_loyalty_mechanic_private_lift']  = 'Télésiège privé : accès réservé pour les clients VIP. Coûte ' . VIP_PRIVATE_LIFT_COST . ' €/nuit, rapporte +' . VIP_PRIVATE_LIFT_REP_BONUS . ' réputation et génère des revenus premium.';
$lang['building']['vip_loyalty_mechanic_premium_slopes']= 'Pistes premium : accès exclusif aux pistes pour les clients VIP. Coûte ' . VIP_PREMIUM_SLOPES_COST . ' €/nuit et rapporte +' . VIP_PREMIUM_SLOPES_REP_BONUS . ' réputation par nuit.';
$lang['building']['vip_loyalty_mechanic_concierge']     = 'Service de conciergerie : assistance personnelle pour les clients VIP. Coûte ' . VIP_CONCIERGE_COST . ' €/nuit, rapporte +' . VIP_CONCIERGE_REP_BONUS . ' réputation et génère ' . VIP_CONCIERGE_REVENUE_PER_VISITOR . ' €/client conciergerie.';
$lang['building']['vip_loyalty_mechanic_airport_transfer'] = 'Transfert aéroport : service de navette premium depuis l\'aéroport le plus proche pour les clients VIP. Coûte ' . VIP_AIRPORT_TRANSFER_COST . ' €/nuit, rapporte +' . VIP_AIRPORT_TRANSFER_REP_BONUS . ' réputation et génère ' . VIP_AIRPORT_TRANSFER_REVENUE_PER_VISITOR . ' €/client transfert.';
$lang['building']['vip_loyalty_mechanic_apreski_lounge']   = 'Lounge après-ski : espace de divertissement exclusif en soirée pour les clients VIP. Coûte ' . VIP_APRESKI_LOUNGE_COST . ' €/nuit, rapporte +' . VIP_APRESKI_LOUNGE_REP_BONUS . ' réputation et génère ' . VIP_APRESKI_LOUNGE_REVENUE_PER_VISITOR . ' €/client lounge.';
$lang['building']['vip_loyalty_settings_title']         = 'Paramètres du programme';
$lang['building']['vip_loyalty_enable_loyalty_label']   = 'Activer le programme de remise fidélité';
$lang['building']['vip_loyalty_enable_loyalty_help']    = 'Lorsqu\'il est activé, les skieurs fréquents bénéficient d\'une remise sur leur forfait, augmentant la satisfaction et la réputation à un faible coût de revenu.';
$lang['building']['vip_loyalty_discount_label']         = 'Remise fidélité';
$lang['building']['vip_loyalty_discount_help']          = 'Pourcentage de remise accordé aux clients fidèles sur leur forfait de ski.';
$lang['building']['vip_loyalty_private_lift_label']     = 'Service télésiège privé VIP';
$lang['building']['vip_loyalty_private_lift_help']      = 'Coûte %d €/nuit à exploiter. Rapporte +%d réputation par nuit et génère des revenus premium.';
$lang['building']['vip_loyalty_premium_slopes_label']   = 'Accès pistes premium VIP';
$lang['building']['vip_loyalty_premium_slopes_help']    = 'Coûte %d €/nuit à exploiter. Rapporte +%d réputation par nuit pour un accès exclusif aux pistes.';
$lang['building']['vip_loyalty_concierge_label']        = 'Service de conciergerie VIP';
$lang['building']['vip_loyalty_concierge_help']         = 'Coûte %d €/nuit à exploiter. Rapporte +%d réputation par nuit et génère des revenus de conciergerie.';
$lang['building']['vip_loyalty_airport_transfer_label'] = 'Service de transfert aéroport VIP';
$lang['building']['vip_loyalty_airport_transfer_help']  = 'Coûte %d €/nuit à exploiter. Rapporte +%d réputation par nuit et génère des revenus de transfert premium pour les clients VIP arrivant par avion.';
$lang['building']['vip_loyalty_apreski_lounge_label']   = 'Lounge après-ski VIP';
$lang['building']['vip_loyalty_apreski_lounge_help']    = 'Coûte %d €/nuit à exploiter. Rapporte +%d réputation par nuit et génère des revenus de lounge pour les clients VIP en soirée.';
$lang['building']['vip_loyalty_save_btn']               = 'Enregistrer';
$lang['building']['vip_loyalty_key_figures']            = 'Paramètres actuels';
$lang['building']['vip_loyalty_loyalty_status_label']   = 'Programme de fidélité';
$lang['building']['vip_loyalty_on']                     = 'ACTIF';
$lang['building']['vip_loyalty_off']                    = 'INACTIF';
$lang['building']['vip_loyalty_rep_gain_label']         = 'Gain de réputation nocturne';
$lang['building']['vip_loyalty_rep_gain_desc']          = 'Jusqu\'à +' . (VIP_LOYALTY_REP_BONUS + VIP_PRIVATE_LIFT_REP_BONUS + VIP_PREMIUM_SLOPES_REP_BONUS + VIP_CONCIERGE_REP_BONUS + VIP_AIRPORT_TRANSFER_REP_BONUS + VIP_APRESKI_LOUNGE_REP_BONUS) . ' pts/nuit (tous les services actifs)';
$lang['building']['vip_loyalty_settings_saved']         = '<div class="alert alert-success text-center">Paramètres VIP & Fidélité enregistrés avec succès.</div>';
$lang['building']['vip_loyalty_invalid_settings']       = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['vip_loyalty_save_error']             = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';

// ============================================================
// Celebrity / VIP Visits
// ============================================================
$lang['building']['celebrity_visit_title']                  = 'Visites VIP & Célébrités';
$lang['building']['celebrity_visit_intro']                  = 'Parfois, une personnalité célèbre débarque dans votre station. Influenceurs, skieurs professionnels et équipes de tournage peuvent booster votre réputation – ou provoquer un désastre si vos remontées tombent en panne.';
$lang['building']['celebrity_visit_how_it_works']           = 'Comment ça fonctionne';
$lang['building']['celebrity_visit_how_it_works_desc']      = 'Chaque nuit, il y a une chance aléatoire qu\'une célébrité visite votre station. L\'impact sur la réputation dépend de l\'état de vos pistes et de l\'état de vos remontées mécaniques.';
$lang['building']['celebrity_visit_mechanic_chance']        = 'Il y a <strong>%d%%</strong> de chances chaque nuit qu\'une célébrité visite votre station.';
$lang['building']['celebrity_visit_mechanic_types']         = 'Types de visiteurs possibles : <strong>Influenceur sur les réseaux sociaux</strong>, <strong>Skieur professionnel</strong>, <strong>Équipe de tournage</strong>.';
$lang['building']['celebrity_visit_mechanic_good_slopes']   = 'Si vos pistes sont en bon état, votre station gagne <strong>+%d réputation</strong> (grand saut !).';
$lang['building']['celebrity_visit_mechanic_base']          = 'Si vos pistes sont moyennes, votre station gagne quand même <strong>+%d réputation</strong>.';
$lang['building']['celebrity_visit_mechanic_lift_fail']     = 'Si une remontée est en maintenance lors de la visite, votre station perd <strong>%d réputation</strong> (désastre de relations publiques !).';
$lang['building']['celebrity_visit_history_title']          = 'Historique des visites (derniers %d jours)';
$lang['building']['celebrity_visit_no_history']             = 'Aucune visite de célébrité sur cette période. Gardez votre station au top !';
$lang['building']['celebrity_visit_col_date']               = 'Date';
$lang['building']['celebrity_visit_col_type']               = 'Visiteur';
$lang['building']['celebrity_visit_col_slopes']             = 'Pistes';
$lang['building']['celebrity_visit_col_lift']               = 'Remontées';
$lang['building']['celebrity_visit_col_rep']                = 'Réputation';
$lang['building']['celebrity_visit_type_influencer']        = 'Influenceur';
$lang['building']['celebrity_visit_type_pro_skier']         = 'Skieur Pro';
$lang['building']['celebrity_visit_type_film_crew']         = 'Équipe de tournage';
$lang['building']['celebrity_visit_slopes_good']            = 'Bon';
$lang['building']['celebrity_visit_slopes_avg']             = 'Moyen';
$lang['building']['celebrity_visit_lift_failed']            = 'En panne';
$lang['building']['celebrity_visit_lift_ok']                = 'OK';
// Accessibilité & Transports
// ============================================================
$lang['building']['transport_title']              = 'Accessibilité & Transports';
$lang['building']['transport_page_intro']         = 'Gérez les navettes, le stockage de skis et les liaisons par télécabine entre les secteurs de la station. De meilleurs transports améliorent la satisfaction des visiteurs, surtout pour les familles.';
$lang['building']['transport_how_it_works']       = 'Comment ça fonctionne';
$lang['building']['transport_how_it_works_desc']  = 'Chaque nuit, le jeu évalue votre infrastructure de transport et attribue des bonus de réputation en fonction de ce que vous avez construit. Les navettes et télécabines attirent les familles comme les skieurs expérimentés, tandis que les casiers à skis sont particulièrement appréciés des familles.';
$lang['building']['transport_mechanic_shuttle']   = 'Niveau de navette 1–3 : bus, tramway ou navette premium entre les secteurs réduisent la frustration des visiteurs et rapportent de la réputation chaque nuit.';
$lang['building']['transport_mechanic_ski_storage'] = 'Stockage de skis : une salle de dépôt dédiée rapporte un bonus de réputation nocturne auprès des familles qui apprécient ne pas porter leur équipement partout.';
$lang['building']['transport_mechanic_gondola']   = 'Liaison télécabine : une télécabine inter-secteurs rapporte de la réputation auprès des familles et des skieurs experts qui apprécient un accès rapide à toute la station.';
$lang['building']['transport_mechanic_visitors']  = 'De bons transports attirent plus de visiteurs : chaque niveau de navette ajoute ' . (int)(TRANSPORT_VISITOR_BONUS_PER_LEVEL * 100) . '% au nombre journalier de visiteurs.';
$lang['building']['transport_settings_title']     = 'Paramètres de transport';
$lang['building']['transport_shuttle_label']      = 'Niveau de service navette';
$lang['building']['transport_shuttle_help']       = 'Des niveaux plus élevés offrent un meilleur transport entre les secteurs et augmentent le nombre de visiteurs.';
$lang['building']['transport_shuttle_level_0']    = 'Pas de navette';
$lang['building']['transport_shuttle_level_1']    = 'Bus basique';
$lang['building']['transport_shuttle_level_2']    = 'Tramway';
$lang['building']['transport_shuttle_level_3']    = 'Navette premium';
$lang['building']['transport_ski_storage_label']  = 'Casiers à skis';
$lang['building']['transport_ski_storage_help']   = 'Proposez un espace dédié pour que les visiteurs entreposent leurs skis et chaussures la nuit.';
$lang['building']['transport_gondola_label']      = 'Liaison télécabine inter-secteurs';
$lang['building']['transport_gondola_help']       = 'Une télécabine reliant différents secteurs de la station pour un accès rapide.';
$lang['building']['transport_save_btn']           = 'Enregistrer les paramètres';
$lang['building']['transport_key_figures']        = 'Statut actuel';
$lang['building']['transport_shuttle_status']     = 'Niveau de navette';
$lang['building']['transport_shuttle_daily_cost'] = 'Coût journalier navette';
$lang['building']['transport_ski_storage_status'] = 'Casiers à skis';
$lang['building']['transport_gondola_status']     = 'Télécabine inter-secteurs';
$lang['building']['transport_visitor_bonus_label'] = 'Bonus visiteurs (navettes)';
$lang['building']['transport_nightly_rep_label']  = 'Bonus de réputation nocturne';
$lang['building']['transport_rep_families']       = 'rép. (familles)';
$lang['building']['transport_rep_pros']           = 'rép. (pros)';
$lang['building']['transport_per_day']            = 'jour';
$lang['building']['transport_on']                 = 'ACTIVÉ';
$lang['building']['transport_off']                = 'DÉSACTIVÉ';
$lang['building']['transport_settings_saved']     = '<div class="alert alert-success text-center">Paramètres de transport enregistrés avec succès.</div>';
$lang['building']['transport_invalid_settings']   = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['transport_save_error']         = '<div class="alert alert-danger text-center">Une erreur est survenue lors de la sauvegarde. Veuillez réessayer.</div>';
// Hébergements – Upgrades
$lang['building']['accommodation_title']               = 'Améliorations d\'hébergement';
$lang['building']['accommodation_page_intro']          = 'Améliorez l\'hébergement de votre station pour attirer davantage de visiteurs et booster votre réputation. Choisissez entre des chalets, des lodges ou des hôtels de luxe — chacun avec des coûts de maintenance et des avantages croissants.';
$lang['building']['accommodation_how_it_works']        = 'Comment ça fonctionne';
$lang['building']['accommodation_how_it_works_desc']   = 'Sélectionner un niveau d\'hébergement l\'active et entraîne un coût de maintenance nocturne. En contrepartie, votre station gagne des points de réputation quotidiens et attire des visiteurs supplémentaires.';
$lang['building']['accommodation_mechanic_cost']       = 'Un coût de maintenance nocturne est automatiquement prélevé chaque nuit lorsque l\'hébergement est actif.';
$lang['building']['accommodation_mechanic_rep']        = 'Un hébergement actif ajoute des points de réputation chaque nuit.';
$lang['building']['accommodation_mechanic_visitors']   = 'Un hébergement de niveau supérieur attire un pourcentage plus élevé de visiteurs supplémentaires chaque jour.';
$lang['building']['accommodation_mechanic_upgrade']    = 'La mise à niveau vers un nouveau tier coûte un montant unique et active immédiatement le nouveau niveau.';
$lang['building']['accommodation_current_status']      = 'Statut actuel';
$lang['building']['accommodation_type_label']          = 'Type d\'hébergement';
$lang['building']['accommodation_none']                = 'Aucun';
$lang['building']['accommodation_type_cabin']          = 'Chalet';
$lang['building']['accommodation_type_lodge']          = 'Lodge';
$lang['building']['accommodation_type_luxury_hotel']   = 'Hôtel de luxe';
$lang['building']['accommodation_status_label']        = 'Statut';
$lang['building']['accommodation_on']                  = 'ACTIF';
$lang['building']['accommodation_off']                 = 'INACTIF';
$lang['building']['accommodation_nightly_cost_label']  = 'Coût de maintenance nocturne';
$lang['building']['accommodation_rep_bonus_label']     = 'Bonus de réputation nocturne';
$lang['building']['accommodation_visitor_bonus_label'] = 'Bonus visiteurs';
$lang['building']['accommodation_disable_btn']         = 'Désactiver l\'hébergement';
$lang['building']['accommodation_enable_btn']          = 'Activer l\'hébergement';
$lang['building']['accommodation_upgrade_title']       = 'Choisir un niveau d\'hébergement';
$lang['building']['accommodation_upgrade_desc']        = 'Sélectionnez un niveau ci-dessous pour effectuer une mise à niveau. Un coût unique est facturé lors du changement de niveau.';
$lang['building']['accommodation_col_type']            = 'Type';
$lang['building']['accommodation_col_cost']            = 'Coût de mise à niveau';
$lang['building']['accommodation_col_maintenance']     = 'Maintenance';
$lang['building']['accommodation_col_rep']             = 'Bonus réputation / nuit';
$lang['building']['accommodation_col_visitors']        = 'Bonus visiteurs';
$lang['building']['accommodation_current_badge']       = 'Actuel';
$lang['building']['accommodation_per_night']           = 'nuit';
$lang['building']['accommodation_upgrade_btn']         = 'Mettre à niveau';
// Messages de résultat
$lang['building']['accommodation_upgraded']            = '<div class="alert alert-success text-center">Hébergement mis à niveau avec succès !</div>';
$lang['building']['accommodation_enabled']             = '<div class="alert alert-success text-center">Hébergement activé.</div>';
$lang['building']['accommodation_disabled']            = '<div class="alert alert-warning text-center">Hébergement désactivé.</div>';
$lang['building']['accommodation_invalid_type']        = '<div class="alert alert-danger text-center">Type d\'hébergement invalide sélectionné.</div>';
$lang['building']['accommodation_not_enough_money']    = '<div class="alert alert-danger text-center">Vous n\'avez pas assez d\'argent pour améliorer l\'hébergement.</div>';
$lang['building']['accommodation_no_type_selected']    = '<div class="alert alert-danger text-center">Aucun type d\'hébergement sélectionné. Choisissez d\'abord un niveau.</div>';
$lang['building']['accommodation_save_error']          = '<div class="alert alert-danger text-center">Une erreur s\'est produite. Veuillez réessayer.</div>';
// ============================================================
// Remontées panoramiques (Scenic Lifts)
// ============================================================
$lang['building']['scenic_lift_title']              = 'Remontées panoramiques';
$lang['building']['scenic_lift_page_intro']         = 'Proposez un service de téléphérique panoramique pour générer des revenus supplémentaires auprès des touristes et booster la réputation de votre station chaque jour.';
$lang['building']['scenic_lift_how_it_works']       = 'Comment ça fonctionne';
$lang['building']['scenic_lift_how_it_works_desc']  = 'Lorsque le service de remontée panoramique est activé, une partie de vos visiteurs quotidiens achète un billet d\'observation et monte en gondole. Les revenus sont crédités chaque nuit. Pendant l\'exploitation, vous payez également un coût de fonctionnement journalier et gagnez un petit bonus de réputation.';
$lang['building']['scenic_lift_mechanic_revenue']   = 'Revenu journalier : ' . (int)(SCENIC_LIFT_TOURIST_RATIO * 100) . ' % des visiteurs × prix du billet.';
$lang['building']['scenic_lift_mechanic_cost']      = 'Coût de fonctionnement journalier : ' . SCENIC_LIFT_DAILY_COST . ' € indépendamment du nombre de visiteurs.';
$lang['building']['scenic_lift_mechanic_reputation'] = 'Bonus de réputation : +' . SCENIC_LIFT_REP_BONUS_PER_DAY . ' par jour tant que le service est actif.';
$lang['building']['scenic_lift_settings_title']     = 'Paramètres du téléphérique';
$lang['building']['scenic_lift_enable_label']       = 'Activer le service de gondole panoramique';
$lang['building']['scenic_lift_enable_help']        = 'Quand activé, les touristes achètent des billets d\'observation chaque jour.';
$lang['building']['scenic_lift_ticket_price_label'] = 'Prix du billet panoramique';
$lang['building']['scenic_lift_per_person']         = 'personne/jour';
$lang['building']['scenic_lift_save_btn']           = 'Enregistrer';
$lang['building']['scenic_lift_key_figures']        = 'Paramètres actuels';
$lang['building']['scenic_lift_status_label']       = 'Statut du service';
$lang['building']['scenic_lift_on']                 = 'ACTIVÉ';
$lang['building']['scenic_lift_off']                = 'DÉSACTIVÉ';
$lang['building']['scenic_lift_daily_revenue_label'] = 'Revenu journalier (estimé)';
$lang['building']['scenic_lift_daily_revenue_desc']  = (int)(SCENIC_LIFT_TOURIST_RATIO * 100) . ' % des visiteurs × prix du billet × capacité';
$lang['building']['scenic_lift_daily_cost_label']   = 'Coût de fonctionnement journalier';
$lang['building']['scenic_lift_rep_bonus_label']    = 'Bonus de réputation';
$lang['building']['scenic_lift_rep_bonus_desc']     = '+' . SCENIC_LIFT_REP_BONUS_PER_DAY . ' pt / jour pendant l\'activité';
$lang['building']['scenic_lift_capacity_label']     = 'Niveau de capacité de la gondole';
$lang['building']['scenic_lift_capacity_help']      = 'Contrôle le nombre de cabines en service (1 = minimal, 5 = maximum). Les niveaux élevés augmentent le flux de touristes et le coût journalier de ' . SCENIC_LIFT_CAPACITY_COST_PER_LEVEL . ' € par niveau au-dessus du défaut (' . SCENIC_LIFT_DEFAULT_CAPACITY . ').';
$lang['building']['scenic_lift_mechanic_capacity']  = 'Capacité gondole (1–5) : multiplie le flux de touristes par niveau/' . SCENIC_LIFT_DEFAULT_CAPACITY . ' et ajuste le coût journalier de ±' . SCENIC_LIFT_CAPACITY_COST_PER_LEVEL . ' € par palier.';
$lang['building']['scenic_lift_discount_label']     = 'Remise hors-saison';
$lang['building']['scenic_lift_discount_help']      = 'Quand activé, une remise de ' . (int)((1 - SCENIC_LIFT_DISCOUNT_PRICE_FACTOR) * 100) . ' % est appliquée en période creuse, attirant ' . (int)((SCENIC_LIFT_DISCOUNT_VISITOR_BOOST - 1) * 100) . ' % de promeneurs supplémentaires hors-saison.';
$lang['building']['scenic_lift_mechanic_discount']  = 'Remise hors-saison : en période creuse une remise de ' . (int)((1 - SCENIC_LIFT_DISCOUNT_PRICE_FACTOR) * 100) . ' % attire ' . (int)((SCENIC_LIFT_DISCOUNT_VISITOR_BOOST - 1) * 100) . ' % de visiteurs en plus (léger compromis sur le revenu, mais plus de fréquentation).';
// Thème de visite
$lang['building']['scenic_lift_tour_theme_label']       = 'Thème de visite';
$lang['building']['scenic_lift_tour_theme_help']        = 'Choisissez une expérience thématique pour la gondole. Chaque thème génère plus de visiteurs ou de revenus moyennant un coût journalier supplémentaire, et accorde de la réputation en prime.';
$lang['building']['scenic_lift_theme_standard']         = 'Standard (sans coût supplémentaire)';
$lang['building']['scenic_lift_theme_nature']           = 'Nature & Faune (+' . (int)((SCENIC_LIFT_THEME_NATURE_VISITOR_BOOST - 1) * 100) . ' % visiteurs, +' . SCENIC_LIFT_THEME_NATURE_REP_BONUS . ' rép/jour, +' . SCENIC_LIFT_THEME_NATURE_EXTRA_COST . ' €/jour)';
$lang['building']['scenic_lift_theme_sunset']           = 'Coucher de soleil & Étoiles (+' . (int)((SCENIC_LIFT_THEME_SUNSET_PRICE_FACTOR - 1) * 100) . ' % prix billet, +' . SCENIC_LIFT_THEME_SUNSET_REP_BONUS . ' rép/jour, +' . SCENIC_LIFT_THEME_SUNSET_EXTRA_COST . ' €/jour)';
$lang['building']['scenic_lift_theme_adventure']        = 'Aventure & Glacier (+' . (int)((SCENIC_LIFT_THEME_ADVENTURE_VISITOR_BOOST - 1) * 100) . ' % visiteurs, +' . SCENIC_LIFT_THEME_ADVENTURE_REP_BONUS . ' rép/jour, +' . SCENIC_LIFT_THEME_ADVENTURE_EXTRA_COST . ' €/jour)';
$lang['building']['scenic_lift_tour_theme_current']     = 'Thème de visite';
$lang['building']['scenic_lift_mechanic_theme']         = 'Thème de visite : Standard, Nature (+' . (int)((SCENIC_LIFT_THEME_NATURE_VISITOR_BOOST - 1) * 100) . ' % visiteurs), Coucher de soleil (+' . (int)((SCENIC_LIFT_THEME_SUNSET_PRICE_FACTOR - 1) * 100) . ' % prix) ou Aventure (+' . (int)((SCENIC_LIFT_THEME_ADVENTURE_VISITOR_BOOST - 1) * 100) . ' % visiteurs) : échanger un coût journalier contre de meilleures recettes et plus de réputation.';
// Forfait photo
$lang['building']['scenic_lift_photo_label']            = 'Forfait photographie';
$lang['building']['scenic_lift_photo_help']             = 'Service photo à bord de la gondole : chaque visiteur panoramique génère ' . SCENIC_LIFT_PHOTO_REVENUE_PER_VISITOR . ' € supplémentaires de ventes photo, pour un coût journalier additionnel de ' . SCENIC_LIFT_PHOTO_DAILY_COST . ' €.';
$lang['building']['scenic_lift_mechanic_photo']         = 'Forfait photo : +' . SCENIC_LIFT_PHOTO_REVENUE_PER_VISITOR . ' € par visiteur en recettes photo (+' . SCENIC_LIFT_PHOTO_DAILY_COST . ' €/jour de coût).';
$lang['building']['scenic_lift_photo_current']          = 'Forfait photographie';
// Gondole VIP
$lang['building']['scenic_lift_vip_label']              = 'Mode gondole VIP';
$lang['building']['scenic_lift_vip_help']               = 'Réservez les cabines aux clients VIP uniquement : le nombre de visiteurs tombe à ' . (int)(SCENIC_LIFT_VIP_VISITOR_FACTOR * 100) . ' % de la normale, mais chaque billet vaut ' . SCENIC_LIFT_VIP_PRICE_MULTIPLIER . '× le tarif habituel. Bonus de +' . SCENIC_LIFT_VIP_REP_BONUS . ' réputation/jour (+' . SCENIC_LIFT_VIP_DAILY_COST . ' €/jour).';
$lang['building']['scenic_lift_mechanic_vip']           = 'Gondole VIP : ' . (int)(SCENIC_LIFT_VIP_VISITOR_FACTOR * 100) . ' % des visiteurs à ' . SCENIC_LIFT_VIP_PRICE_MULTIPLIER . '× le prix, +' . SCENIC_LIFT_VIP_REP_BONUS . ' rép/jour, +' . SCENIC_LIFT_VIP_DAILY_COST . ' €/jour.';
$lang['building']['scenic_lift_vip_current']            = 'Mode gondole VIP';
$lang['building']['scenic_lift_settings_saved']     = '<div class="alert alert-success text-center">Paramètres du téléphérique enregistrés avec succès.</div>';
$lang['building']['scenic_lift_invalid_settings']   = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['scenic_lift_save_error']         = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';
// Forfaits saisonniers de ski
// ============================================================
$lang['building']['season_pass_title']             = 'Forfaits Saisonniers de Ski';
$lang['building']['season_pass_page_intro']        = 'Proposez des forfaits saisonniers à vos visiteurs. Les détenteurs de forfaits saisonniers paient à l\'avance pour un accès illimité tout au long de la saison de ski, vous offrant des revenus quotidiens prévisibles et une clientèle fidèle.';
$lang['building']['season_pass_how_it_works']      = 'Comment ça fonctionne';
$lang['building']['season_pass_how_it_works_desc'] = 'Au début de chaque saison, un certain nombre de forfaits sont vendus en fonction de la réputation de votre station et du prix fixé. Les revenus sont répartis équitablement sur la saison en tant que revenus journaliers.';
$lang['building']['season_pass_mechanic_sales']    = 'Les ventes dépendent de votre réputation et du prix : plus de réputation = plus d\'acheteurs ; prix plus bas = plus d\'acheteurs.';
$lang['building']['season_pass_mechanic_revenue']  = 'Revenu quotidien = forfaits vendus × prix ÷ ' . SEASON_PASS_SEASON_LENGTH . ' jours.';
$lang['building']['season_pass_mechanic_loyalty']  = 'Bonus de fidélité : vendre au moins ' . SEASON_PASS_HIGH_SALES_THRESHOLD . ' forfaits rapporte +' . SEASON_PASS_LOYALTY_REP_BONUS . ' de réputation chaque nuit.';
$lang['building']['season_pass_mechanic_renewal']  = 'Les forfaits sont automatiquement revendus au début de chaque nouvelle saison.';
$lang['building']['season_pass_mechanic_early_bird'] = 'Remise primo-acheteur : lorsqu\'elle est activée, la remise annoncée encourage les achats anticipés et augmente les ventes totales de +' . (int)(SEASON_PASS_EARLY_BIRD_SALES_BOOST * 100) . '%.';
$lang['building']['season_pass_settings_title']    = 'Paramètres des forfaits saisonniers';
$lang['building']['season_pass_enable_label']      = 'Activer les forfaits saisonniers';
$lang['building']['season_pass_enable_help']       = 'Lorsqu\'activé, les forfaits saisonniers sont en vente chaque saison et génèrent des revenus quotidiens.';
$lang['building']['season_pass_price_label']       = 'Prix du forfait saisonnier';
$lang['building']['season_pass_price_help']        = 'Prix par forfait saisonnier (€). Des prix plus élevés signifient plus de revenus par forfait, mais moins de forfaits vendus.';
$lang['building']['season_pass_save_btn']          = 'Enregistrer les paramètres';
$lang['building']['season_pass_key_figures']       = 'Aperçu actuel';
$lang['building']['season_pass_status_label']      = 'Forfaits saisonniers';
$lang['building']['season_pass_on']                = 'ACTIVÉ';
$lang['building']['season_pass_off']               = 'DÉSACTIVÉ';
$lang['building']['season_pass_estimated_sales']   = 'Forfaits estimés (prochaine saison)';
$lang['building']['season_pass_passes_sold_label'] = 'Forfaits vendus (saison actuelle)';
$lang['building']['season_pass_passes_unit']       = 'forfaits';
$lang['building']['season_pass_daily_revenue_label'] = 'Revenu quotidien estimé';
$lang['building']['season_pass_per_day']           = '/ jour';
$lang['building']['season_pass_loyalty_label']     = 'Bonus de fidélité';
$lang['building']['season_pass_loyalty_desc']      = '+' . SEASON_PASS_LOYALTY_REP_BONUS . ' réputation/nuit quand ' . SEASON_PASS_HIGH_SALES_THRESHOLD . '+ forfaits vendus';
$lang['building']['season_pass_early_bird_label']            = 'Remise primo-acheteur';
$lang['building']['season_pass_early_bird_help']             = 'Lorsqu\'elle est activée, une remise primo-acheteur est annoncée pour attirer les acheteurs avant le début de la saison. Cela augmente les ventes de forfaits de +%d%%.';
$lang['building']['season_pass_early_bird_discount_label']   = 'Taux de remise primo-acheteur';
$lang['building']['season_pass_early_bird_discount_label_short'] = 'remise';
$lang['building']['season_pass_early_bird_discount_help']    = 'Le pourcentage de remise offert aux acheteurs anticipés. Une remise plus élevée attire plus d\'acheteurs mais réduit le revenu par forfait.';
$lang['building']['season_pass_settings_saved']    = '<div class="alert alert-success text-center">Paramètres des forfaits saisonniers enregistrés avec succès.</div>';
$lang['building']['season_pass_invalid_settings']  = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['season_pass_save_error']        = '<div class="alert alert-danger text-center">Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.</div>';
// Maintenance Depth
$lang['building']['maint_depth_title']               = 'Profondeur de maintenance';
$lang['building']['maint_depth_intro']               = 'Choisissez un plan de maintenance préventive pour vos remontées mécaniques. Chaque nuit, des pannes mécaniques aléatoires peuvent survenir selon le type, l\'âge et l\'utilisation des remontées. Un meilleur plan réduit les risques de panne et les coûts de réparation. Le niveau de compétence de vos mécaniciens de remontée procure une remise supplémentaire sur les réparations.';
$lang['building']['maint_depth_how_it_works']        = 'Comment ça fonctionne';
$lang['building']['maint_depth_how_it_works_desc']   = 'Chaque nuit, chaque remontée ouverte est évaluée pour une éventuelle panne mécanique aléatoire.';
$lang['building']['maint_depth_mechanic_type']       = 'Type de remontée : les télécabines et téléphériques ont un taux de panne plus élevé que les simples téléskis.';
$lang['building']['maint_depth_mechanic_age']        = 'Âge : plus la remontée est ancienne, plus sa probabilité de panne quotidienne est élevée.';
$lang['building']['maint_depth_mechanic_usage']      = 'Utilisation : les remontées transportant des visiteurs au-delà de 50 % de leur capacité accumulent des risques de panne supplémentaires.';
$lang['building']['maint_depth_mechanic_staff']      = 'Compétence du personnel : les mécaniciens de remontée très efficaces réduisent les coûts de réparation jusqu\'à ' . (int)(MAINT_STAFF_MAX_REPAIR_DISCOUNT * 100) . ' %.';
$lang['building']['maint_depth_plans_title']         = 'Sélectionner un plan de maintenance';
$lang['building']['maint_depth_plan_basic']          = 'Basique';
$lang['building']['maint_depth_plan_basic_desc']     = 'Réactif uniquement. Aucun coût journalier. Les pannes sont réparées au prix plein.';
$lang['building']['maint_depth_plan_standard']       = 'Standard';
$lang['building']['maint_depth_plan_standard_desc']  = 'Inspections régulières. Frais journaliers par remontée. Réduit les coûts de réparation.';
$lang['building']['maint_depth_plan_preventive']     = 'Préventif';
$lang['building']['maint_depth_plan_preventive_desc'] = 'Programme préventif complet. Frais journaliers plus élevés. Divise le risque de panne par deux et réduit les coûts de réparation.';
$lang['building']['maint_depth_cost']                = 'Coût journalier';
$lang['building']['maint_depth_free']                = 'Gratuit';
$lang['building']['maint_depth_none']                = 'Aucun';
$lang['building']['maint_depth_failure_reduction']   = 'Réduction des pannes';
$lang['building']['maint_depth_repair_discount']     = 'Remise réparation';
$lang['building']['maint_depth_per_lift_day']        = 'remontée / jour';
$lang['building']['maint_depth_save_btn']            = 'Enregistrer le plan';
$lang['building']['maint_depth_key_figures']         = 'État actuel';
$lang['building']['maint_depth_current_plan']        = 'Plan actif';
$lang['building']['maint_depth_open_lifts']          = 'Remontées ouvertes';
$lang['building']['maint_depth_daily_plan_cost']     = 'Coût journalier du plan';
$lang['building']['maint_depth_avg_mechanic_eff']    = 'Efficacité moy. des mécaniciens';
$lang['building']['maint_depth_staff_discount']      = 'remise réparation via personnel';
$lang['building']['maint_depth_base_failure']        = 'Risque de panne de base';
// Feedback messages
$lang['building']['maint_depth_saved']               = '<div class="alert alert-success text-center">Plan de maintenance enregistré avec succès.</div>';
$lang['building']['maint_depth_invalid_plan']        = '<div class="alert alert-danger text-center">Plan de maintenance invalide sélectionné.</div>';
$lang['building']['maint_depth_save_error']          = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement du plan de maintenance.</div>';
// Logs
$lang['building']['maint_depth_failure_log']         = ' a subi une panne mécanique et a été mis en maintenance.';
$lang['building']['maint_depth_plan_cost_log']       = 'Coût du plan de maintenance préventive débité : ';


// Webcams montagne
$lang['building']['mountain_cam_title']              = 'Webcams de montagne';
$lang['building']['mountain_cam_page_intro']         = 'Installez des webcams en direct sur la montagne pour que les visiteurs potentiels puissent vérifier les conditions d\'enneigement en ligne, augmentant ainsi la fréquentation et la réputation de votre station chaque jour.';
$lang['building']['mountain_cam_how_it_works']       = 'Comment ça fonctionne';
$lang['building']['mountain_cam_how_it_works_desc']  = 'Lorsque les webcams sont actives, les flux en direct attirent les skieurs consultant les conditions en ligne et les transforment en visiteurs. Plus le nombre de caméras est élevé et meilleure est la qualité, plus la demande augmente. Un coût journalier couvre la bande passante et la maintenance.';
$lang['building']['mountain_cam_mechanic_visitors']  = 'Boost quotidien de visiteurs : +' . (int)(MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM * 100) . ' % par caméra (multiplié par la qualité).';
$lang['building']['mountain_cam_mechanic_cost']      = 'Coût journalier : ' . MOUNTAIN_CAM_DAILY_COST_BASE . ' € pour la première caméra, +' . MOUNTAIN_CAM_DAILY_COST_PER_CAM . ' € par caméra supplémentaire (multiplié par la qualité).';
$lang['building']['mountain_cam_mechanic_reputation'] = 'Bonus de réputation : +' . MOUNTAIN_CAM_REP_BONUS_PER_DAY . ' par jour tant que les webcams sont actives.';
$lang['building']['mountain_cam_mechanic_quality']   = 'Niveau de qualité : une qualité supérieure augmente à la fois la demande des visiteurs et le coût journalier.';
$lang['building']['mountain_cam_mechanic_stream']    = 'Mode streaming en direct : activer le streaming augmente la demande de visiteurs de +' . (int)(( MOUNTAIN_CAM_STREAM_VISITOR_MULT - 1) * 100) . ' % mais augmente le coût journalier de ' . (int)(( MOUNTAIN_CAM_STREAM_COST_MULT - 1) * 100) . ' %.';
$lang['building']['mountain_cam_mechanic_social']    = 'Partage sur les réseaux sociaux : partagez automatiquement des captures en ligne pour +' . MOUNTAIN_CAM_SOCIAL_REP_BONUS . ' réputation/jour avec un surcoût de +' . MOUNTAIN_CAM_SOCIAL_COST_PER_DAY . ' €/jour.';
$lang['building']['mountain_cam_mechanic_night_vision']     = 'Mode vision nocturne : équipez les caméras d\'infrarouge pour diffuser les conditions de ski en soirée, boostant la demande de visiteurs de +' . (int)((MOUNTAIN_CAM_NIGHT_VISION_VISITOR_MULT - 1) * 100) . ' % (+' . MOUNTAIN_CAM_NIGHT_VISION_COST_PER_DAY . ' €/jour supplémentaires).';
$lang['building']['mountain_cam_mechanic_weather_overlay']  = 'Incrustation météo : affichez la température, les chutes de neige et le vent en direct sur les flux webcam pour +' . MOUNTAIN_CAM_WEATHER_OVERLAY_REP_BONUS . ' réputation/jour (+' . MOUNTAIN_CAM_WEATHER_OVERLAY_COST_PER_DAY . ' €/jour supplémentaires).';
$lang['building']['mountain_cam_settings_title']     = 'Paramètres des webcams';
$lang['building']['mountain_cam_enable_label']       = 'Activer les webcams de montagne';
$lang['building']['mountain_cam_enable_help']        = 'Lorsqu\'activées, les flux en direct attirent des visiteurs supplémentaires chaque jour.';
$lang['building']['mountain_cam_num_cams_label']     = 'Nombre de caméras';
$lang['building']['mountain_cam_num_cams_help']      = 'Chaque caméra supplémentaire ajoute +' . (int)(MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM * 100) . ' % de demande et +' . MOUNTAIN_CAM_DAILY_COST_PER_CAM . ' € de coût journalier.';
$lang['building']['mountain_cam_quality_label']      = 'Qualité des caméras';
$lang['building']['mountain_cam_quality_1']          = 'Standard';
$lang['building']['mountain_cam_quality_2']          = 'HD';
$lang['building']['mountain_cam_quality_3']          = '4K';
$lang['building']['mountain_cam_quality_help']       = 'Une qualité supérieure augmente le boost de visiteurs et le coût journalier.';
$lang['building']['mountain_cam_save_btn']           = 'Enregistrer les paramètres';
$lang['building']['mountain_cam_key_figures']        = 'Paramètres actuels';
$lang['building']['mountain_cam_status_label']       = 'État des webcams';
$lang['building']['mountain_cam_on']                 = 'ON';
$lang['building']['mountain_cam_off']                = 'OFF';
$lang['building']['mountain_cam_visitor_boost_label'] = 'Boost quotidien de visiteurs';
$lang['building']['mountain_cam_visitor_boost_desc'] = '+' . (int)(MOUNTAIN_CAM_VISITOR_BOOST_PER_CAM * 100) . ' % par caméra × multiplicateur de qualité';
$lang['building']['mountain_cam_daily_cost_label']   = 'Coût de fonctionnement journalier';
$lang['building']['mountain_cam_rep_bonus_label']    = 'Bonus de réputation';
$lang['building']['mountain_cam_rep_bonus_per_day']  = 'pt / jour lorsqu\'actif';
$lang['building']['mountain_cam_stream_label']       = 'Mode streaming en direct';
$lang['building']['mountain_cam_stream_help']        = 'Le streaming en direct augmente la demande de visiteurs de +' . (int)(( MOUNTAIN_CAM_STREAM_VISITOR_MULT - 1) * 100) . ' % mais augmente le coût journalier de ' . (int)(( MOUNTAIN_CAM_STREAM_COST_MULT - 1) * 100) . ' %.';
$lang['building']['mountain_cam_stream_visitor_note'] = 'boost visiteurs';
$lang['building']['mountain_cam_social_label']       = 'Partage sur les réseaux sociaux';
$lang['building']['mountain_cam_social_help']        = 'Partagez automatiquement des captures webcam sur les réseaux sociaux pour +' . MOUNTAIN_CAM_SOCIAL_REP_BONUS . ' réputation/jour (+' . MOUNTAIN_CAM_SOCIAL_COST_PER_DAY . ' €/jour supplémentaires).';
$lang['building']['mountain_cam_night_vision_label'] = 'Mode vision nocturne';
$lang['building']['mountain_cam_night_vision_help']  = 'Équipez les caméras de vision nocturne infrarouge pour diffuser les conditions en soirée et de nuit, boostant la demande de visiteurs de +' . (int)((MOUNTAIN_CAM_NIGHT_VISION_VISITOR_MULT - 1) * 100) . ' % (+' . MOUNTAIN_CAM_NIGHT_VISION_COST_PER_DAY . ' €/jour supplémentaires).';
$lang['building']['mountain_cam_weather_overlay_label'] = 'Incrustation météo';
$lang['building']['mountain_cam_weather_overlay_help']  = 'Affichez la température, les chutes de neige et le vent en direct sur les flux webcam pour renforcer la confiance des visiteurs, avec +' . MOUNTAIN_CAM_WEATHER_OVERLAY_REP_BONUS . ' réputation/jour (+' . MOUNTAIN_CAM_WEATHER_OVERLAY_COST_PER_DAY . ' €/jour supplémentaires).';
$lang['building']['mountain_cam_settings_saved']     = '<div class="alert alert-success text-center">Paramètres des webcams de montagne enregistrés avec succès.</div>';
$lang['building']['mountain_cam_invalid_settings']   = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['mountain_cam_save_error']         = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';

// Système d'urgence et de secours
$lang['building']['emergency_title']              = '🚑 Système d\'urgence et de secours';
$lang['building']['emergency_page_intro']         = 'Gérez les équipes de secours en avalanche et les postes médicaux pour protéger vos clients. Le temps de réponse affecte la sécurité, la réputation et votre exposition financière en cas d\'incidents.';
$lang['building']['emergency_how_it_works']       = 'Comment ça fonctionne';
$lang['building']['emergency_how_it_works_desc']  = 'Chaque nuit, le jeu simule la préparation aux urgences. Un temps de réponse rapide booste la réputation ; un temps lent la fait baisser. Il y a ' . EMERGENCY_INCIDENT_CHANCE_PCT . '% de chance nocturne d\'incident — sans assurance, vous faites face à une lourde amende.';
$lang['building']['emergency_mechanic_rescue']    = 'L\'équipe de secours en avalanche réduit le temps de réponse jusqu\'à ' . EMERGENCY_RESCUE_RESPONSE_REDUCTION[3] . ' min (niveau avancé).';
$lang['building']['emergency_mechanic_medical']   = 'Les postes médicaux réduisent le temps de réponse jusqu\'à ' . EMERGENCY_MEDICAL_RESPONSE_REDUCTION[3] . ' min (niveau avancé).';
$lang['building']['emergency_mechanic_insurance'] = 'L\'assurance coûte ' . EMERGENCY_INSURANCE_DAILY_COST . ' €/nuit mais plafonne les amendes d\'incident à ' . EMERGENCY_FINE_WITH_INSURANCE . ' € au lieu de ' . EMERGENCY_FINE_NO_INSURANCE . ' €.';
$lang['building']['emergency_mechanic_reputation'] = 'Réponse < ' . EMERGENCY_RESPONSE_FAST_THRESHOLD . ' min : +' . EMERGENCY_REP_FAST_RESPONSE_BONUS . ' rép/nuit. Réponse > ' . EMERGENCY_RESPONSE_POOR_THRESHOLD . ' min : ' . EMERGENCY_REP_POOR_RESPONSE_PENALTY . ' rép/nuit.';
$lang['building']['emergency_settings_title']     = 'Paramètres d\'urgence';
$lang['building']['emergency_rescue_label']       = 'Équipe de secours en avalanche';
$lang['building']['emergency_rescue_help']        = 'Des niveaux plus élevés réduisent le temps de réponse et le coût de fonctionnement journalier augmente en conséquence.';
$lang['building']['emergency_medical_label']      = 'Postes médicaux';
$lang['building']['emergency_medical_help']       = 'Les postes médicaux en piste réduisent encore le temps de réponse.';
$lang['building']['emergency_insurance_label']    = 'Activer l\'assurance risques';
$lang['building']['emergency_insurance_help']     = 'L\'assurance limite les dommages financiers des incidents mais coûte une prime journalière.';
$lang['building']['emergency_save_btn']           = 'Enregistrer les paramètres';
$lang['building']['emergency_status_title']       = 'Situation actuelle';
$lang['building']['emergency_level_0']            = 'Aucun';
$lang['building']['emergency_level_1']            = 'Basique';
$lang['building']['emergency_level_2']            = 'Standard';
$lang['building']['emergency_level_3']            = 'Avancé';
$lang['building']['emergency_per_night']          = 'nuit';
$lang['building']['emergency_response_time_label'] = 'Temps de réponse';
$lang['building']['emergency_response_fast']      = 'Rapide';
$lang['building']['emergency_response_average']   = 'Moyen';
$lang['building']['emergency_response_poor']      = 'Lent';
$lang['building']['emergency_insurance_status_label'] = 'Assurance';
$lang['building']['emergency_insurance_on']       = 'Active';
$lang['building']['emergency_insurance_off']      = 'Non couverte';
$lang['building']['emergency_daily_cost_label']   = 'Coût journalier total';
$lang['building']['emergency_incident_chance_label'] = 'Probabilité d\'incident';
$lang['building']['emergency_fine_label']         = 'Amende par incident';
$lang['building']['emergency_fine_insured_note']  = 'couvert par l\'assurance';
$lang['building']['emergency_rep_effect_label']   = 'Effet nocturne sur la réputation';
$lang['building']['emergency_rep_per_night']      = 'pts/nuit';
$lang['building']['emergency_settings_saved']     = '<div class="alert alert-success text-center">Paramètres d\'urgence enregistrés avec succès.</div>';
$lang['building']['emergency_invalid_settings']   = '<div class="alert alert-danger text-center">Paramètres invalides. Veuillez vérifier les valeurs et réessayer.</div>';
$lang['building']['emergency_save_error']         = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';

// Commerce & Boutiques
$lang['building']['retail_title']              = 'Commerce & Boutiques';
$lang['building']['retail_page_intro']         = 'Gérez vos boutiques, cafés et bars en bord de piste. Définissez les niveaux de stock et les stratégies de prix pour maximiser les revenus, et activez les articles saisonniers pour un boost hivernal.';
$lang['building']['retail_how_it_works']       = 'Comment ça fonctionne';
$lang['building']['retail_how_it_works_desc']  = 'Chaque boutique ouverte génère des revenus journaliers selon son niveau de stock, sa stratégie de prix et sa popularité actuelle. La popularité évolue chaque nuit — un stock élevé et des prix abordables satisfont les clients, tandis qu\'un stock bas ou des prix premium l\'érodent.';
$lang['building']['retail_mechanic_stock']     = 'Niveau de stock (1–5) : un stock élevé maintient les rayons pleins, boostant popularité et potentiel de revenus.';
$lang['building']['retail_mechanic_pricing']   = 'Stratégie de prix : Budget (+popularité, ×0.7 revenus), Standard (neutre, ×1.0), Premium (−popularité, ×1.4 revenus).';
$lang['building']['retail_mechanic_popularity']= 'La popularité (0–100) évolue chaque nuit selon le stock et les prix. Les revenus sont calculés proportionnellement à la popularité ÷ 50.';
$lang['building']['retail_mechanic_seasonal']  = 'Les articles saisonniers (vêtements chauds, souvenirs, équipements) donnent un bonus de revenus de +' . (int)(RETAIL_SEASONAL_BONUS * 100 - 100) . '% quand la station est ouverte.';
$lang['building']['retail_shop_ski_shop']          = '🎿 Magasin de ski';
$lang['building']['retail_shop_souvenir_shop']     = '🧸 Boutique de souvenirs';
$lang['building']['retail_shop_cafe']              = '☕ Café';
$lang['building']['retail_shop_bar']               = '🍺 Bar';
$lang['building']['retail_shop_ski_shop_desc']          = 'Vend du matériel, des vêtements chauds et des équipements de ski. Populaire en saison.';
$lang['building']['retail_shop_souvenir_shop_desc']     = 'Vend des souvenirs et cadeaux de la station. Attire les visiteurs cherchant un souvenir.';
$lang['building']['retail_shop_cafe_desc']              = 'Café en bord de piste servant boissons chaudes et en-cas. Un favori des skieurs en pause.';
$lang['building']['retail_shop_bar_desc']               = 'Bar après-ski pour se détendre après une journée sur les pistes.';
$lang['building']['retail_enable_label']       = 'Ouvrir cette boutique';
$lang['building']['retail_stock_label']        = 'Niveau de stock';
$lang['building']['retail_stock_help']         = 'Contrôle la qualité du stock. Un stock élevé améliore la popularité et les revenus mais coûte plus cher à maintenir.';
$lang['building']['retail_pricing_label']      = 'Stratégie de prix';
$lang['building']['retail_pricing_budget']     = 'Budget (abordable)';
$lang['building']['retail_pricing_standard']   = 'Standard';
$lang['building']['retail_pricing_premium']    = 'Premium (haut de gamme)';
$lang['building']['retail_pricing_help']       = 'Budget : plus populaire, marge plus faible. Premium : marge plus élevée, pénalité de popularité.';
$lang['building']['retail_seasonal_label']     = 'Articles saisonniers en stock';
$lang['building']['retail_seasonal_help']      = 'Vêtements chauds, souvenirs de station et équipements saisonniers. Ajoute un bonus de revenus de +' . (int)(RETAIL_SEASONAL_BONUS * 100 - 100) . '% lorsque la station de ski est ouverte.';
$lang['building']['retail_popularity_label']   = 'Popularité';
$lang['building']['retail_open']               = 'Ouvert';
$lang['building']['retail_closed']             = 'Fermé';
$lang['building']['retail_save_btn']           = 'Enregistrer les paramètres';
$lang['building']['retail_revenue_guide_title'] = 'Référence des revenus';
$lang['building']['retail_revenue_guide_desc']  = 'Revenus journaliers de base au niveau de stock 3, prix standard, popularité 50, et station ouverte :';
$lang['building']['retail_col_shop']            = 'Boutique';
$lang['building']['retail_col_base_rev']        = 'Revenu de base / jour';
$lang['building']['retail_col_seasonal_bonus']  = 'Avec articles saisonniers';
$lang['building']['retail_revenue_guide_note']  = 'Les revenus réels varient selon le niveau de stock, la popularité et la stratégie de prix. Valeurs affichées pour stock 3, prix standard, popularité 50.';
$lang['building']['retail_settings_saved']     = '<div class="alert alert-success text-center">Paramètres de commerce enregistrés avec succès.</div>';
$lang['building']['retail_save_error']         = '<div class="alert alert-danger text-center">Une erreur s\'est produite lors de l\'enregistrement. Veuillez réessayer.</div>';

// Partenariats & Sponsors
$lang['building']['sponsorship_title']             = '🏅 Partenariats & Sponsors';
$lang['building']['sponsorship_page_intro']        = 'Signez des contrats avec des équipementiers, des marques de vêtements et des sponsors d\'événements pour gagner des revenus journaliers et débloquer des bonus pour la station. Maintenez une bonne réputation pour conserver la satisfaction de vos sponsors.';
$lang['building']['sponsorship_how_it_works']      = 'Comment ça fonctionne';
$lang['building']['sponsorship_how_it_works_desc'] = 'Chaque contrat de sponsoring génère un paiement journalier et peut débloquer un bonus de jeu. Chaque nuit, le jeu vérifie si la réputation de votre station atteint le seuil minimum du sponsor et ajuste sa satisfaction en conséquence. Si la satisfaction atteint 0, le sponsor annule le contrat.';
$lang['building']['sponsorship_mechanic_revenue']  = 'Chaque contrat actif verse un revenu journalier fixe selon son niveau.';
$lang['building']['sponsorship_mechanic_bonus']    = 'Certains sponsors offrent des bonus : maintenance des remontées moins chère, visiteurs supplémentaires ou gains de réputation journaliers.';
$lang['building']['sponsorship_mechanic_satisfaction'] = 'La satisfaction de la marque (0–100) augmente de ' . SPONSORSHIP_SATISFACTION_GAIN . ' pts/jour quand la réputation est atteinte, et baisse de ' . SPONSORSHIP_SATISFACTION_LOSS . ' pts/jour quand ce n\'est pas le cas.';
$lang['building']['sponsorship_mechanic_cancel']   = 'Un sponsor se retire (et vous coûte ' . SPONSORSHIP_CANCEL_REP_PENALTY . ' réputation) si sa satisfaction atteint 0.';
$lang['building']['sponsorship_type_lift_equipment'] = '⚙️ Sponsor Équipements';
$lang['building']['sponsorship_type_apparel']        = '🎿 Sponsor Vêtements';
$lang['building']['sponsorship_type_energy_drink']   = '⚡ Sponsor Boisson Énergisante';
$lang['building']['sponsorship_type_resort_map']     = '🗺️ Publicité Plan de Pistes';
$lang['building']['sponsorship_type_event_title']    = '🏆 Sponsor Titre Événement';
$lang['building']['sponsorship_desc_lift_equipment'] = 'Une marque d\'équipements co-sponsorise la maintenance des remontées, réduisant vos coûts d\'entretien journaliers.';
$lang['building']['sponsorship_desc_apparel']        = 'Une marque de vêtements de ski fait la promotion de votre station, attirant plus de visiteurs chaque jour.';
$lang['building']['sponsorship_desc_energy_drink']   = 'Une marque de boissons énergisantes populaire place des publicités dans toute la station, générant des revenus journaliers fixes.';
$lang['building']['sponsorship_desc_resort_map']     = 'Publicité sur le plan de pistes : les marques paient pour figurer sur vos plans de pistes imprimés et numériques.';
$lang['building']['sponsorship_desc_event_title']    = 'Devenez partenaire de site événementiel, gagnant des revenus journaliers et un petit boost de réputation quotidien.';
$lang['building']['sponsorship_bonus_maintenance']  = 'économie maintenance remontées';
$lang['building']['sponsorship_bonus_visitors']     = 'boost visiteurs';
$lang['building']['sponsorship_bonus_reputation']   = 'réputation/jour';
$lang['building']['sponsorship_bonus_revenue_only'] = 'Revenu journalier uniquement';
$lang['building']['sponsorship_level_1'] = 'Basique';
$lang['building']['sponsorship_level_2'] = 'Standard';
$lang['building']['sponsorship_level_3'] = 'Premium';
$lang['building']['sponsorship_col_sponsor']      = 'Sponsor';
$lang['building']['sponsorship_col_level']        = 'Niveau';
$lang['building']['sponsorship_col_revenue']      = 'Revenu journalier';
$lang['building']['sponsorship_col_bonus']        = 'Bonus';
$lang['building']['sponsorship_col_satisfaction'] = 'Satisfaction marque';
$lang['building']['sponsorship_col_action']       = 'Action';
$lang['building']['sponsorship_col_min_rep']      = 'Rép. min.';
$lang['building']['sponsorship_col_sign_cost']    = 'Frais de signature';
$lang['building']['sponsorship_per_day']          = 'jour';
$lang['building']['sponsorship_active_title']    = 'Contrats actifs';
$lang['building']['sponsorship_available_title'] = 'Sponsors disponibles';
$lang['building']['sponsorship_available_desc']  = 'Payez des frais de signature uniques pour activer un contrat. Vous pouvez avoir un contrat par type de sponsor à la fois.';
$lang['building']['sponsorship_sign_btn']           = 'Signer le contrat';
$lang['building']['sponsorship_terminate_btn']      = 'Résilier';
$lang['building']['sponsorship_select_level']       = 'Sélectionner le niveau';
$lang['building']['sponsorship_terminate_confirm']  = 'Êtes-vous sûr de vouloir résilier ce contrat ?';
$lang['building']['sponsorship_already_active']     = '✓ Contrat actif';
$lang['building']['sponsorship_signed']              = '<div class="alert alert-success text-center">Contrat de sponsoring signé avec succès !</div>';
$lang['building']['sponsorship_terminated']          = '<div class="alert alert-warning text-center">Contrat de sponsoring résilié.</div>';
$lang['building']['sponsorship_invalid_type']        = '<div class="alert alert-danger text-center">Type de sponsor invalide.</div>';
$lang['building']['sponsorship_invalid_level']       = '<div class="alert alert-danger text-center">Niveau de contrat invalide.</div>';
$lang['building']['sponsorship_rep_too_low']         = '<div class="alert alert-danger text-center">La réputation de votre station est trop basse pour ce niveau de contrat.</div>';
$lang['building']['sponsorship_insufficient_funds']  = '<div class="alert alert-danger text-center">Fonds insuffisants pour payer les frais de signature.</div>';
$lang['building']['sponsorship_error']               = '<div class="alert alert-danger text-center">Une erreur s\'est produite. Veuillez réessayer.</div>';
$lang['building']['sponsorship_signed_log']          = 'Contrat de sponsoring signé :';
$lang['building']['sponsorship_revenue_log']         = 'Revenus de sponsoring reçus :';
$lang['building']['sponsorship_cancelled_log']       = 'Le sponsor a annulé le contrat (satisfaction de marque à 0) :';
$lang['building']['sponsorship_rep_bonus_log']       = 'Bonus de réputation du sponsor titre événement :';

// Quiz de station de ski
$lang['building']['sqz_title']               = 'Quiz Station de Ski';
$lang['building']['sqz_intro']               = 'Testez vos connaissances sur le ski et la gestion des stations avec ce quiz à choix multiples. Répondez à 10 questions et découvrez votre score !';
$lang['building']['sqz_secret_title']        = 'Entrer le code secret';
$lang['building']['sqz_secret_desc']         = 'Cette zone est protégée. Entrez le code secret pour accéder au Quiz Station de Ski.';
$lang['building']['sqz_secret_label']        = 'Code secret';
$lang['building']['sqz_secret_placeholder']  = 'Entrez le code…';
$lang['building']['sqz_secret_submit']       = 'Déverrouiller';
$lang['building']['sqz_secret_error']        = '<div class="alert alert-danger">Code secret incorrect. Veuillez réessayer.</div>';
$lang['building']['sqz_instructions']        = 'Répondez à 10 questions à choix multiples sur le ski et la gestion des stations. Sélectionnez la bonne réponse pour chaque question. Bonne chance !';
$lang['building']['sqz_btn_start']           = 'Commencer le quiz';
$lang['building']['sqz_btn_next']            = 'Question suivante';
$lang['building']['sqz_btn_restart']         = 'Rejouer';
$lang['building']['sqz_question']            = 'Question';
$lang['building']['sqz_score']               = 'Score';
$lang['building']['sqz_feedback_correct']    = '✅ Correct ! Bien joué.';
$lang['building']['sqz_feedback_incorrect']  = '❌ Incorrect. La bonne réponse est mise en évidence en vert.';
$lang['building']['sqz_result_title']        = 'Quiz terminé !';
$lang['building']['sqz_result_score_label']  = 'Votre score :';
$lang['building']['sqz_result_gold']         = 'Excellent ! Vous êtes un vrai expert des stations de ski ! 🎿';
$lang['building']['sqz_result_silver']       = 'Bon effort ! Vous connaissez bien la montagne.';
$lang['building']['sqz_result_bronze']       = 'Pas mal ! Un peu plus de pratique et vous ferez mieux.';
$lang['building']['sqz_result_try_again']    = 'Retour sur les pistes débutants ! Continuez à vous entraîner.';

// ── Gouvernement & Réglementations ──────────────────────────────────────────
$lang['building']['gov_title']                   = 'Gouvernement &amp; Réglementations';
$lang['building']['gov_page_intro']              = 'Équilibrez profits et conformité réglementaire. La protection de l\'environnement peut limiter votre expansion, les inspections de sécurité entraînent des amendes ou des récompenses, et le gouvernement ajuste son taux de taxe réglementaire chaque saison. Restez conforme et bénéficiez de subventions écologiques.';
$lang['building']['gov_compliance_title']        = 'Score de conformité';
$lang['building']['gov_compliance_desc']         = 'Votre conformité réglementaire globale. Maintenez-la haute pour éviter le blocage des extensions et bénéficier d\'un risque d\'audit réduit.';
$lang['building']['gov_expansion_blocked_warning'] = '⚠ Extension bloquée par le gouvernement en raison d\'une faible conformité.';
$lang['building']['gov_tax_rate_title']          = 'Taux de taxe réglementaire';
$lang['building']['gov_tax_rate_desc']           = 'Appliqué chaque nuit sur le chiffre d\'affaires brut de la veille. Le taux est réinitialisé à chaque nouvelle saison.';
$lang['building']['gov_tax_rate_range']          = 'Plage possible';
$lang['building']['gov_tax_rate_season']         = 'Taux fixé en saison';
$lang['building']['gov_audit_title']             = 'Inspection de sécurité';
$lang['building']['gov_audit_pass']              = '✅ Réussie';
$lang['building']['gov_audit_fail']              = '❌ Échouée';
$lang['building']['gov_audit_none']              = 'Aucune inspection';
$lang['building']['gov_audit_last_date']         = 'Dernière inspection';
$lang['building']['gov_audit_desc']              = 'Probabilité journalière aléatoire d\'une inspection surprise. Réussir rapporte une récompense ; échouer entraîne une amende.';
$lang['building']['gov_subsidy_title']           = 'Subvention écologique';
$lang['building']['gov_subsidy_desc']            = 'Le gouvernement accorde une subvention à chaque nouvelle saison lorsque votre réputation écologique atteint le seuil requis.';
$lang['building']['gov_subsidy_eco_threshold']   = 'Réputation éco requise';
$lang['building']['gov_subsidy_your_eco']        = 'Votre réputation éco';
$lang['building']['gov_subsidy_available_label'] = 'Subvention disponible';
$lang['building']['gov_subsidy_claim_btn']       = 'Réclamer la subvention';
$lang['building']['gov_subsidy_none']            = 'Aucune subvention disponible';
$lang['building']['gov_subsidy_how_to_earn']     = 'Améliorez votre réputation écologique au-dessus du seuil pour obtenir une subvention au début de la prochaine saison.';
$lang['building']['gov_subsidy_claimed']         = '<div class="alert alert-success">Subvention réclamée avec succès !</div>';
$lang['building']['gov_no_subsidy_available']    = '<div class="alert alert-warning">Aucune subvention n\'est actuellement disponible.</div>';
$lang['building']['gov_subsidy_claimed_log']     = 'Subvention écologique réclamée :';
$lang['building']['gov_how_it_works_title']      = 'Fonctionnement';
$lang['building']['gov_mechanic_compliance']     = 'Votre <strong>score de conformité</strong> évolue chaque nuit en fonction de votre réputation écologique et de votre statut d\'expansion.';
$lang['building']['gov_mechanic_expansion']      = '<strong>Protection de l\'environnement</strong> : si la conformité passe sous ' . GOV_COMPLIANCE_BLOCK_THRESHOLD . ', le gouvernement bloque l\'expansion de la station.';
$lang['building']['gov_mechanic_audit']          = '<strong>Inspections de sécurité</strong> : ' . GOV_AUDIT_CHANCE . '% de chance quotidienne d\'une inspection surprise. Conformité ≥ ' . GOV_AUDIT_PASS_THRESHOLD . ' = réussite (+' . GOV_AUDIT_PASS_REWARD . ' €) ; en dessous = échec (-' . GOV_AUDIT_FAIL_FINE . ' €).';
$lang['building']['gov_mechanic_subsidy']        = '<strong>Subventions écologiques</strong> : lorsqu\'une nouvelle saison commence et que votre réputation éco est ≥ ' . GOV_SUBSIDY_ECO_THRESHOLD . ', vous recevez une subvention de ' . number_format(GOV_SUBSIDY_AMOUNT, 0, ',', ' ') . ' € à réclamer.';
$lang['building']['gov_mechanic_tax']            = '<strong>Taxe réglementaire</strong> : une taxe supplémentaire de ' . GOV_TAX_RATE_MIN . '–' . GOV_TAX_RATE_MAX . '% sur le chiffre d\'affaires brut de la veille est prélevée chaque nuit. Le taux est réassigné aléatoirement à chaque nouvelle saison.';
$lang['building']['gov_table_event']             = 'Événement';
$lang['building']['gov_table_effect']            = 'Effet';
$lang['building']['gov_row_eco_high']            = 'Réputation éco ≥ 70';
$lang['building']['gov_row_eco_low']             = 'Réputation éco &lt; 30';
$lang['building']['gov_row_expansion_restricted'] = 'Restriction d\'expansion carbone';
$lang['building']['gov_row_audit_pass']          = 'Inspection réussie';
$lang['building']['gov_row_audit_fail']          = 'Inspection échouée';
$lang['building']['gov_row_tax_rate']            = 'Taxe réglementaire';
$lang['building']['gov_row_tax_rate_note']       = 'du CA brut de la veille (réinitialisé chaque saison)';
$lang['building']['gov_row_subsidy']             = 'Subvention éco (nouvelle saison)';
$lang['building']['gov_row_subsidy_note']        = 'si réputation éco ≥ ' . GOV_SUBSIDY_ECO_THRESHOLD;
$lang['building']['gov_unit_compliance']         = 'conformité';
$lang['building']['gov_unit_night']              = 'nuit';
$lang['building']['gov_updated_nightly']         = 'La conformité, les audits, les taxes et les subventions sont traités automatiquement chaque nuit.';
$lang['building']['gov_stats_title']             = 'Résumé global';
$lang['building']['gov_stats_fines']             = 'Total des amendes réglementaires payées';
$lang['building']['gov_stats_subsidies']         = 'Total des subventions reçues';

// Night skiing event strings (French)
$lang['building']['night_skiing_events_title']         = 'Événements nocturnes spéciaux';
$lang['building']['night_skiing_events_intro']         = 'Planifiez des événements ponctuels pour booster la fréquentation, les revenus et la réputation pour une nuit.';
$lang['building']['night_skiing_event_dj_night']       = 'Soirée DJ';
$lang['building']['night_skiing_event_race_night']     = 'Nuit de course';
$lang['building']['night_skiing_event_torchlight_parade'] = 'Descente aux flambeaux';
$lang['building']['night_skiing_event_schedule']       = 'Planifier';
$lang['building']['night_skiing_event_cancel']         = 'Annuler';
$lang['building']['night_skiing_event_scheduled']      = 'Planifié';
$lang['building']['night_skiing_event_completed']      = 'Terminé';
$lang['building']['night_skiing_event_cancelled']      = 'Annulé';
$lang['building']['night_skiing_event_no_events']      = 'Aucun événement planifié.';
$lang['building']['night_skiing_grooming_label']       = 'Supplément damage nocturne';
$lang['building']['night_skiing_dynamic_demand_label'] = 'Fréquentation nocturne';
$lang['building']['night_skiing_trail_cards_title']    = 'Éclairage des pistes';
$lang['building']['night_skiing_trail_cards_intro']    = 'Configurez l\'éclairage pour chaque piste. Activez le ski nocturne par piste ou cliquez sur Configurer.';
$lang['building']['night_skiing_live_preview_title']   = 'Aperçu des revenus en direct';
$lang['building']['night_skiing_est_visitors']         = 'Visiteurs nocturnes estimés';
$lang['building']['night_skiing_est_revenue']          = 'Revenus totaux estimés';
$lang['building']['night_skiing_est_costs']            = 'Coûts totaux estimés';
$lang['building']['night_skiing_est_net']              = 'Net estimé';
$lang['building']['night_skiing_forecast_title']       = 'Prévisions de fréquentation';
$lang['building']['night_skiing_forecast_explainer']   = 'Estime les visiteurs nocturnes de ce soir en fonction du prix, des horaires, des événements et de la météo.';
$lang['building']['night_skiing_cost_breakdown_title'] = 'Répartition des coûts nocturnes';
$lang['building']['night_skiing_revenue_trends_title'] = 'Tendances des revenus';
$lang['building']['night_skiing_revenue_trends_help']  = 'Répartition récente des revenus nocturnes entre billets, école de ski, photos et événements.';
$lang['building']['night_skiing_tonight_event_badge']  = 'Événement de ce soir';
