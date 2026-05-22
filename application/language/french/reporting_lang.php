<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Reporting view
$lang['reporting']['intro']	= 'Sur cette page, tu peux commander une analyse complète de ta station. Un groupe d\'experts produira un rapport concis et t\'informera de toutes les améliorations possibles pour améliorer sa rentabilité. <br>Après la commande, le rapport sera disponible le jour suivant (après 01h00 GMT) et sera disponible pour toujours.';
$lang['reporting']['cost_report']	= 'Le coût d\'un rapport est de';
$lang['reporting']['example_below']	= 'Tu peux trouver un exemple de rapport ici: ';
$lang['reporting']['order_report']	= 'Tu peux commander l\'analyse pour aujourd\'hui en cliquant sur le bouton ci-dessous';
$lang['reporting']['order']	= 'Commander';
$lang['reporting']['report_ordered']	= '<div class="alert alert-success text-center">Analyse commandée avec succès ! Reviens après 01h00 du matin GMT pour voir le rapport.</div>';
$lang['reporting']['report_not_ordered']	= '<div class="alert alert-danger text-center">L\'analyse n\'a pas pu être commandée ! Essaie à nouveau ou contactez-nous à contact@ski-manager.net.</div>';
$lang['reporting']['not_enough_genepis']	= '<div class="alert alert-warning text-center">Tu n\'as pas assez de Génépis pour commander l\'analyse. Obtiens plus de Génépis depuis <a href="'.base_url().'genepis_controller">la page Génépis</a>.</div>';
$lang['reporting']['already_ordered_today']	= '<div class="alert alert-warning text-center">Tu as déjà commandé une analyse aujourd\'hui. Reviens après 01h00 GMT pour voir le rapport.</div>';
$lang['reporting']['date']	= 'Date';
$lang['reporting']['status']	= 'Statut';
$lang['reporting']['view']	= 'Visualiser';
$lang['reporting']['download']	= 'Télécharger';
$lang['reporting']['or']	= 'ou';
$lang['reporting']['resort_analysis']	= 'Analyse de la station';


