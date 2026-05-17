<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');

// Common to all staff pages
$lang['common_staff']['titleMain']		= 'Employés';

// Overview Staff page
$lang['overviewStaff']['title']		= 'Vue d\'ensemble du personnel';
$lang['overviewStaff']['intro']	= 'Sur cette page, tu peut voir un aperçu des employés actuellement embauchés. Tu peux également licencier le personnel si tu en as trop. Lorsqu\'un employé est licencié, pénalité salariale de trois mois doit être payée. N\'oublie pas d\'affecter chaque employé à son équipement, sa piste ou sa remontée mécanique. Les équipements, les remontées ou les pistes n\'apparaissent dans le menu déroulant que lorsque la construction ou livraison est terminée.';
$lang['overviewStaff']['assigned_to']		= 'Assigné à';
$lang['overviewStaff']['hiring_date']		= 'Date d\'embauche';
$lang['overviewStaff']['fire']                   = 'Licencier';
$lang['overviewStaff']['no_staff_hired']		= '<div class="alert alert-warning text-center">Tu n\'as pas encore embauché d\'employé. Fais-le depuis la <a href="'.base_url().'hire_staff_controller/">page de recrutement</a></div>';
$lang['overviewStaff']['staff_fired']            = 'L\'employé a été licencié avec succès! Trois mois de salaire ont été débités de ton compte bancaire.';
$lang['overviewStaff']['staff_not_fired']	= '<div class="alert alert-danger text-center">L\'employé n\'a pas pu être licencié.</div>';
$lang['overviewStaff']['not_updated_already_assigned']	= 'Il y a déjà quelqu\'un affecté à cette piste, à cette remontée mécanique ou à cet équipement. Choisis en un(e) autre.';
$lang['overviewStaff']['fire_tooltip']           = 'Le licenciement d\'un employé te coûtera trois fois son salaire mensuel. Cette action ne peut pas être annulée.';
$lang['overviewStaff']['confirm_fire']           = 'Es-tu sûr de vouloir licencier cet employé?<br>Tu devras payer trois fois son salaire mensuel.';
$lang['overviewStaff']['updated']           = 'Mis à jour';
$lang['overviewStaff']['available']           = 'disponible';

// Hire Staff page
$lang['hireStaff']['title']		= 'Embaucher du personnel';
$lang['hireStaff']['hire']                   = 'Embaucher';
$lang['hireStaff']['intro']		= 'Sur cette page, tu peux engager de nouveaux employés directement auprès de l\'agence pour l\'emploi. Plus l\'employé est efficace, plus son salaire est élevé!<br>Une fois qu\'un employé a été embauché, il doit être assigné à un équipement, une piste ou une remontée mécanique sur la page de <a href="'.base_url().'overview_staff_controller">vue d\'ensemble du personnel</a>';
$lang['hireStaff']['staff_hired']            = '<div class="alert alert-success text-center">L\'employé a été embauché avec succès! N\'oublie pas de l\'assigner à un équipement, une piste ou un remontée mécanique sur la page de <a href="'.base_url().'overview_staff_controller">vue d\'ensemble du personnel</a></div>';
$lang['hireStaff']['staff_not_hired']	= '<div class="alert alert-danger text-center">L\'employé n\'a pas pu être embauché.</div>';

// Staff type
$lang['hireStaff']['skipatrol']		= 'Pisteur secouriste';
$lang['hireStaff']['skiinstructor']		= 'Moniteur de ski/snowboard';
$lang['hireStaff']['liftmechanic']		= 'Mécanicien de remontée mécanique';
$lang['hireStaff']['mechanicGroomer']		= 'Mécanicien de dameuse';
$lang['hireStaff']['driver']		= 'Chauffeur de bus';
$lang['hireStaff']['snowmaker']		= 'Opérateur d\'enneigement';

// Headers
$lang['common_staff']['position']		= 'Poste';
$lang['common_staff']['efficiency']		= 'Efficacité';
$lang['common_staff']['salary']                 = 'Salaire';

$lang['hireStaff']['too_many_instructors']		= '<div class="alert alert-warning text-center">Tu as atteint le nombre maximum de moniteurs de ski dans ta station. Construis plus de pistes pour embaucher plus de moniteurs.</div>';
$lang['hireStaff']['not_enough_money']		= '<div class="alert alert-warning text-center">Pas assez d\'argent pour effectuer cette action.</div>';

// Candidate pool
$lang['hireStaff']['no_candidates']            = '<em>Aucun candidat disponible pour le moment. Actualisez le vivier pour voir de nouveaux postulants.</em>';
$lang['hireStaff']['candidate_unavailable']    = '<div class="alert alert-warning text-center">Ce candidat n\'est plus disponible. Le vivier a été actualisé.</div>';
$lang['hireStaff']['refresh_pool']             = 'Actualiser le vivier';
$lang['hireStaff']['refresh_tooltip']          = 'Remplacer les candidats actuels par de nouveaux postulants de l\'agence pour l\'emploi. Coûte '.CANDIDATE_REFRESH_COST.' €.';
$lang['hireStaff']['refresh_confirm']          = 'Remplacer les candidats actuels ? Cela coûte '.CANDIDATE_REFRESH_COST.' €.';
$lang['hireStaff']['pool_refreshed']           = 'De nouveaux candidats sont maintenant disponibles !';
$lang['hireStaff']['pool_expires_info']        = 'Les candidats sont disponibles pendant %d jours.';
$lang['hireStaff']['not_enough_money_short']   = 'Fonds insuffisants.';

