<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// -----------------------------------------------------------------------
// Micro-événements – textes d'interface
// -----------------------------------------------------------------------
$lang['micro_events']['modal_title']        = '⚡ Décision rapide';
$lang['micro_events']['expires_in']         = 'Cet événement expire dans 24 heures.';
$lang['micro_events']['loading']            = 'Traitement de votre décision…';

// -----------------------------------------------------------------------
// vip_queue_jump
// Un client VIP insiste pour passer devant la file de remontée.
// Choix A : Le laisser passer (+€800, -3 réputation)
// Choix B : Faire respecter les règles (0, +4 réputation)
// -----------------------------------------------------------------------
$lang['micro_events']['vip_queue_jump_title']       = 'Demande de priorité VIP';
$lang['micro_events']['vip_queue_jump_description'] = 'Un client VIP bien connu exige de passer devant la file d\'attente de la remontée mécanique. Les autres skieurs regardent attentivement.';
$lang['micro_events']['vip_queue_jump_a_label']     = 'Le laisser passer';
$lang['micro_events']['vip_queue_jump_a_hint']      = '+€800, -3 réputation';
$lang['micro_events']['vip_queue_jump_b_label']     = 'Faire respecter les règles';
$lang['micro_events']['vip_queue_jump_b_hint']      = '+4 réputation';
$lang['micro_events']['vip_queue_jump_a_result']    = 'Le VIP est ravi et donne un généreux pourboire au personnel. Cependant, certains clients ont ronchonné face à ce traitement de faveur.';
$lang['micro_events']['vip_queue_jump_b_result']    = 'Vous avez tenu bon. Les autres clients ont applaudi ce traitement équitable, et la réputation de votre station a légèrement progressé.';

// -----------------------------------------------------------------------
// press_interview
// Un journaliste demande une interview rapide sur la station.
// Choix A : Accorder l'interview (-€500, +8 réputation)
// Choix B : Décliner poliment (0, -2 réputation)
// -----------------------------------------------------------------------
$lang['micro_events']['press_interview_title']       = 'Demande d\'interview presse';
$lang['micro_events']['press_interview_description'] = 'Un journaliste d\'un magazine de voyage populaire souhaite une courte interview sur votre station. Ce pourrait être une excellente publicité.';
$lang['micro_events']['press_interview_a_label']     = 'Accorder l\'interview';
$lang['micro_events']['press_interview_a_hint']      = '+8 réputation, -€500';
$lang['micro_events']['press_interview_b_label']     = 'Décliner poliment';
$lang['micro_events']['press_interview_b_hint']      = '-2 réputation';
$lang['micro_events']['press_interview_a_result']    = 'L\'article était élogieux ! Votre station a bénéficié d\'une excellente couverture médiatique et votre réputation a grimpé.';
$lang['micro_events']['press_interview_b_result']    = 'Le journaliste est parti déçu et a noté brièvement que votre station était « indisponible pour un commentaire ».';

// -----------------------------------------------------------------------
// equipment_deal
// Un fournisseur propose une réduction de dernière minute sur l'équipement.
// Choix A : Accepter l'offre (-€400, +2 réputation)
// Choix B : Passer cette fois (0, 0)
// -----------------------------------------------------------------------
$lang['micro_events']['equipment_deal_title']       = 'Offre d\'équipement de dernière minute';
$lang['micro_events']['equipment_deal_description'] = 'Un fournisseur de matériel de ski propose une réduction importante sur l\'équipement de location, valable aujourd\'hui seulement. Les clients remarqueront la qualité améliorée.';
$lang['micro_events']['equipment_deal_a_label']     = 'Accepter l\'offre';
$lang['micro_events']['equipment_deal_a_hint']      = '-€400, +2 réputation';
$lang['micro_events']['equipment_deal_b_label']     = 'Passer cette fois';
$lang['micro_events']['equipment_deal_b_hint']      = 'Aucun effet';
$lang['micro_events']['equipment_deal_a_result']    = 'Les clients ont adoré le matériel tout neuf ! Votre boutique de location a reçu quelques compliments supplémentaires aujourd\'hui.';
$lang['micro_events']['equipment_deal_b_result']    = 'Vous avez décliné l\'offre. Les choses continuent normalement.';

// -----------------------------------------------------------------------
// lost_skier
// Un skieur n'est pas rentré à la station.
// Choix A : Déployer la patrouille immédiatement (-€300, +8 réputation)
// Choix B : Attendre et surveiller (0, -12 réputation)
// -----------------------------------------------------------------------
$lang['micro_events']['lost_skier_title']       = 'Signalement de skieur disparu';
$lang['micro_events']['lost_skier_description'] = 'Un skieur n\'est pas rentré à la station. Son groupe s\'inquiète. Que faites-vous ?';
$lang['micro_events']['lost_skier_a_label']     = 'Déployer la patrouille immédiatement';
$lang['micro_events']['lost_skier_a_hint']      = '-€300, +8 réputation';
$lang['micro_events']['lost_skier_b_label']     = 'Attendre et surveiller';
$lang['micro_events']['lost_skier_b_hint']      = '-12 réputation';
$lang['micro_events']['lost_skier_a_result']    = 'Votre patrouille a retrouvé le skieur rapidement et en sécurité. Les clients font l\'éloge de la réactivité exemplaire de votre station.';
$lang['micro_events']['lost_skier_b_result']    = 'Après un long délai, le skieur a été retrouvé sain et sauf, mais l\'attente a alarmé de nombreux clients. Votre réputation en a pris un coup.';