// Night main scripts
$lang['reporting']['avg_quality_slopes']	= 'La qualité moyenne des pistes désservies par la remontée';
$lang['reporting']['is_only']                   = 'est seulement';
$lang['reporting']['improve_their_quality']	= 'Améliore leur qualité pour attirer plus de touristes sur cette remontée';
$lang['reporting']['no_slopes_connected']	= 'Il n\'y a pas de piste reliée à la remontée';
$lang['reporting']['build_more_slopes']         = 'Construis quelques pistes supplémentaires à proximité de cette remontée pour attirer plus de touristes.';
$lang['reporting']['too_few_slopes_connected']	= 'Il y a trop peu de pistes reliées à la remontée';
$lang['reporting']['adequate_slopes_connected']	= 'Il y a un nombre adéquat de pistes reliées à la remontée';
$lang['reporting']['build_few_slopes']          = 'Construire quelques pistes supplémentaires à proximité de cette remontée pour attirer encore plus de touristes.';
$lang['reporting']['not_necessary_slopes']	= 'Il n\'est pas nécessaire de construire d\'autres pistes à proximité de cette remontée.';
$lang['reporting']['unknown_error_lift']	= 'Erreur inconnue avec les remontées';
$lang['reporting']['the_lift']                  = 'Avec le nombre de pistes connectées et le niveau actuel, la remontée';
$lang['reporting']['could_attract_max']                  = 'peut gérer jusqu\'à';
$lang['reporting']['tourists']                  = 'touristes';
$lang['reporting']['also_includes']                  = 'Ceci inclut également les touristes qui viennent pour la journée seulement.';
$lang['reporting']['all_your_lifts']                  = 'Toutes les remontées mécaniques';
$lang['reporting']['your_resort_hotel_capacity']                  = 'Ta station a une capacité d\'hébergement de';
$lang['reporting']['thanks_to_access_resort_housing_capacity']                  = 'Grâce au bonus donné par l\'accès à ta station, la capacité d\'accueil réelle est de';
$lang['reporting']['no_hotel_in_resort']                  = 'Tu n\'as pas d\'hôtel dans ta station, ce qui limite fortement ta capacité à attirer les touristes.';
$lang['reporting']['lift_capacity_too_low']                  = 'Tes remontées mécaniques ne sont pas en mesure de prendre en charge tous les touristes qui visitent ta station pour la journée ou qui séjournent dans tes hôtels. Augmente ta capacité en terme de remontées mécaniques.';
$lang['reporting']['infrastructure_capacity_too_low']                  = 'Vos remontées mécaniques pourraient accueillir plus de touristes, mais le nombre actuel de touristes est limité par l\'infrastructure de ta station. Construis plus d\'hôtels, améliore ceux qui existent déjà ou améliore l\'accès à ta station pour attirer plus de touristes.';
$lang['reporting']['no_open_lifts']                  = 'Il n\'y a pas de remontées mécaniques ouvertes dans ta station. Ouvre plus de remontées pour attirer les touristes';
$lang['reporting']['no_slopes_deserving']                  = 'Il n\'y a pas de piste desservie par la remontée mécanique';
$lang['reporting']['build_more_slopes']                  = 'Construire plus de pistes autour de cette remontée pour augmenter les revenus.';
$lang['reporting']['risk_injury_slope']                  = 'Il existe un risque de blessure de';
$lang['reporting']['on_slope']                  = 'sur la piste';
$lang['reporting']['reduce_injuries_tip']                  = 'Améliore la qualité de la piste en t\'assurant qu\'une patrouille de pisteur efficace lui est affectée et qu\'il y a suffisamment de neige dans ta station. Il est possible d\'affecter jusqu\'à '.MAX_PATROL_PER_SLOPE.' pisteurs par piste pour un bonus de sécurité.';
$lang['reporting']['danger_today']                  = 'La météo d\'hier était trop dangereuse pour skier et cela a causé trois fois plus de blessures que d\'habitude. Assure-toi de fermer ta station en cas de mauvais temps ou abonne-toi aux prévisions météo à 14 jours pour bénéficier de la fonction de fermeture automatique. Voir la page Météo pour plus d\'informations.';
$lang['reporting']['your_skibus_can_only']                  = 'Tes navettes de ski ne peuvent prendre en charge';
$lang['reporting']['your_skibus_can_handle']                  = 'Tes navettes de ski peuvent prendre en charge';
$lang['reporting']['perc_of_tourists']                  = '% des touristes de ta station.';
$lang['reporting']['buy_more_skibuses']                 = 'Achete plus de navettes de ski ou améliore celles qui existent déjà pour gagner plus d\'argent.';
$lang['reporting']['enough_skibuses']                  = 'Tu as assez de navettes de ski pour accueillir tous les touristes de ta station.';
$lang['reporting']['no_driver_or_skibus']                  = 'Tu n\'as pas de navettes de ski avec un chauffeur assigné. Achète au moins une navette et embauche un chauffeur. Affecte ensuite le chauffeur à la navette sur la page de vue d\'ensemble du personnel.';
$lang['reporting']['no_instructors']                  = 'Tu n\'as pas de moniteur de ski affecté à un secteur de ta station. Engage au moins un moniteur de ski/snowboard et assigne-le à un secteur de ta station sur la page de vue d\'ensemble du personnel.';
$lang['reporting']['avg_efficiency_drivers']                  = 'L\'efficacité moyenne de tes chauffeurs de bus n\'est que de';
$lang['reporting']['get_better_drivers']                  = 'Embauche des chauffeurs plus efficaces si tu veux augmenter les revenus générés par tes navettes de ski.';
$lang['reporting']['avg_efficiency_instructors']                  = 'L\'efficacité moyenne de tes moniteurs de ski/snowboard n\'est que de';
$lang['reporting']['get_better_instructors']                  = 'Embauche des moniteurs de ski/snowboard plus efficaces si tu veux augmenter les revenus générés par les écoles de ski.';
$lang['reporting']['no_building_type']                  = 'Tu n\'as pas de';
$lang['reporting']['building_buildings']                  = 'Augmente tes revenus en construisant ce type de bâtiment.';
$lang['reporting']['enough_building_type']                  = 'Tu as assez de';
$lang['reporting']['not_enough_building_type']                  = 'Tes bâtiments de type';
$lang['reporting']['of_your_visitors']                  = 'de tes visiteurs.';
$lang['reporting']['can_only_handle']                  = 'peut seulement gêrer';
$lang['reporting']['build_some_more']                  = 'Construise-en d\'autres pour accueillir tous les touristes de ta station et augmenter tes revenus.';
$lang['reporting']['to_handle_visitors']                  = 'pour gérer tous les touristes de ta station.';
$lang['reporting']['or_no_visitors']                  = 'ou il n\'y a pas eu de visiteurs dans ta station hier.';

