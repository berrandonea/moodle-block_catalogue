<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Initially developped for :
 * Université de Cergy-Pontoise
 * 33, boulevard du Port
 * 95011 Cergy-Pontoise cedex
 * FRANCE
 *
 * Displays a catalogue of all the blocks, modules, reports and customlabels the teacher can use in his course.
 *
 * @package    block_catalogue
 * @copyright  Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
 * File : lang/fr/block_catalogue.php
 * French strings.
 */

defined('MOODLE_INTERNAL') || die();
$string['pluginname'] = 'Catalogue';
$string['catalogue'] = 'Catalogue';
$string['config_blocktitle_default'] = 'Catalogue';
$string['catalogue:addinstance'] = 'Ajouter un bloc Catalogue';
$string['catalogue:myaddinstance'] = 'Ajouter un bloc Catalogue à mon tableau de bord';
$string['catalogue:edit'] = 'Changer des descriptions ou des liens dans le bloc Catalogue';
$string['catalogue:togglefav'] = 'Ajouter ou retirer des favoris dans le bloc Catalogue';
$string['catalogue:togglehide'] = 'Cacher ou montrer des éléments dans le bloc Catalogue';
$string['addnew'] = "Ajout d'un nouveau";
$string['chooseplace'] = 'Choisissez un emplacement';
$string['sortorder'] = 'Order de tri';
$string['hover'] = 'Survolez une description avec votre souris pour la lire en entier';
$string['doc'] = 'En savoir plus...';
$string['favorites'] = 'Favoris';
$string['favorites_help'] = 'Vous pouvez ajouter ou retirer des favoris à partir des catégories ci-dessus.';
$string['nofavs'] = 'Vous n\'avez pas encore déclaré de favoris.';
$string['addfav'] = 'Ajouter aux favoris';
$string['delfav'] = 'Retirer des favoris';
$string['hide'] = 'Cacher';
$string['show'] = 'Montrer';
$string['on_fav'] = 'Ajouter aux favoris';
$string['off_fav'] = 'Retirer des favoris';
$string['on_hide'] = 'Cacher';
$string['off_hide'] = 'Montrer';
$string['headerconfig'] = 'Réglages des blocs Catalogue';
$string['descconfig'] = 'Choisissez quelles listes seront affichées dans les blocs Catalogue et dans quel ordre.';
$string['displayedlists'] = 'Listes affichées';
$string['descdisplayedlists'] = 'Valeurs possibles : activities (Activités), blocks (Blocs), customlabels (Eléments de cours, requiert mod_customlabel), enrols (Inscrire des étudiants), grades (Notes), reports (Rapports), resources (Ressources). A écrire sans espaces, séparés par des virgules.';
$string['getremotedata'] = 'Chercher des données distantes';
$string['descgetremotedata'] = 'Si coché, cherche des données concernant les éléments du catalogue dans la documentation en ligne de Moodle et le site des plugins. Le chargement de la page prend donc plus de temps. Mais une fois trouvée, une donnée est stockée localement.';
$string['edit1'] = "Fermer l'interface gestionnaire";
$string['edit0'] = "Ouvrir l'interface gestionnaire";

$string['activities_listname'] = 'Activités';
$string['activities_exercise'] = 'Exercices';
$string['activities_collaborative'] = 'Outils collaboratifs';
$string['activities_other'] = 'Autres';
$string['activities_use'] = 'Ajouter au cours';

