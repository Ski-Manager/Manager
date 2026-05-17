<?php
//french file
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['signup']['text']		= 'Créer un nouveau compte';
$lang['signup']['account_info']		= 'Détails de compte';
$lang['signup']['new_password']		= 'Nouveau le mot de passe';
$lang['signup']['password_confirm']		= 'Confirmer le mot de passe';
$lang['signup']['new_password_confirm']		= 'Confirmer le nouveau mot de passe';
$lang['signup']['age']		= 'Âge';
$lang['signup']['referral']		= 'Parrain (Facultatif)';
$lang['signup']['referral_confirmed']		= 'Ton parrain a été prise en compte. Vous recevrez tous les deux un supplément de '.GENEPIS.' Génépis une fois que tu auras complété quelques objectifs.';
$lang['signup']['info_referral']		= 'Entre le nom de ton parrain si tu en a un afin que vous puissiez tous les deux obtenir '.GENEPIS.' Génépis supplémentaires (Monnaie virtuelle sur Ski-manager).';
$lang['signup']['signup_create']		= 'Créer mon compte';
$lang['signup']['edit_account_submit']		= 'Mettre à jour mon compte';
$lang['signup']['account_created']		= 'Inscription réussie. Un email a été envoyé à ton adresse email. Ouvre-le pour activer ton compte et commencer à jouer. <br> Si tu n\'as rien reçu d\'ici 15 minutes, vérifie ton dossier de courrier indésirable ou contacte-nous à '.CONST_ADMIN_EMAIL .'.';
$lang['signup']['account_updated']		= '<div class="alert alert-success text-center">Compte mis à jour.</div>';
$lang['signup']['no_data_changed']		= '<div class="alert alert-warning text-center">Aucune information n\'a été modifiée.</div>';
$lang['signup']['account_not_activated']		= 'Ton compte n\'a pas encore été activé. Vérifie ta boîte de réception pour activer ton compte. Si tu n\'as rien reçu d\'ici 15 minutes, vérifie ton dossier de courrier indésirable ou contacte-nous à '.CONST_ADMIN_EMAIL .'.';
$lang['signup']['intro_update_account']		= 'Ici tu peux trouver les détails de ton compte. Pour changer ton nom d\'utilisateur, contacte-nous à '.CONST_ADMIN_EMAIL .'.';
$lang['signup']['click_here']             = 'Clique ici';
$lang['signup']['to_send_email']          = ' pour envoyer un nouvel email d\'activation à';
$lang['alpha_dash_space']    = "Le champ Nom d\'utilisateur peut uniquement contenir des caractères alphanumériques, underscores et des tirets/espaces.";
$lang['signup']['invalid_username']    = "Le champ Nom d\'utilisateur contient des caractères non valides.<br>";
$lang['missing_username']    = "Tu dois renseignez un nom d\'utilisateur.";
$lang['signup']['info_password']    = "Le mot de passe doit comporter au moins 4 caractères.<br>";
$lang['signup']['agree_newsletter']    = "En t'inscrivant sur le site, tu sera automatiquement ajouté à la liste des destinataires de notre newsletter. Tu peux te désinscrire à tout moment depuis la page de ton compte.";


$lang['signup']['reset_account']    = "Réinitialiser le compte";
$lang['signup']['confirm_reset_account_title']    = "Confirmation de la réinitialisation du compte.";
$lang['signup']['confirm_reset_account']    = "Confirmer la réinitialisation de mon compte";
$lang['signup']['reset_account_text']    = "La réinitialisation de ton compte supprime ta station et tous le progrès, en revance, ton compte sera toujours actif.";
$lang['signup']['confirm_reset_account_text']    = "Es-tu sûr de vouloir réinitialiser ton compte? Cette action ne peut pas être annulée. Ta station sera détruite et tout progrès sera perdu. Ton compte restera actif.";
$lang['signup']['email_sent_reset_account']    = "Un e-mail contenant des instructions supplémentaires sur la réinitialisation de ton compte a été envoyé à ton adresse e-mail.";
$lang['signup']['email_sent_reset_account_confirmed']    = "Ton compte a été réinitialisé.";
$lang['signup']['password_confirm_reset']    = "Entrez ton mot de passe pour confirmer la réinitialisation de ton compte";

