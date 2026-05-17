<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['help']['title']      = 'Aide et Foire Aux Questions (FAQ)';
$lang['help']['eyebrow']    = 'Une réponse rapide?';
$lang['help']['stat_label'] = 'réponses rapides';
$lang['help']['intro']      = 'Une question à propos du jeu? Tu te demandes comment quelque chose fonctionne? Tu trouveras probablement la réponse ci-dessous. Sinon, tu peux toujours <a href="'.base_url().'contact_controller">nous contacter</a>.';

/* ─── Compte & Accès ───────────────────────────────── */
$lang['help']['question']['1'] = 'Ski-Manager est-il vraiment gratuit?';
$lang['help']['answer']['1']   = 'Oui, complètement ! Ski-Manager est 100 % gratuit, sans achat obligatoire. La monnaie premium optionnelle (Génépis) peut accélérer certaines actions, mais chaque fonctionnalité — pistes, remontées, personnel, tournois, finances — est accessible à tous les joueurs sans frais, pour toujours.';

$lang['help']['question']['2'] = 'Dois-je télécharger ou installer quelque chose?';
$lang['help']['answer']['2']   = 'Aucun téléchargement ni installation n\'est requis. Ski-Manager fonctionne entièrement dans ton navigateur web sur n\'importe quel appareil — ordinateur, portable, tablette ou mobile. Il suffit de visiter ski-manager.net, de créer un compte gratuit et de jouer immédiatement.';

$lang['help']['question']['3'] = 'Comment créer un compte?';
$lang['help']['answer']['3']   = 'Clique sur le bouton bleu « S\'inscrire » sur la page d\'accueil ou visite <a href="'.base_url().'register_controller">ski-manager.net/register</a>. Tu n\'as besoin que d\'une adresse e-mail valide et d\'un nom d\'utilisateur. Une fois inscrit, tu peux te connecter immédiatement et commencer à construire ta station.';

$lang['help']['question']['4'] = 'J\'ai oublié mon mot de passe. Comment le réinitialiser?';
$lang['help']['answer']['4']   = 'Sur la page de connexion, clique sur « Mot de passe oublié? » et saisis l\'adresse e-mail liée à ton compte. Tu recevras un lien de réinitialisation en quelques minutes. S\'il n\'arrive pas, vérifie tes spams. Si le problème persiste, <a href="'.base_url().'contact_controller">contacte-nous</a>.';

$lang['help']['question']['5'] = 'Puis-je jouer en plusieurs langues?';
$lang['help']['answer']['5']   = 'Oui ! Ski-Manager est entièrement disponible en anglais et en français. Tu peux changer de langue à tout moment via le sélecteur de langue dans la barre de navigation. Ta préférence est sauvegardée automatiquement.';

/* ─── Bases du jeu ─────────────────────────────────── */
$lang['help']['question']['6'] = 'Comment commencer à construire ma station?';
$lang['help']['answer']['6']   = 'Après l\'inscription, tu accèdes à la carte interactive de ta station. Commence par placer des pistes depuis un point élevé vers le bas de la montagne. Installe ensuite une remontée mécanique pour ramener les visiteurs en haut. Une fois qu\'une piste et une remontée sont ouvertes, tu peux accueillir des visiteurs via le bâtiment Office de tourisme.';

$lang['help']['question']['7'] = 'Quels sont les niveaux de difficulté des pistes?';
$lang['help']['answer']['7']   = 'Ski-Manager propose quatre niveaux de difficulté : Verte (débutant), Bleue (intermédiaire), Rouge (avancé) et Noire (expert). Chaque type attire des profils de visiteurs différents et génère des revenus différents. Une station équilibrée avec les quatre types de pistes attire le plus grand nombre de touristes et maximise les revenus quotidiens.';

$lang['help']['question']['8'] = 'Comment fonctionnent les remontées mécaniques?';
$lang['help']['answer']['8']   = 'Les remontées mécaniques transportent les visiteurs du bas vers le haut des pistes. Tu peux construire plusieurs types : tire-fesses, T-bars, télécabines et télésiège débrayables. Des remontées à plus grande capacité réduisent les temps d\'attente et augmentent la satisfaction des visiteurs. Chaque remontée doit être configurée pour desservir les pistes correspondantes.';

$lang['help']['question']['9'] = 'Comment gérer les finances de ma station?';
$lang['help']['answer']['9']   = 'La section Finances affiche tes revenus quotidiens (forfaits, cours, hôtellerie) par rapport à tes coûts (salaires, maintenance des remontées, entretien des bâtiments). Ta station génère des revenus lorsqu\'elle est ouverte et que les visiteurs sont présents. Tu peux contracter un prêt bancaire pour financer les constructions en début de partie, puis le rembourser à mesure que les revenus augmentent.';

$lang['help']['question']['10'] = 'Qu\'est-ce que la Réputation et pourquoi est-elle importante?';
$lang['help']['answer']['10']   = 'La Réputation est le score d\'image publique de ta station. Elle augmente si tu maintiens la qualité des pistes, une excellente satisfaction des visiteurs, des tournois réussis et un marketing actif. Une réputation plus élevée attire plus de visiteurs par jour, débloque des prix de forfaits plus élevés et améliore ton classement mondial. Négliger le damage peut rapidement réduire ta réputation.';