$string['blocks_listname'] = 'Blocs';
$string['blocks_monitor'] = 'Suivre mes étudiants';
$string['blocks_communicate'] = 'Communiquer avec mes étudiants';
$string['blocks_other'] = 'Enrichir mon espace pédagogique';
$string['blocks_use'] = 'Créer un nouveau';
$string['blocks_description_activity_modules'] = "Donne la liste et permet l'accès aux différentes activités disponibles dans votre cours (forum, tests, devoirs, etc.).";
$string['blocks_description_calendar_upcoming'] = 'Affiche les évènements futurs dans une liste, leur date d’accès et leur date limite. Les évènements sont générés directement depuis le calendrier et/ou les activités datées.';
$string['blocks_description_comments'] = "Les utilisateurs peuvent ajouter des commentaires, non seulement sur la page d'accueil du cours mais aussi, selon le choix de l'enseignant qui l'ajoute, sur des ressources ou des activités.";
$string['blocks_description_community'] = "Avec ce bloc, il est possible d'accéder à des serveurs d'échange (comme MOOCH par exemple) pour trouver des cours, et les télécharger ou s'y inscrire.";
$string['blocks_description_blog_menu'] = "Permet à un utilisateur de voir tous ses articles, d'afficher un nouvel article, ou de souscrire au flux RSS s'il est activé.";
$string['blocks_description_blog_tags'] = "Affiche une liste de blog. Plus le titre d'un blog est gros, plus il est visité.";
$string['blocks_description_course_summary'] = "Affiche le résumé ou la description du cours, tel que vous l'avez rédigé dans les Paramètres.";
$string['blocks_description_feedback'] = "Fournit un raccourci vers des activités de feedback global s'il y en a sur la page d'accueil.";
$string['blocks_description_glossary_random'] = "Affiche au hasard des entrées d'un de vos glossaires, des images ou des astuces, etc.";
$string['blocks_description_login'] = "Permet aux utilisateurs qui ne sont pas encore identifiés dans le site de rentrer leur « Nom d'utilisateur » et « Mot de passe » pour se connecter, de créer un nouveau compte, ou de retrouver / restaurer leur mot de passe.";
$string['blocks_description_participants'] = "Affiche les enseignants et les étudiants du cours.";
$string['blocks_description_myprofile'] = "Affiche quelques éléments du profil de l'utilisateur.";
$string['blocks_description_course_main_menu'] = "Pour naviguer entre les sections du cours.";
$string['blocks_description_course_contents'] = "Affiche la liste des sections (ou semaines) visibles dans ce cours.";
$string['blocks_description_course_list'] = "Affiche l'ensemble des cours auxquels l'utilisateur est inscrit et facilite le passage d'un cours à un autre.";
$string['blocks_description_course_overview'] = "Affiche les cours auxquels l’utilisateur est inscrit ainsi que les suivis à faire dans ces cours. Activez le mode édition et un menu déroulant permet à l'utilisateur de sélectionner le nombre de cours à afficher.";
$string['blocks_description_html'] = "Bloc standard utilisé pour ajouter du texte ou des images sur une page de site ou de cours. Le titre peut être personnalisé ou laissé vide.";
$string['blocks_description_private_files'] = "Donne accès au dépôt 'Fichiers personnels' de l'utilisateur.";
$string['blocks_description_blog_recent'] = "Affiche les N dernières entrées de blog, filtrées par contexte.";
$string['blocks_description_quiz_results'] = "Affiche aux étudiants leurs notes pour un test choisi. Selon les paramètres, les notes affichées seront par groupe ou par étudiant et en pourcentage, en nombre absolu ou en fraction.";
$string['blocks_description_recent_activity'] = "Liste les activités ajoutées au cours depuis votre dernière visite, comme les messages postés dans les forums ou les fichiers ajoutés par l'enseignant.";
$string['blocks_description_section_links'] = "Affiche les numéros des sections présentes dans le cours. Cliquez sur le numéro de la section pour accéder directement à celle-ci.";
$string['blocks_description_site_main_menu'] = "Ce bloc est utilisé par l’administrateur, sur la page d'accueil du site, pour donner accès à des ressources et des activités destinées aux utilisateurs ayant un rôle particulier sur la page d'accueil.";
$string['blocks_description_social_activities'] = "Permet d'ajouter des ressources et des activités supplémentaires dans un cours au format social.";
$string['blocks_description_activity_results'] = "Affiche les résultats des activités notées ou classées de ce cours.";
$string['blocks_description_calendar_month'] = "Affiche les événements prévus ce mois-ci.";
$string['blocks_description_online_users'] = "Affiche une liste d'utilisateurs connectés sur ce cours, c'est à dire ayant effectué au moins une action sur ce cours dans les 5 dernières minutes (par défaut), la liste étant mise à jour régulièrement.";
$string['blocks_description_mentees'] = "Fournit au(x) tuteur(s) un accès rapide au(x) profil(s) de leur(s) tutoré(s).";
$string['blocks_description_completionstatus'] = "Montre l'avancement d'un étudiant dans le cours par rapport à des critères spécifiques.";
$string['blocks_description_messages'] = "Permet d’envoyer un message personnel à n’importe quel utilisateur du site.";
$string['blocks_description_rss_client'] = "Permet de diffuser des flux RSS de sites extérieurs dans votre cours.";
$string['blocks_description_search_forums'] = "Permet d’effectuer une recherche sur tous les forums du cours.";
$string['blocks_description_selfcompletion'] = "Permet aux étudiants de déclarer qu'ils ont terminé le cours. Cela peut faire partie des conditions d'achèvement du cours.";
$string['blocks_description_badges'] = "Affiche les badges obtenus par cet utilisateur.";
$string['blocks_description_news_items'] = "Affiche les derniers messages du forum des nouvelles, envoyés par l'enseignant aux étudiants.";
$string['blocks_description_navigation'] = "Le bloc Navigation apparaît sur toutes les pages du site. Il facilite les déplacements dans l'arborescence, dont le contenu varie selon le rôle de l'utilisateur et l’endroit où il se trouve sur le site.";
$string['blocks_description_tag_youtube'] = "Ne peut être ajouté que sur la page des tags. Si un utilisateur indique des mots-clés dans son profil ou son blog, ces mots-clés deviennent des tags.";
$string['blocks_description_tag_flickr'] = "Ne peut être ajouté que sur la page des tags. Permet d'afficher des images en fonction de critères.";
$string['blocks_description_tags'] = "Chaque tag du site est affiché d'autant plus gros qu'il y a d'objets associés à lui sur le site.";
$string['blocks_description_admin_bookmarks'] = "Permet à l'administrateur de se créer des raccourcis vers des pages d'administration du site.";
$string['blocks_link_activity_modules'] = 'fr/Bloc_Activités';
$string['blocks_link_admin_bookmarks'] = 'fr/Bloc_Marque-pages_administrateur';
$string['blocks_link_blog_menu'] = 'fr/Bloc_Menu_blog';
$string['blocks_link_calendar_month'] = 'fr/Bloc_Calendrier';
$string['blocks_link_comments'] = 'fr/Bloc_Commentaires';
$string['blocks_link_community'] = 'fr/Bloc_Recherche_communaut%C3%A9';
$string['blocks_link_html'] = 'fr/Bloc_HTML';
$string['blocks_link_navigation'] = 'fr/Bloc_Navigation';
$string['blocks_link_news_items'] = 'fr/Bloc_Derni%C3%A8res_nouvelles';
$string['blocks_link_online_users'] = 'fr/Bloc_Utilisateurs_en_ligne';
$string['blocks_link_participants'] = 'fr/Bloc_Personnes';
$string['blocks_link_private_files'] = 'fr/Bloc_Fichiers_personnels';
$string['blocks_link_recent_activity'] = 'fr/Bloc_Activit%C3%A9_r%C3%A9cente';
$string['blocks_link_social_activities'] = 'fr/Bloc_Activit%C3%A9s_sociales';

