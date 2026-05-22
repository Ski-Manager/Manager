<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// SYSTEM MESSAGES
$lang['email_must_be_array'] = "La m&eacute;thode de validation d'email n'accepte que les tableaux associatifs (array).";
$lang['email_invalid_address'] = "Adresse email invalide: %s";
$lang['email_attachment_missing'] = "Impossible de localiser le fichier joint suivant: %s";
$lang['email_attachment_unreadable'] = "Impossible d'ouvrir ce fichier joint: %s";
$lang['email_no_from'] = 'Impossible d\'envoyer des emails sans champ \"De\".';
$lang['email_no_recipients'] = "Vous devez sp&eacute;cifier des destinataires: To, Cc, or Bcc";
$lang['email_send_failure_phpmail'] = "Impossible d'envoyer des emails avec la fonction mail() de PHP. Votre serveur ne doit pas &ecirc;tre configur&eacute; pour pouvoir utiliser cette m&eacute;thode.";
$lang['email_send_failure_sendmail'] = "Impossible d'envoyer des emails avec la m&eacute;thode Sendmail de PHP. Votre serveur ne doit pas &ecirc;tre configur&eacute; pour pouvoir utiliser cette m&eacute;thode.";
$lang['email_send_failure_smtp'] = "Impossible d'envoyer des emails avec la m&eacute;thode SMTP de PHP. Votre serveur ne doit pas &ecirc;tre configur&eacute; pour pouvoir utiliser cette m&eacute;thode.";
$lang['email_sent'] = "Votre message a bien &eacute;t&eacute; exp&eacute;di&eacute; par le protocole suivant: %s";
$lang['email_no_socket'] = "Impossible d'ouvrir un socket avec Sendmail. Veuillez v&eacute;rifier la configuration de votre environnement.";
$lang['email_no_hostname'] = "Vous n'avez pas sp&eacute;cific&eacute; d'h&ocirc;te SMTP.";
$lang['email_smtp_error'] = "L'erreur SMTP suivante s'est produite: %s";
$lang['email_no_smtp_unpw'] = "Erreur: Vous devez sp&eacute;cifier un nom d'utilisateur et un mot de passe SMTP.";
$lang['email_failed_smtp_login'] = "Echec lors de l'envoi de la commande AUTH LOGIN. Erreur: %s";
$lang['email_smtp_auth_un'] = "Impossible d'identifier le nom d'utilisateur. Erreur: %s";
$lang['email_smtp_auth_pw'] = "Impossible d'identifier le mot de passe. Erreur: %s";
$lang['email_smtp_data_failure'] = "Impossible d'envoyer les donn&eacute;es: %s";
$lang['email_exit_status'] = "Code de l'&euml; d'exit : %s";

// CUSTOM MESSAGES
// EMAIL VALIDATION AFTER SIGNUP
$lang['email']['activation_subject']         = 'Activez votre compte sur Ski-Manager';
$lang['email']['activation_heading']         = 'Activez votre compte';
$lang['email']['activation_body']            = 'Vous vous êtes enregistré avec succès sur Ski-Manager. Cliquez sur le bouton ci-dessous pour vérifier votre adresse e-mail et commencer à gérer votre station !';
$lang['email']['activation_cta']             = 'Activer mon compte';
$lang['email']['activation_note']            = 'Si vous n\'avez pas créé ce compte, vous pouvez ignorer cet e-mail. Pour toute question, contactez-nous à <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';

$lang['email']['beta_activation_subject']    = 'Activez votre compte sur Ski-Manager Beta';
$lang['email']['beta_activation_heading']    = 'Bienvenue sur la Beta Ski-Manager !';
$lang['email']['beta_activation_body']       = 'Vous vous êtes enregistré avec succès sur la version Beta de Ski-Manager. Cliquez sur le bouton ci-dessous pour activer votre compte.<br><br>Note : tous les progrès réalisés pendant la période bêta seront effacés à la fin du test. Les bêta-testeurs seront notifiés lorsque le test bêta sera terminé.';
$lang['email']['beta_activation_cta']        = 'Activer le compte Beta';

// Legacy HTML fragment keys – kept so that any remaining references do not break.
$lang['email']['activation_start_tags'] = '<!DOCTYPE html><html><head><meta charset="utf-8"/><title>Ski-Manager</title></head><body style="font-family:Arial,sans-serif;">';
$lang['email']['activation_dear']       = '<p>Cher ';
$lang['email']['activation_br']         = ',</p>';
$lang['email']['activation_body1']      = '<p>Vous vous êtes enregistré avec succès sur Ski-Manager, veuillez <strong>';
$lang['email']['activation_body2']      = 'cliquez ici</a></strong> pour activer votre compte.</p>';
$lang['email']['activation_end_tags']   = '</body></html>';