// Contract
$lang['hireStaff']['contract_duration']        = 'Contrat';
$lang['hireStaff']['contract_months_fmt']      = '%d mois';
$lang['hireStaff']['min_contract']             = 'Contrat min.';
$lang['hireStaff']['per_month']                = 'mois';
$lang['hireStaff']['hire_bonus']               = 'Prime d\'embauche';

// Specializations
$lang['hireStaff']['spec_speed']               = 'Rapidité';
$lang['hireStaff']['spec_safety']              = 'Sécurité';
$lang['hireStaff']['spec_precision']           = 'Précision';
$lang['hireStaff']['spec_endurance']           = 'Endurance';
$lang['hireStaff']['spec_trainer']             = 'Formateur';
$lang['hireStaff']['spec_tech']                = 'Technicien';
$lang['hireStaff']['spec_none']                = 'Aucune';
// Specialization descriptions
$lang['hireStaff']['spec_desc_speed']          = 'Répond plus vite aux incidents — réduit le temps de réponse de cet employé.';
$lang['hireStaff']['spec_desc_safety']         = 'Accent particulier sur la sécurité — réduit la probabilité d\'accidents à l\'emplacement assigné.';
$lang['hireStaff']['spec_desc_precision']      = 'Travail minutieux — meilleure qualité de réparation et moins de pannes sur la durée.';
$lang['hireStaff']['spec_desc_endurance']      = 'Résistant aux conditions difficiles — pénalité de moral réduite lors des tempêtes.';
$lang['hireStaff']['spec_desc_trainer']        = 'Moniteur expert — les clients progressent plus vite dans la station.';
$lang['hireStaff']['spec_desc_tech']           = 'Mécanicien technophile — l\'équipement assigné à cet employé dure plus longtemps.';
$lang['hireStaff']['spec_desc_none']           = 'Aucune spécialisation.';

// Traits
$lang['hireStaff']['trait_hardworking']        = 'Travailleur';
$lang['hireStaff']['trait_easygoing']          = 'Décontracté';
$lang['hireStaff']['trait_sensitive']          = 'Sensible';
$lang['hireStaff']['trait_ambitious']          = 'Ambitieux';
$lang['hireStaff']['trait_loyal']              = 'Loyal';
$lang['hireStaff']['trait_none']               = 'Aucun';
// Trait descriptions
$lang['hireStaff']['trait_desc_hardworking']   = 'Gagne de l\'expérience plus vite — franchit les étapes de compétence plus tôt.';
$lang['hireStaff']['trait_desc_easygoing']     = 'Récupère rapidement son moral — rebondit après les mauvaises journées.';
$lang['hireStaff']['trait_desc_sensitive']     = 'Le moral baisse plus vite par mauvais temps ou en cas de manque de personnel.';
$lang['hireStaff']['trait_desc_ambitious']     = 'Le moral augmente lors d\'une promotion — s\'épanouit dans la progression.';
$lang['hireStaff']['trait_desc_loyal']         = 'Moins de pénalité de moral pour les longs contrats ou lors du licenciement.';
$lang['hireStaff']['trait_desc_none']          = 'Aucun trait particulier.';

// Skill levels (for overview page tooltips)
$lang['common_staff']['skill_level']           = 'Niveau de carrière';
$lang['common_staff']['experience']            = 'Expérience';

// Staff Morale
$lang['overviewStaff']['morale']                 = 'Moral';
$lang['overviewStaff']['on_strike']              = 'En grève !';
$lang['overviewStaff']['strike_alert']           = '⚠ Alerte grève !';
$lang['overviewStaff']['strike_count']           = '%d employé(s) sont actuellement en grève et ne travaillent pas.';
$lang['overviewStaff']['low_morale_warning']     = '⚠ Le moral du personnel est bas. Les réparations seront plus lentes et le risque d\'accidents est accru. Augmentez les salaires ou améliorez les conditions de travail pour éviter une grève.';
$lang['overviewStaff']['morale_tooltip']         = 'Moral du personnel (0-100). Influencé par la rémunération, la charge de travail et la météo. Un moral bas entraîne des réparations plus lentes, un risque d\'accidents plus élevé et une possible grève.';

// Staff Training
$lang['overviewStaff']['train']                  = 'Former';
$lang['overviewStaff']['train_tooltip']          = 'Envoyer cet employé en session de formation pour accélérer sa progression. Coûte ' . STAFF_TRAINING_COST . ' € et accorde ' . STAFF_TRAINING_XP . ' XP. Une fois toutes les ' . STAFF_TRAINING_COOLDOWN_HOURS . ' heures.';
$lang['overviewStaff']['train_info']             = 'Vous pouvez former n\'importe quel employé pour accélérer sa progression de carrière. Chaque session coûte';
$lang['overviewStaff']['train_info_xp']          = 'et accorde';
$lang['overviewStaff']['train_success']          = 'Formation terminée ! XP accordé.';
$lang['overviewStaff']['train_level_up']         = 'Niveau de carrière supérieur →';
$lang['overviewStaff']['train_max_level']        = 'Cet employé a déjà atteint le niveau de carrière maximum.';
$lang['overviewStaff']['train_cooldown']         = 'Cet employé a été formé récemment. Prochaine session disponible à :';
$lang['overviewStaff']['train_not_enough_cash']  = 'Fonds insuffisants pour payer la session de formation.';
$lang['overviewStaff']['train_error']            = 'Une erreur s\'est produite. Veuillez réessayer.';
$lang['overviewStaff']['train_log_entry']        = 'Session de formation du personnel :';


