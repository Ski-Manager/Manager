<?php
//english file
defined('BASEPATH') OR exit('No direct script access allowed');


$lang['bank']['titleMain']		= 'Banque';
$lang['bank']['bank_name']		= 'Nom de la banque';
$lang['bank']['intro']                  = 'Obtenir des fonds supplémentaires pour donner un coup de boost au dévelopement de la station.';
$lang['bank']['desc']                   = 'Tu peux emprunter de l\'argent à une des banques, choisir la durée de l\'emprunt ainsi que son montant. Assure-toi de pouvoir te permettre les mensualités! Il est possible d\'écourter un prêt en cours en payant le restant dû.';
$lang['bank']['min_loan']		= 'Montant minimum';
$lang['bank']['max_loan']		= 'Montant maximum';
$lang['bank']['interest_rate']		= 'Taux d\'intérêt';
$lang['bank']['genepis_required']		= 'Génépis requis';
$lang['bank']['daily_payment']          = 'Paiement quotidien';
$lang['bank']['amount_to_borrow']	= 'Montant à emprunter';
$lang['bank']['loan_duration']		= 'Durée de l\'emprunt';
$lang['bank']['sign_up']		= 'Signer';
$lang['bank']['ongoing_loans']		= 'Emprunts en cours';
$lang['bank']['signed_on']		= 'Signé le';
$lang['bank']['left_to_pay']		= 'Montant restant dû';
$lang['bank']['last_payment_date']		= 'Date du dernier paiement';
$lang['bank']['sign_up_tooltip']	= 'Signer pour un emprunt avec cette banque';
$lang['bank']['amount_too_low']		= 'Montant trop faible pour cette banque';
$lang['bank']['amount_too_high']	= 'Montant trop élevé pour cette banque';
$lang['bank']['confirm_signup_loan']            = 'Êtes-vous sûr de vouloir souscrire à cet emprunt ?';
$lang['bank']['confirm_signup_do_you_want']	= 'Voulez-vous signer un emprunt avec';
$lang['bank']['with_daily_payment']	= 'avec un paiement quotidien de';
$lang['bank']['during']                 = 'pendant';
$lang['bank']['loan_signed_up']                 = 'L\'emprunt a été signé.';
$lang['bank']['loan_not_signed_up']                 = 'L\emprunt n\'a pas pu être signé. Essaye à nouveau ou contactez-nous à '.CONST_ADMIN_EMAIL;
$lang['bank']['not_enough_revenue']                 = 'Tes revenus actuels sont trop faibles pour souscrire à cette offre. Afin de prévenir l’endettement, le prêt n’a pas été signé. Réessaie lorsque tes revenus sont plus élevés ou diminue le paiement quotidien';
$lang['bank']['ongoing_loans_error']                 = 'Tu ne peux signer qu\'un emprunt standard à la fois ou choisir un à deux emprunts à la banque VIP (nécessite <a href="'.base_url().'genepis_controller">des Génépis</a>). Il existe également un maximum de deux emprunts simultanés (1 standard + 2 VIP ou 3 VIP).';

$lang['bank']['payoff']                 = 'Remboursement anticipé';
$lang['bank']['payoff_now']                 = 'Solder prêt maintenant';
$lang['bank']['payoff_help']                 = 'Rembourser ce prêt immédiatement te coûtera';
$lang['bank']['confirm_payoff_do_you_want']                 = 'Es-tu sûr de vouloir rembourser ce prêt immédiatement?';
$lang['bank']['will_be_directly_taken']                 = 'seront directement prélevés sur ton compte.';
$lang['bank']['not_enough_money_payoff']                 = 'Tu n\'as pas assez d\'argent pour rembourser ce prêt aujourd\'hui. Tu as besoin de';
$lang['bank']['for_this_action']                 = 'pour cette action.';
$lang['bank']['loan_not_payed_off']                 = 'L\emprunt n\'a pas pu être payé de façon anticipée. Essaye à nouveau ou contactez-nous à '.CONST_ADMIN_EMAIL;
$lang['bank']['based_last_week_profit']                 = 'Sur la base de votre bénéfice de la semaine dernière, tu peux emprunter de l\'argent avec un paiement quotidien maximal de';

// Historique des prêts
$lang['bank']['loan_history']       = 'Historique des prêts';
$lang['bank']['borrowed_amount']    = 'Montant emprunté';
$lang['bank']['reimbursed_on']      = 'Remboursé le';

// Compte épargne
$lang['bank']['investment_title']            = 'Compte épargne';
$lang['bank']['investment_desc']             = 'Dépose de l\'argent inactif sur un compte épargne et génère des intérêts quotidiens. Retrait possible à tout moment.';
$lang['bank']['investment_annual_rate']      = 'Taux d\'intérêt annuel';
$lang['bank']['investment_balance']          = 'Solde actuel';
$lang['bank']['investment_min_deposit']      = 'Dépôt minimum';
$lang['bank']['investment_max_balance']      = 'Solde maximum';
$lang['bank']['investment_amount']           = 'Montant';
$lang['bank']['investment_deposit_btn']      = 'Déposer';
$lang['bank']['investment_withdraw_btn']     = 'Retirer';
$lang['bank']['investment_confirm_deposit']  = 'Confirmes-tu le dépôt de';
$lang['bank']['investment_confirm_withdraw'] = 'Confirmes-tu le retrait de';
$lang['bank']['investment_success']          = 'Opération réussie.';
$lang['bank']['investment_error']            = 'Opération échouée. Réessaie.';
$lang['bank']['investment_deposited']        = 'Dépôt sur le compte épargne :';
$lang['bank']['investment_withdrawn']        = 'Retrait du compte épargne :';
$lang['bank']['investment_interest_log']     = 'Intérêts quotidiens sur l\'épargne :';
$lang['bank']['investment_min_deposit_error']= 'Le montant est inférieur au dépôt minimum.';
$lang['bank']['investment_max_balance_error']= 'Ce dépôt dépasserait le solde maximum autorisé.';
$lang['bank']['investment_withdraw_error']   = 'Montant de retrait invalide.';
