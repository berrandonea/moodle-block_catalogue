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
 * File : list/sections/choosesection.php
 * Choose which section the selected action will be applied to.
 */

require_once('../../../../config.php');
require_once("blockcatalogue.list.php");
require_once("../../lib.php");

// Check params.
$courseid = required_param('course', PARAM_INT);
$elementname = required_param('action', PARAM_TEXT);

// Context access control.
$course = get_course($courseid);
require_login($course);
$coursepage = "$CFG->wwwroot/course/view.php?id=$courseid";
$coursecontext = context_course::instance($courseid);
$list = new blockcatalogue_list_sections();

$canupdate = has_capability('moodle/course:update', $coursecontext);
switch ($elementname) {
    case 'goto':
        $condition = true;
        $page = 'view';
        $args = array('id' => $courseid);
        break;

    case 'delete':
        $condition = $canupdate;
        $page = 'editsection';
        $args = array('sr' => 0, 'delete' => 1);
        break;

    case 'edit':
        $condition = $canupdate;
        $page = 'editsection';
        $args = array('sr' => 0);
        break;

    case 'highlight':
        $condition = $canupdate;
        $page = 'view';
        $args = array('id' => $courseid, 'sesskey' => sesskey());
        break;

    case 'hideshow':
        $condition = has_capability('moodle/course:sectionvisibility', $coursecontext);
        $page = 'view';
        $args = array('id' => $courseid, 'sesskey' => sesskey());
        break;

    case 'picture':
        $condition = $canupdate;
        $page = 'format/grid/editimage';
        $args = array('contextid' => $coursecontext->id, 'userid' => $USER->id);
        break;

    default:
        $condition = false;
        break;
}

// Fine access control.
if (!$condition) {
    header("Location: $coursepage");
}

// Header code.
$PAGE->set_title($course->fullname);
$params = array('course' => $courseid, 'action' => $elementname);
$moodlefilename = '/blocks/catalogue/list/sections/choosesection.php';
$PAGE->set_url($moodlefilename, $params);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);

// Page display.
echo $OUTPUT->header();
echo '<h1>'.get_string("sections_$elementname", 'block_catalogue').'</h1>';
echo '<p>'.get_string("sections_description_$elementname", 'block_catalogue').'</p>';
echo '<h2>'./*BRICE get_string('chooseplace', 'block_catalogue')*/'Choisissez une section'.'</h2>';
echo '<ul>';
$targetpage = "$CFG->wwwroot/course/$page.php";

$sections = $DB->get_recordset('course_sections', array('course' => $courseid));
foreach ($sections as $section) {
    $sectionname = get_section_name($section->course, $section->section);
    if (!$section->visible) {
        if (!has_capability('moodle/course:viewhiddensections', $coursecontext)) {
            continue;
        }
        $style = 'font-style:italic';
    } else if ($COURSE->marker == $section->section) {
        $style = 'font-weight:bold';
    } else {
        $style = '';
    }
    // Set section specific argument.
    switch ($elementname) {
        case 'goto':
            $args['section'] = $section->section;
            break;
        case 'delete':
            $args['id'] = $section->id;
            break;
        case 'edit':
            $args['id'] = $section->id;
            break;
        case 'highlight':
            $args['marker'] = $section->section;
            break;
        case 'hideshow':
            if ($section->visible) {
                $args['hide'] = $section->section;
            } else {
                $args['show'] = $section->section;
            }
            break;
        case 'picture':
            $args['sectionid'] = $section->id;
            break;
        default:
            break;
    }
    $url = new moodle_url($targetpage, $args);
    echo "<li style='padding-bottom:25px'><a href='$url' style='$style'>$sectionname</a></li>";
    /* BRICE if ($elementname == 'goto') {
        block_catalogue_section_toc($section->id);
    } */
}

echo '</ul>';
echo $OUTPUT->footer();
$sections->close();

