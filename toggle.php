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
 * @copyright     Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : toggle.php
 * Used within AJAX process to toggle favorites and hidings.
 */

require_once('../../config.php');
require_once('lib.php');

global $DB, $PAGE, $USER;

$listname = required_param('list', PARAM_TEXT);
$elementname = required_param('element', PARAM_TEXT);
$toggler = required_param('toggler', PARAM_TEXT);
$courseid = required_param('courseid', PARAM_INT);
$default = required_param('default', PARAM_INT);

// Check permission.
$course = $DB->get_record('course', array('id' => $courseid));
$PAGE->set_course($course);
require_login($course);
$coursecontext = context_course::instance($courseid);
$usereditor = has_capability("block/catalogue:toggle$toggler", $coursecontext);

if ($usereditor) {
    // Current state for this element.
    $table = "block_catalogue_$toggler";
    $params = array('listname' => $listname, 'elementname' => $elementname);
    if ($toggler == 'fav') {
        $params['userid'] = $USER->id;
    }
    $recorded = $DB->get_record($table, $params);

    // Toggle.
    if ($recorded) {
        $DB->delete_records($table, array('id' => $recorded->id));
    } else {
        $record = new stdClass();
        if ($toggler == 'fav') {
            $record->userid = $USER->id;
        }
        $record->listname = $listname;
        $record->elementname = $elementname;
        $record->id = $DB->insert_record($table, $record);
    }

    if ($recorded xor $default) {
        $picture = "on_$toggler.png";
        $label = get_string("on_$toggler", 'block_catalogue');
    } else {
        $picture = "off_$toggler.png";
        $label = get_string("off_$toggler", 'block_catalogue');
    }
}

// New content for toggler div.
block_catalogue_display_toggler($picture, $label);
if ($toggler == 'fav') {
    echo '£µ£';
    $listnames = block_catalogue_get_listnames();
    $listsandfavorites = block_catalogue_all_favorites($listnames);
    $bgcolor = get_config('catalogue', 'bgcolor');
    $favstring = block_catalogue_show_favorites($listsandfavorites->favorites, $bgcolor);
    echo $favstring;

    // For theme_catalogue.
    echo '£µ£';
    echo block_catalogue_theme_favorites();
}