// RESET ACCOUNT EMAIL
$lang['email']['reset_account_subject'] = 'Réinitialisation de compte Ski-Manager.net';
$lang['email']['reset_account_heading'] = 'Réinitialiser votre compte';
$lang['email']['reset_account_body']    = 'Vous avez demandé à réinitialiser votre compte sur Ski-Manager.net. Cliquez sur le bouton ci-dessous pour confirmer. Toutes vos données de jeu seront effacées et vous pourrez repartir de zéro.';
$lang['email']['reset_account_cta']     = 'Confirmer la réinitialisation';
$lang['email']['reset_account_note']    = 'Si vous n\'avez pas fait cette demande, ignorez cet e-mail ou contactez-nous à <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';
// Legacy
$lang['email']['reset_account_body1']   = '<p>Vous avez demandé à réinitialiser votre compte. Veuillez<strong>';
$lang['email']['reset_account_body2']   = 'cliquez ici</a></strong> pour réinitialiser votre compte.</p>';

$lang['email']['delete_account_subject'] = 'Suppression de compte Ski-Manager.net';
$lang['email']['delete_account_heading'] = 'Supprimer votre compte';
$lang['email']['delete_account_body']    = 'Vous avez demandé la suppression définitive de votre compte sur Ski-Manager.net. Cliquez sur le bouton ci-dessous pour confirmer. <strong>Cette action est irréversible.</strong>';
$lang['email']['delete_account_cta']     = 'Confirmer la suppression';
$lang['email']['delete_account_note']    = 'Si vous n\'avez pas fait cette demande, ignorez cet e-mail ou contactez-nous à <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';
// Legacy
$lang['email']['delete_account_body1']   = '<p>Vous avez demandé à supprimer votre compte. Veuillez<strong>';
$lang['email']['delete_account_body2']   = 'cliquez ici</a></strong> pour supprimer votre compte.</p>';

$lang['email']['invalid_code'] = 'Le code est invalide, Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'.</div>';
$lang['email']['invalid_request'] = 'Demande invalide. Veuillez utiliser le lien fourni dans votre email.';
$lang['email']['code_expired'] = 'Le code a expiré, demande un nouveau ou contacte-nous à '.CONST_ADMIN_EMAIL .' si le problème persiste.</div>';
$lang['email']['account_reset_success'] = 'Ton compte a été réinitialisé avec succès. Tu peux maintenant créer une nouvelle station.</div>';
$lang['email']['account_reset_failed'] = 'Ton compte n\'a pas pu être réinitialisé. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'.</div>';
$lang['email']['account_delete_success'] = 'Ton compte a été supprimé avec succès. Nous sommes tristes de te voir partir.</div>';
$lang['email']['account_delete_failed'] = 'Ton compte n\'a pas pu être supprimé. Réessaie ou contacte-nous à '.CONST_ADMIN_EMAIL .'.</div>';

// Vacation mode
$lang['email']['vacation_mode_subject'] = 'Compte Ski-Manager mis en mode vacances';
$lang['email']['vacation_mode_heading'] = 'Mode vacances activé';
$lang['email']['vacation_mode_body']    = 'Vous avez été inactif sur <a href="' . base_url() . '" style="color:#0ea5e9;">Ski-Manager.net</a> pendant 14 jours, votre compte a donc été mis en mode vacances. Cela signifie que tout progrès sera suspendu et votre station ne sera pas ouverte jusqu\'à ce que vous vous reconnectiez.<br><br>Pour désactiver le mode vacances, connectez-vous simplement à votre compte.';
// Legacy
$lang['email']['vacation_mode_body1'] = '<p>Tu as été inactif sur <a href="'.base_url().'">Ski-Manager.net</a> pendant 14 jours, ton compte a donc été mis en mode vacances. Cela signifie que tout progrès sera suspendu et ta station ne sera pas ouverte jusqu\'à ce que tu te reconnectes.<br>Pour désactiver le mode vacances, connecte-toi simplement à ton compte.</p>';
$lang['email']['vacation_mode_body2'] = '<br>Cordialement,<br>/L\'équipe Ski-Manager.';

// ADMIN TRACKING
$lang['email']['tracking_delete_account'] = 'Account deleted';
$lang['email']['tracking_reset_account'] = 'Account reset';
$lang['email']['tracking_reset_password'] = 'Password reset';
$lang['email']['player_reset_account'] = 'A player has reset his account';
$lang['email']['player_delete_account'] = 'A player has deleted his account';
$lang['email']['player_reset_password'] = 'A player has reset his password';
$lang['email']['tracking_account_created'] = 'New account created';
$lang['email']['tracking_account_created_text'] = 'A new account created with the following details:';
$lang['email']['tracking_creation_time'] = 'Creation time';
$lang['email']['tracking_account_type'] = 'Account type';
$lang['email']['tracking_account_type_regular'] = 'Regular';
$lang['email']['tracking_account_type_facebook'] = 'Facebook';