// Reporting script

$lang['reporting']['resort_closed']                  = 'Ta station était fermé hier, donc aucun touriste n\'était présent. Ouvre ton Office du tourisme sur la page Accès & Parkings pour ouvrir ta station.';
$lang['reporting']['there_is_only']                  = 'Il y a seulement';
$lang['reporting']['low_snow_level']                  = 'cm de neige dans ta station et la qualité de tes pistes peut diminuer. Construis plus de canons à neige pour t\'assurer que le niveau de neige reste élevé.';
$lang['reporting']['your_lift']                  = 'La remontée';
$lang['reporting']['your_slope']                  = 'La piste';
$lang['reporting']['has_low_condition']                  = 'est en mauvais état';
$lang['reporting']['not_operate_well']                  = 'et ne fonctionne pas à sa capacité maximale. Répare la remontée en cliquant dessus sur la page Station puis choisis "réparer".';
$lang['reporting']['more_injuries']                  = 'et n\'attirera pas autant de touristes qu\'une piste en bon état. Les blessures ont aussi tendance à se produire davantage sur ces pistes. Essaye d\'augmenter la qualité de la piste en embauchant de meilleurs conducteurs de dameuses ou en achetant/améliorant des canons à neige (si le niveau de neige dans la station est bas).';
$lang['reporting']['is_closed']                  = 'était fermée hier. Ouvre la pour attirer les touristes et générer plus de revenus.';

$lang['reporting']['report_intro_text']                  = 'Un groupe d\'experts a évalué ta station pour t\'aider à améliorer sa rentabilité et sa réputation. Tu trouvera ci-dessous les différentes rubriques et commentaires:';
$lang['reporting']['type']                  = 'Type';
$lang['reporting']['comments']                  = 'Commentaires';
$lang['reporting']['report_for']                  = 'rapport pour';
$lang['reporting']['produced_on']                  = 'réalisé le';

$lang['reporting']['up_to']                  = 'Jusqu\'à';
$lang['reporting']['can_be_handled_lifts_downhil_slopes']                  = 'peuvent être accueillis par tes remontées, snowparks, bordercrosses, pistes alpines et pistes de luge.';
$lang['reporting']['have_used_cross_country_slopes']                  = 'ont skié sur tes pistes de ski de fond.';
$lang['reporting']['no_cross_country_clopes']                  = 'Tu n\'as pas de pistes de ski de fond dans ta station. Si tu as débloqué de nouveaux types de pistes, construis-en pour attirer les touristes et générer plus de revenus.';

$lang['reporting']['reporting_wiki']                  = 'Pour plus d\'informations et la liste des métriques analysées, consulte la <a href="'.base_url().'reporting_controller">page de rapport</a>.';

// Simulation IA du flux de visiteurs
$lang['reporting']['guest_flow_snow_factor']            = 'Simulation IA – facteur niveau de neige appliqué :';
$lang['reporting']['guest_flow_restaurant_factor']      = 'Facteur de proximité des restaurants appliqué :';
$lang['reporting']['black_diamond_injury_warning']    = 'La piste Double Noire "%s" présente un risque de blessure élevé en raison de sa difficulté extrême. Assure-toi d\'avoir une patrouille de ski efficace assignée.';
$lang['reporting']['black_diamond_rep_bonus']         = 'Ta station a gagné %d points de réputation grâce à %d piste(s) Double Noire ouverte(s) attirant des skieurs experts.';
// Risques naturels
$lang['reporting']['avalanche_risk_steep']     = 'Risque d\'avalanche : piste raide';
$lang['reporting']['avalanche_condition_loss'] = 'a perdu de la condition suite à une avalanche. Embauche une patrouille de ski et assure une gestion adéquate du manteau neigeux.';
$lang['reporting']['storm_damaged_lift']       = 'Dégâts de tempête : remontée';
$lang['reporting']['storm_condition_loss']     = 'a perdu de la condition en raison des dégâts de la tempête. Affecte un mécanicien pour maintenir la remontée en bon état.';
$lang['reporting']['ice_slopes_affected']      = 'Le gel a causé une accumulation de glace sur les pistes, réduisant leur condition. Assure-toi que les canons à neige sont opérationnels pour maintenir un manteau neigeux adéquat.';
