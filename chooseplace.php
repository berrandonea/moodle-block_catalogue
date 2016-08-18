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
 * File : chooseplace.php
 * Choose at the bottom of which section the new mod will be added
 */

require_once('../../config.php');
require_once('lib.php');

// Check params.
$listname = required_param('list', PARAM_ALPHA);
$courseid = required_param('course', PARAM_INT);
$mod = required_param('mod', PARAM_TEXT);
$type = optional_param('type', '', PARAM_TEXT);

if ($listname == 'blocks') {
    $elementname = required_param('block', PARAM_TEXT);
} else if ($type) {
    $elementname = $type;
} else {
    $elementname = $mod;
}

// Access control.
$course = get_course($courseid);
require_login($course);
$coursepage = "$CFG->wwwroot/course/view.php?id=$courseid";
$list = block_catalogue_instanciate_list($listname);
$permitted = $list->can_add($elementname);
if (!$permitted) {
    header("Location: $coursepage");
}

// Header code.
$elementlocalname = $list->get_element_localname($elementname);
$PAGE->set_title($course->fullname);
$args = array('list' => $listname, 'course' => $courseid, 'mod' => $mod, 'type' => $type);
$moodlefilename = '/blocks/catalogue/chooseplace.php';
$PAGE->set_url($moodlefilename, $args);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);

// Add block to left column.
if ($listname == 'blocks') {
    $list->add_block($elementname, $courseid, 'side-pre');
    header("Location: $coursepage");
}

$targetpage = "$CFG->wwwroot/course/modedit.php";
$sections = $DB->get_recordset('course_sections', array('course' => $COURSE->id));

// Page display.
echo $OUTPUT->header();
echo '<h1>'.get_string('addnew', 'block_catalogue')." $elementlocalname</h1>";
echo '<h2>'.get_string('chooseplace', 'block_catalogue').'</h2>';
echo '<ul>';
foreach ($sections as $section) {
    $sectionname = get_section_name($section->course, $section->section);
    if (!$section->visible) {
        $style = 'font-style:italic';
    } else if ($COURSE->marker == $section->section) {
        $style = 'font-weight:bold';
    } else {
        $style = '';
    }
    $args = array('add' => $mod,
                  'type' => $type,
                  'course' => $section->course,
                  'section' => $section->section,
                  'return' => 0,
                  'sr' => 0);
    $url = new moodle_url($targetpage, $args);
    echo "<li style='padding-bottom:25px'><a href='$url' style='$style'>$sectionname</a></li>";
}
echo '</ul>';
echo $OUTPUT->footer();
$sections->close();
