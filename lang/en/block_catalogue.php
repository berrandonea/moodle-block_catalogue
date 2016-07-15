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
 * File : lang/en/block_catalogue.php
 * English strings.
 */

defined('MOODLE_INTERNAL') || die();
$string['pluginname'] = 'Catalogue';
$string['catalogue'] = 'Catalogue';
$string['config_blocktitle_default'] = 'Catalogue';
$string['catalogue:addinstance'] = 'Add a new Catalogue block';
$string['catalogue:myaddinstance'] = 'Add a new Catalogue block to the My Moodle page';
$string['catalogue:edit'] = 'Edit descriptions and links in the catalogue block';
$string['catalogue:togglefav'] = 'Add or remove favorites in block Catalogue';
$string['catalogue:togglehide'] = 'Hide or show elements in block Catalogue';
$string['addnew'] = 'Adding a new ';
$string['chooseplace'] = 'Please choose a place';
$string['sortorder'] = 'Sort order';
$string['hover'] = 'Hover a description with your mouse to read complete text';
$string['doc'] = 'Documentation';
$string['favorites'] = 'Favorites';
$string['favorites_help'] = 'You can add or remove favorites by visiting the above categories.';
$string['nofavs'] = 'You haven\'t declared any favorite yet.';
$string['on_fav'] = 'Add to favorites';
$string['off_fav'] = 'Remove from favorites';
$string['on_hide'] = 'Hide';
$string['off_hide'] = 'Show';
$string['headerconfig'] = 'Catalogue blocks settings';
$string['descconfig'] = 'Please choose which lists will be displayed in the catalogue blocks, and in which order.';
$string['displayedlists'] = 'Displayed lists';
$string['descdisplayedlists'] = 'Possible values : activities, blocks, customlabels (requires mod_customlabel), enrols, grades, reports, resources. Write them with no blanck space, separated by commas.';
$string['getremotedata'] = 'Look for remote data';
$string['descgetremotedata'] = 'If set, will search the Moodle online documentation and the Moodle plugin directory for data about the items in the catalogue, causing a longer loading delay for the page. Once found, a data is stored locally and not searched anymore.';
$string['edit1'] = 'Close manager interface';
$string['edit0'] = 'Open manager interface';