$string['customlabels_listname'] = 'Eléments de cours';
$string['customlabels_pedagogic'] = 'Eléments pédagogiques';
$string['customlabels_structure'] = 'Titres et sous-titres';
$string['customlabels_other'] = 'Autres éléments';
$string['customlabels_use'] = 'Créer un nouveau';
$string['customlabels_help_singular'] = 'A utiliser pour afficher un(e)';
$string['customlabels_help_plural'] = 'A utiliser pour afficher des';
$string['customlabels_inyourcourse'] = 'dans votre cours.';
$string['customlabels_doclink'] = 'https://docs.moodle.org/2x/fr/%C3%89l%C3%A9ments_de_cours';

$string['enrols_listname'] = "Inscriptions";
$string['enrols_methods'] = "Méthodes pour inscrire";
$string['enrols_users'] = 'Utilisateurs et groupes';
$string['enrols_use'] = 'Consulter';
$string['enrols_manualenrol'] = 'Inscription manuelle';
$string['enrols_description_user_index'] = 'Liste consultable par les étudiants.';
$string['enrols_description_group_index'] = 'Gérer les groupes et les groupements.';
$string['enrols_description_enrol_users'] = 'Pour inscrire ou désinscrire manuellement un étudiant ou un enseignant.';
$string['enrols_description_report_roster'] = 'Trombinoscope des étudiants.';
$string['enrols_description_enrol_vet'] = "Inscrire tous les étudiants d'une certaine VET (promotion).";
$string['enrols_description_enrol_self_edit'] = "Définissez un mot de passe qui permettra aux étudiants de s'inscrire eux-mêmes au cours.";
$string['enrols_description_mass_enroll'] = "Permet de faire une inscription de masse à l'aide d'un fichier CSV (tableur).";
$string['enrols_description_group_copygroup'] = "Permet d'importer des groupes d'étudiants déclarés dans CELCAT ou dans un autre cours de la plateforme.";
$string['enrols_description_block_demands'] = "Si des étudiants ont demandé une inscription dans ce cours, répondez-leur ici.";