$lang['signup']['delete_account']    = "Suppression de compte";
$lang['signup']['confirm_delete_account_title']    = "Confirmation de suppression de compte";
$lang['signup']['confirm_delete_account']    = "Confirmer la suppression de mon compte";
$lang['signup']['delete_account_text']    = "La suppression de ton compte supprimera ta station et toute information liée à ton utilisateur.";
$lang['signup']['confirm_delete_account_text']    = "Es-tu sûr de vouloir supprimer ton compte? Cette action ne peut pas être annulée. Ta station sera détruite, tout progrès sera perdu et toute information relative à ton utilisateur sera supprimé.";
$lang['signup']['email_sent_delete_account']    = "Un e-mail contenant des instructions supplémentaires sur la suppression de ton compte a été envoyé à ton adresse e-mail.";
$lang['signup']['email_sent_delete_account_confirmed']    = "Ton compte a été supprimé.";
$lang['signup']['password_confirm_delete']    = "Entrez ton mot de passe pour confirmer la suppression de ton compte";


// Facebook account
$lang['signup']['finalize_account']    = "C\'est presque fini!";
$lang['signup']['finalize_account_info']    = "Tu es sur le point de créer un nouveau compte sur Ski-Manager en utilisant ton compte Facebook";
$lang['signup']['finalize_account_more_info']    = "Nous avons juste besoin d'un peu plus d\'informations pour finaliser ton compte.";
$lang['signup']['finalize_account_call_you']    = "Comment devrions-nous t\'appeler";
$lang['signup']['signup_finalize_account']    = "Finaliser mon compte";
$lang['signup']['signup_merge_account']    = "Fusionner mes comptes";
$lang['signup']['account_finalized']    = "Félicitations, ton compte a été finalisé, tu peux commencer à jouer tout de suite! Clique <a href='".base_url()."genepis_controller'>ici</a> pour commencer à construire ta station.";

// Merging accounts
$lang['signup']['merge_account']    = "Email déjà enregistré";
$lang['signup']['merge_account_existing']    = "Il y a déjà un compte enregistré sur Ski-Manager avec l\'e-mail que tu as entré dans le formulaire de connexion Facebook.";
$lang['signup']['fb_account_existing']    = "Il y a déjà un compte enregistré sur Ski-Manager avec le compte Facebook actuellement utilisé";
$lang['signup']['fb_account_existing2']    = "Utilise le lien en bas à gauche pour te connecter avec Facebook ou crée un mot de passe standard pour te connecter avec ton nom d'utilisateur, en utilisant le lien ci-dessous.";
$lang['signup']['merge_account_question']    = "Veux-tu fusionner les deux comptes? Si tu veux te connecter en utilisant Facebook, tu dois fusionner les deux comptes, tu pourra alors te connecter en utilisant les deux méthodes.";
$lang['signup']['merge_account_password']    = "Pour confirmer la fusion, entre le mot de passe associé à l'adresse email fournie";
$lang['signup']['account_merged']    = "Tes comptes ont été fusionnés. Tu peux maintenant te connecter en utilisant Facebook ou ton nom d'utilisateur/mot de passe habituel.";

// account page
$lang['signup']['linked_facebook_account']    = "Ton compte est lié à Facebook";
$lang['signup']['not_linked_facebook']    = "Ton compte n'est pas lié à Facebook";
$lang['signup']['linked_google_account']    = "Ton compte est lié à Google";
$lang['signup']['not_linked_google']    = "Ton compte n'est pas lié à Google";
$lang['signup']['google_account_linked']    = "Ton compte Google a été lié avec succès.";
$lang['signup']['currently_no_newsletter']    = "Tu n\'es actuellement pas inscrit à la newsletter.";
$lang['signup']['currently_newsletter']    = "Tu es actuellement inscrit à la newsletter.";
$lang['signup']['newsletter_max_freq']    = "La newsletter est envoyée au maximum deux fois par mois et informera les utilisateurs des mises à jour importantes et des nouvelles fonctionnalités.";
$lang['signup']['newsletter_select_lang']    = "Choisis ta langue";
$lang['signup']['newsletter_email_conditions']    = "Ton adresse e-mail est uniquement utilisée pour t\'envoyer notre newsletter et des informations sur les activités de Ski-Manager. Tu peux toujours te désinscrire en utilisant le lien inclus en bas de la newsletter.";
$lang['signup']['unsubscribe']    = "Se désinscrire";
$lang['signup']['subscribe']    = "S\'inscrire";
$lang['signup']['newsletter']    = "Newsletter";

// Resend verification email
$lang['signup']['resend_verification_email']         = "Renvoyer l'email de vérification";
$lang['signup']['resend_verification_email_text']    = "Ton compte n'a pas encore été activé. Clique sur le bouton pour renvoyer l'email d'activation.";
$lang['signup']['email_sent_verification']           = "Un nouvel email d'activation a été envoyé à ton adresse email.";
$lang['signup']['account_already_activated']         = "Ton compte est déjà activé.";
