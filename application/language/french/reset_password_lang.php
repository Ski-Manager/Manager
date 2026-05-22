<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$lang['reset_password']['title']			= 'Mot de passe ou nom d\'utilisateur oublié?';
$lang['reset_password']['intro']			= 'Si tu as oublié ton mot de passe ou ton nom d\'utilisateur, tu peux le réinitialiser en entrant ton adresse e-mail ou ton nom d\'utilisateur dans le champ ci-dessous.';
$lang['reset_password']['step_email_hint']	= 'Après la soumission, tu recevras un e-mail contenant un lien de réinitialisation. Ce lien est valable 24 heures. Si tu ne reçois pas l\'e-mail, vérifie ton dossier spam.';

$lang['reset_password']['reset_subject'] = 'Réinitialiser le mot de passe de ton compte Ski-Manager';
$lang['reset_password']['reset_heading'] = 'Réinitialiser votre mot de passe';
$lang['reset_password']['reset_body']    = 'Vous avez demandé à réinitialiser votre mot de passe sur Ski-Manager.net. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.';
$lang['reset_password']['reset_cta']     = 'Réinitialiser mon mot de passe';
$lang['reset_password']['reset_note']    = 'Ce lien est valide 24 heures. Si vous n\'avez pas demandé la réinitialisation de votre mot de passe, vous pouvez ignorer cet e-mail. Pour tout problème, contactez-nous à <a href="mailto:' . CONST_ADMIN_EMAIL . '" style="color:#0ea5e9;">' . CONST_ADMIN_EMAIL . '</a>.';
// Legacy
$lang['reset_password']['reset_body1'] = 'Tu as demandé à réinitialiser ton mot de passe sur ski-manager.net. Merci de cliquer sur le lien ci-dessous pour réinitialiser ton mot de passe.<br><br>';
$lang['reset_password']['reset_body2'] = 'Clique pour réinitialiser le mot de passe</a><br><br>';
$lang['reset_password']['reset_body3'] = 'Ce lien est valide 24 heures.<br>Si tu n\'as pas demandé à réinitialiser ton mot de passe, tu peux ignorer ce message.<br><br>Merci de nous contacter à '.CONST_ADMIN_EMAIL.' en cas de problème.<br><br>Cordialement,<br>/L\'équipe Ski-Manager.';
$lang['reset_password']['email_sent_username'] = 'Un email contenant des instructions supplémentaires a été envoyé à l\'adresse e-mail associée au nom d\'utilisateur fourni. Vérifie ton dossier spam si tu n\'as rien reçu dans quelques minutes ou contacte-nous à '.CONST_ADMIN_EMAIL.' pour toute question.';
$lang['reset_password']['email_sent_email'] = 'Un email contenant des instructions supplémentaires a été envoyé à l\'adresse email fournie. Vérifie ton dossier spam si tu n\'as rien reçu dans quelques minutes ou contacte-nous à '.CONST_ADMIN_EMAIL.' pour toute question.';

$lang['valid_email_or_username']			= 'Le nom d\'utilisateur ou l\'email n\'est pas valide.';


// confirm page to choose new password
$lang['reset_password']['choose_password_title']			= 'Entre ton nouveau mot de passe ci-dessous';
$lang['reset_password']['password_updated']			= 'Ton mot de passe a été mis à jour avec succès. Tu peux maintenant te connecter au site avec ton nouveau mot de passe.';