$string['grades_listname'] = 'Notes';
$string['grades_gradereport'] = 'Rapports sur les notes';
$string['grades_gradesetting'] = 'Réglages des notes';
$string['grades_outcome'] = 'Objectifs, compétences, badges';
$string['grades_use'] = 'Consulter';
$string['grades_description_gradesetting_scale'] = "Un barème est la liste des notes ou appréciations qu'un apprenant peut recevoir pour une activité.";
$string['grades_description_gradesetting_tree'] = "Permet de modifier les coefficients de chaque activité notée, l'affichage des notes, la pondération et le mode de calcul des notes.";
$string['grades_description_gradesetting_letter'] = "Système de notation anglo-saxon (A, B, C, D, E, F)";
$string['grades_description_gradesetting_settings'] = "Permet de paramétrer le carnet de note des apprenants.";
$string['grades_description_gradereport_singleview'] = "Affiche les notes pour un étudiant dans une activité.";
$string['grades_description_gradereport_history'] = "Récapitule les modifications apportées aux notes.";
$string['grades_description_gradereport_overview'] = "Vue globale des notes de l'apprenant sur l'ensemble des cours sur la plateforme.";
$string['grades_description_gradereport_grader'] = "Les notes des apprenants sur le cours.";
$string['grades_description_gradereport_user'] = "Permet de voir les notes d'un participant sur l'ensemble des activités notées du cours.";
$string['grades_description_badges_index'] = "Créer et gérer des badges pour ce cours, avec leurs critères d'attribution aux étudiants.";
$string['grades_description_gradesetting_outcome'] = "Montre les objectifs définis sur la plateforme et disponibles pour votre cours.";
$string['grades_description_gradesetting_outcomecourse'] = "Montre les objectifs utilisés dans votre cours.";
$string['grades_description_gradereport_outcomes'] = "Montre dans quelle proportion chaque objectif a été atteint par les étudiants.";

$string['reports_listname'] = 'Rapports';
$string['reports_report'] = 'Rapports';
$string['reports_use'] = 'Consulter';
$string['reports_description_report_progress'] = "Permet de voir si l'apprenant a terminé/consulté ou non une activité/ressource.";
$string['reports_description_report_completion'] = "Affiche le progrès d'un étudiant dans le cours par rapport à des critères spécifiques.";
$string['reports_description_report_outline'] = "Affiche tous les activités et ressources du cours, classées par section";
$string['reports_description_report_log'] = "Liste les actions réalisées dans le cours par les participants.";
$string['reports_description_report_loglive'] = "Pour suivre en direct la liste les actions réalisées dans le cours.";
$string['reports_description_report_engagement'] = "Fournit des informations sur les progrès des élèves par rapport à une gamme d'indicateurs.";
$string['reports_description_report_participation'] = "Affiche la participation au cours des apprenants selon le module d'activité, leur statut et groupe.";
$string['reports_description_report_stats'] = "Quelques statistiques sur le cours.";
$string['reports_description_report_exportlist'] = "Exporter en CSV la liste des étudiants par groupe.";
$string['reports_description_report_courseoverview'] = "Affiche les cours auxquels l’utilisateur est inscrit ainsi que les suivis à faire dans ces cours.";
$string['reports_description_report_roster'] = 'Trombinoscope des étudiants.';

$string['resources_listname'] = 'Ressources';
$string['resources_resource'] = 'Ressources';
$string['resources_use'] = 'Ajouter au cours';
