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
 * @author     Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : list/blocks/lang/fr/blockcataloguelist_blocks.php
 * French strings for the list of blocks.
 */

defined('MOODLE_INTERNAL') || die();
$string['listname'] = 'Blocs';
$string['monitor'] = 'Suivre mes étudiants';
$string['communicate'] = 'Communiquer avec mes étudiants';
$string['other'] = 'Enrichir mon espace pédagogique';
$string['use'] = 'Créer un nouveau';

$string['description_activity_modules'] = "Donne la liste et permet l'accès aux différentes activités disponibles dans votre cours (forum, tests, devoirs, etc.).";
$string['description_calendar_upcoming'] = 'Affiche les évènements futurs dans une liste, leur date d’accès et leur date limite. Les évènements sont générés directement depuis le calendrier et/ou les activités datées.';
$string['description_comments'] = "Les utilisateurs peuvent ajouter des commentaires, non seulement sur la page d'accueil du cours mais aussi, selon le choix de l'enseignant qui l'ajoute, sur des ressources ou des activités.";
$string['description_community'] = "Avec ce bloc, il est possible d'accéder à des serveurs d'échange (comme MOOCH par exemple) pour trouver des cours, et les télécharger ou s'y inscrire.";
$string['description_blog_menu'] = "Permet à un utilisateur de voir tous ses articles, d'afficher un nouvel article, ou de souscrire au flux RSS s'il est activé.";
$string['description_blog_tags'] = "Affiche une liste de blog. Plus le titre d'un blog est gros, plus il est visité.";
$string['description_course_summary'] = "Affiche le résumé ou la description du cours, tel que vous l'avez rédigé dans les Paramètres.";
$string['description_feedback'] = "Fournit un raccourci vers des activités de feedback global s'il y en a sur la page d'accueil.";
$string['description_glossary_random'] = "Affiche au hasard des entrées d'un de vos glossaires, des images ou des astuces, etc.";
$string['description_login'] = "Permet aux utilisateurs qui ne sont pas encore identifiés dans le site de rentrer leur « Nom d'utilisateur » et « Mot de passe » pour se connecter, de créer un nouveau compte, ou de retrouver / restaurer leur mot de passe.";
$string['description_participants'] = "Affiche les enseignants et les étudiants du cours.";
$string['description_catalogue'] = "SEFIAP";
$string['description_myprofile'] = "Affiche quelques éléments du profil de l'utilisateur.";
$string['description_course_main_menu'] = "Pour naviguer entre les sections du cours.";
$string['description_mnet_hosts'] = "The Network Servers block allows you to roam to other Moodle (or Mahara) servers.";
$string['description_settings'] = "The settings block provides context-sensitive links to settings pages.";
$string['description_enrol_demands'] = 'SEFIAP';
$string['description_course_contents'] = "Affiche la liste des sections (ou semaines) visibles dans ce cours.";
$string['description_course_list'] = "Affiche l'ensemble des cours auxquels l'utilisateur est inscrit et facilite le passage d'un cours à un autre.";
$string['description_course_overview'] = "Affiche les cours auxquels l’utilisateur est inscrit ainsi que les suivis à faire dans ces cours. Activez le mode édition et un menu déroulant permet à l'utilisateur de sélectionner le nombre de cours à afficher.";
$string['description_html'] = "Bloc standard utilisé pour ajouter du texte ou des images sur une page de site ou de cours. Le titre peut être personnalisé ou laissé vide.";
$string['description_private_files'] = "Donne accès au dépôt 'Fichiers personnels' de l'utilisateur.";
$string['description_blog_recent'] = "Affiche les N dernières entrées de blog, filtrées par contexte.";
$string['description_quiz_results'] = "Affiche aux étudiants leurs notes pour un test choisi. Selon les paramètres, les notes affichées seront par groupe ou par étudiant et en pourcentage, en nombre absolu ou en fraction.";
$string['description_recent_activity'] = "Liste les activités ajoutées au cours depuis votre dernière visite, comme les messages postés dans les forums ou les fichiers ajoutés par l'enseignant.";
$string['description_section_links'] = "Affiche les numéros des sections présentes dans le cours. Cliquez sur le numéro de la section pour accéder directement à celle-ci.";
$string['description_site_main_menu'] = "Ce bloc est utilisé par l’administrateur, sur la page d'accueil du site, pour donner accès à des ressources et des activités destinées aux utilisateurs ayant un rôle particulier sur la page d'accueil.";
$string['description_social_activities'] = "Permet d'ajouter des ressources et des activités supplémentaires dans un cours au format social.";
$string['description_activity_results'] = "Affiche les résultats des activités notées ou classées de ce cours.";
$string['description_calendar_month'] = "Affiche les événements prévus ce mois-ci.";
$string['description_online_users'] = "Affiche une liste d'utilisateurs connectés sur ce cours, c'est à dire ayant effectué au moins une action sur ce cours dans les 5 dernières minutes (par défaut), la liste étant mise à jour régulièrement.";
$string['description_mentees'] = "Fournit au(x) tuteur(s) un accès rapide au(x) profil(s) de leur(s) tutoré(s).";
$string['description_completionstatus'] = "Montre l'avancement d'un étudiant dans le cours par rapport à des critères spécifiques.";
$string['description_messages'] = "Permet d’envoyer un message personnel à n’importe quel utilisateur du site.";
$string['description_rss_client'] = "Permet de diffuser des flux RSS de sites extérieurs dans votre cours.";
$string['description_search_forums'] = "Permet d’effectuer une recherche sur tous les forums du cours.";
$string['description_selfcompletion'] = "Permet aux étudiants de déclarer qu'ils ont terminé le cours. Cela peut faire partie des conditions d'achèvement du cours.";
$string['description_badges'] = "Affiche les badges obtenus par cet utilisateur.";
$string['description_news_items'] = "Affiche les derniers messages du forum des nouvelles, envoyés par l'enseignant aux étudiants.";
$string['description_navigation'] = "Le bloc Navigation apparaît sur toutes les pages du site. Il facilite les déplacements dans l'arborescence, dont le contenu varie selon le rôle de l'utilisateur et l’endroit où il se trouve sur le site.";
$string['description_tag_youtube'] = "Ne peut être ajouté que sur la page des tags. Si un utilisateur indique des mots-clés dans son profil ou son blog, ces mots-clés deviennent des tags.";
$string['description_tag_flickr'] = "Ne peut être ajouté que sur la page des tags. Permet d'afficher des images en fonction de critères.";
$string['description_tags'] = "Chaque tag du site est affiché d'autant plus gros qu'il y a d'objets associés à lui sur le site.";
$string['description_conditional_course'] = "SEFIAP";
$string['description_admin_bookmarks'] = "Permet à l'administrateur de se créer des raccourcis vers des pages d'administration du site.";

$string['link_activity_modules'] = 'fr/Bloc_Activités';
$string['link_admin_bookmarks'] = 'fr/Bloc_Marque-pages_administrateur';
$string['link_blog_menu'] = 'fr/Bloc_Menu_blog';
$string['link_calendar_month'] = 'fr/Bloc_Calendrier';
$string['link_comments'] = 'fr/Bloc_Commentaires';
$string['link_community'] = 'fr/Bloc_Recherche_communaut%C3%A9';
$string['link_html'] = 'fr/Bloc_HTML';
$string['link_navigation'] = 'fr/Bloc_Navigation';
$string['link_news_items'] = 'fr/Bloc_Derni%C3%A8res_nouvelles';
$string['link_online_users'] = 'fr/Bloc_Utilisateurs_en_ligne';
$string['link_participants'] = 'fr/Bloc_Personnes';
$string['link_private_files'] = 'fr/Bloc_Fichiers_personnels';
$string['link_recent_activity'] = 'fr/Bloc_Activit%C3%A9_r%C3%A9cente';
$string['link_social_activities'] = 'fr/Bloc_Activit%C3%A9s_sociales';
