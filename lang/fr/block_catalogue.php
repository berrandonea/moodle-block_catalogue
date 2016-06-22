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
