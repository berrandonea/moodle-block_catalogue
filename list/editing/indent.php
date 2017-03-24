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

require_once('../../../../config.php');
require_once('../../lib.php');
require_once("$CFG->dirroot/course/renderer.php");

$action = required_param('action', PARAM_TEXT);
$cmid = required_param('cmid', PARAM_TEXT);

// Check permission.
$cm = get_coursemodule_from_id('', $cmid, 0, true, MUST_EXIST);
$course = get_course($cm->course);
$PAGE->set_course($course);
require_login($course);
$modcontext = context_module::instance($cmid);
require_capability('moodle/course:manageactivities', $modcontext);

// Indent or unindent.
if ($action == 'indent') {
	$cm->indent++;
} else if ($action == 'unindent') {
	$cm->indent--;
	if ($cm->indent < 0) {
	    $cm->indent = 0;
    }
}
$DB->set_field('course_modules', 'indent', $cm->indent, array('id'=>$cmid));
rebuild_course_cache($course->id);

// Output.
$completioninfo = new completion_info($course);
$modinfo = get_fast_modinfo($course);
$cminfo = $modinfo->cms[$cmid];
$renderer = new core_course_renderer($PAGE, '');
$modulehtml = $renderer->course_section_cm_list_item($course, $completioninfo, $cminfo, null);
block_catalogue_chooseplace_modicon($modulehtml, $cmid, '', false);