/* ─── Bâtiments & Fonctionnalités ──────────────────── */
$lang['help']['question']['11'] = 'Quels bâtiments puis-je construire?';
$lang['help']['answer']['11']   = 'Des dizaines de bâtiments sont disponibles : hôtellerie (restaurants, chalets, hôtels), services (école de ski, location d\'équipement, centre médical, secours), divertissement (snowparks, bars après-ski, salles d\'événements), infrastructure (canons à neige, dépôts de damage, centrales énergétiques) et marketing (webcams de montagne, office de tourisme, réseaux sociaux). Chaque bâtiment ajoute des coûts quotidiens mais améliore aussi les visiteurs, la réputation ou l\'efficacité du personnel.';

$lang['help']['question']['12'] = 'Comment fonctionne l\'enneigement artificiel?';
$lang['help']['answer']['12']   = 'Tu peux installer des canons à neige sur chaque piste. Lorsqu\'ils sont activés, les canons produisent de la neige artificielle — utile pour prolonger ta saison lors de périodes chaudes ou peu enneigées. L\'enneigement consomme de l\'eau et de l\'énergie, donc tu dois gérer tes ressources et ton budget. Un meilleur équipement produit plus de neige par heure.';

$lang['help']['question']['13'] = 'Qu\'est-ce que le Snowpark?';
$lang['help']['answer']['13']   = 'Le Snowpark est une zone dédiée au freestyle ski et au snowboard. Il propose des kickers, des rails, des boxes et une halfpipe. La construction et l\'amélioration de ton snowpark attire les jeunes visiteurs et les amateurs de freestyle, augmentant la diversité des visiteurs et les revenus quotidiens. Tu peux aussi y organiser des compétitions freestyle.';

$lang['help']['question']['14'] = 'À quoi servent les webcams de montagne?';
$lang['help']['answer']['14']   = 'Les webcams de montagne diffusent des flux simulés en direct de tes pistes pour attirer des visiteurs et booster la réputation quotidienne. Tu peux configurer le nombre de caméras (1 à 10), la qualité (SD/HD/4K), la vision nocturne, l\'incrustation météo et le partage sur les réseaux sociaux. Chaque amélioration augmente le bonus d\'attraction de visiteurs mais aussi les coûts de fonctionnement.';

/* ─── Personnel & Opérations ───────────────────────── */
$lang['help']['question']['15'] = 'Comment recruter du personnel?';
$lang['help']['answer']['15']   = 'Ouvre la section Personnel depuis le menu principal pour recruter, former et gérer tes employés. Tu peux embaucher des moniteurs de ski, des dameurs, des pisteurs-secouristes, des directeurs marketing et des directeurs de station. Chaque employé a un niveau de compétence, un salaire et une spécialisation. Un personnel plus qualifié coûte plus cher mais offre de meilleurs résultats.';

$lang['help']['question']['16'] = 'Comment fonctionne le damage des pistes?';
$lang['help']['answer']['16']   = 'Les pistes se dégradent au fil du temps en raison du trafic des skieurs et des conditions météo. Déployer des dameurs la nuit restaure la qualité des pistes pour le lendemain. Tu peux affecter des dameuses spécifiques à des pistes spécifiques. Des pistes bien entretenues attirent plus de visiteurs et permettent de pratiquer des prix plus élevés.';

/* ─── Compétitions & Communauté ────────────────────── */
$lang['help']['question']['17'] = 'Comment fonctionnent les tournois?';
$lang['help']['answer']['17']   = 'Tu peux organiser des compétitions de ski sur tes pistes via la section Événements & Salles. Les tournois attirent des athlètes de haut niveau, des médias sportifs et des touristes supplémentaires, boostant significativement ton nombre de visiteurs et ta réputation pendant l\'événement. Les tournois réussis comptent également dans ton score au classement mondial.';

$lang['help']['question']['18'] = 'Comment ma station est-elle comparée aux autres joueurs?';
$lang['help']['answer']['18']   = 'Ta station est évaluée sur la réputation totale, le nombre de visiteurs, les revenus cumulés et les victoires en tournois. Tous les scores sont affichés sur le classement mondial. Le classement se met à jour quotidiennement. Ton objectif est d\'atteindre le sommet en gérant ta station efficacement et en organisant des événements réussis.';

/* ─── Technique & Autres ───────────────────────────── */
$lang['help']['question']['19'] = 'Qu\'est-ce que la monnaie premium Génépis?';
$lang['help']['answer']['19']   = 'Les Génépis sont la monnaie premium optionnelle de Ski-Manager. Ils peuvent être utilisés pour terminer instantanément une construction, passer les minuteries ou débloquer des améliorations cosmétiques. Ils ne sont jamais obligatoires — toutes les fonctionnalités sont accessibles sans dépenser de Génépis. Tu peux en gagner via les succès en jeu et les bonus quotidiens.';

$lang['help']['question']['20'] = 'Ma progression est-elle sauvegardée automatiquement?';
$lang['help']['answer']['20']   = 'Oui. Toutes les actions dans Ski-Manager sont sauvegardées sur le serveur en temps réel. Aucune sauvegarde manuelle n\'est nécessaire. Ta station continue à fonctionner hors ligne — les visiteurs arrivent toujours, les finances se mettent à jour quotidiennement et les bâtiments restent opérationnels. Reconnecte-toi simplement pour consulter ta progression.';

$lang['help']['question']['21'] = 'Quels navigateurs sont pris en charge?';
$lang['help']['answer']['21']   = 'Ski-Manager est compatible avec tous les navigateurs modernes : Chrome, Firefox, Safari, Edge et Opera. Pour une meilleure expérience, utilise la dernière version de ton navigateur préféré sur un ordinateur ou un portable. Le jeu est également entièrement jouable sur les navigateurs mobiles iOS et Android.';
