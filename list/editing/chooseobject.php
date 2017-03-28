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
 * File : chooseplace.php
 * Choose at the bottom of which section the new mod will be added
 */

require_once('../../../../config.php');
require_once('../../lib.php');
require_once("$CFG->dirroot/course/renderer.php");

// Check params.
$listname = 'editing';
$elementname = required_param('action', PARAM_ALPHA);
$courseid = required_param('course', PARAM_INT);
$args = array('action' => $elementname, 'course' => $courseid);
$sectionid = optional_param('sectionid', 0, PARAM_INT);
if ($sectionid) {
    $args['sectionid'] = $sectionid;
}
$modid = optional_param('modid', 0, PARAM_INT);
if ($modid) {
    $args['modid'] = $modid;
}
$beforemod = optional_param('beforemod', 0, PARAM_INT);
if ($beforemod) {
    $args['beforemod'] = $beforemod;
}
$tosection = optional_param('tosection', 0, PARAM_INT);
if ($tosection) {
    $args['tosection'] = $tosection;
}
$aftersection = optional_param('aftersection', 0, PARAM_INT);
if ($aftersection) {
    $args['aftersection'] = $aftersection;
}

// Access control.
$course = get_course($courseid);
require_login($course);
$coursepage = "$CFG->wwwroot/course/view.php?id=$courseid";
$movepage = "$CFG->wwwroot/blocks/catalogue/list/editing/"
    ."chooseobject.php?course=$courseid&action=move";
$coursecontext = context_course::instance($courseid);
$list = block_catalogue_instanciate_list($listname);
$permitted = $list->can_do($elementname);
if (!$permitted) {
    header("Location: $coursepage");
}
$thisfilename = '/blocks/catalogue/list/editing/chooseobject.php';

$question = get_string("question_$elementname", 'block_catalogue');
$selectsection = $list->select_section($elementname);
$selectmod = $list->select_mod($elementname);
$betweensections = false;
$betweenmods = false;

// Once the user has chosen a section
if ($sectionid) {
    if ($elementname == 'move') { // Moving a section.
        if ($aftersection) { // Move the section.
 	    $section = $DB->get_record('course_sections', array('id' => $sectionid));
	    $aftersectionrecord = $DB->get_record('course_sections', array('id' => $aftersection));
	    $destination = $aftersectionrecord->section;
	    if ($section->section > $destination) {
		$destination++;
	    }
	    // We change the sections' order so we must update the course's marker.
	    if ($COURSE->marker) {
	        $highlightedsectionid = $DB->get_field('course_sections', 'id',
						array('course' => $COURSE->id, 'section' => $COURSE->marker));
	    }
	    move_section_to($COURSE, $section->section, $destination);
	    $highlightedsection = $DB->get_record('course_sections', array('id' => $highlightedsectionid));
	    $DB->set_field("course", "marker", $highlightedsection->section, array('id' => $section->course));
	    format_base::reset_course_cache($section->course);
	    header("Location: $movepage#section".$destination);
	} else { // Select destination.
	    $question = get_string('movewhere', 'block_catalogue');
	    $selectsection = false;
	    $selectmod = false;
	    $betweensections = true;
	}
} else {
	$actionurl = $list->actionurl_section($elementname, $sectionid);
	header("Location: $actionurl&method=catalogue");
    }
}

//Once the user has chosen a mod
if ($modid) {
	$cm = get_coursemodule_from_id('', $modid, 0, true, MUST_EXIST);
	$modcontext = context_module::instance($modid);
	require_capability('moodle/course:manageactivities', $modcontext);
	switch ($elementname) {
		case 'move':			// Moving a mod.
			if ($tosection) {  // Move the mod
				$modinfo = get_fast_modinfo($course);
				$movedcminfo = $modinfo->cms[$modid];
				$tosectionrecord = $DB->get_record('course_sections', array('id' => $tosection));
				moveto_module($movedcminfo, $tosectionrecord, $beforemod);
				header("Location: $movepage#section".$tosection);
			} else {  // Select destination.
				$question = get_string('movewhere', 'block_catalogue');
				$selectsection = false;
				$selectmod = false;
				$betweenmods = true;
			}
			break;

		case 'indent':
			$cm->indent++;
			$DB->set_field('course_modules', 'indent', $cm->indent, array('id'=>$modid));
			rebuild_course_cache($cm->course);
			break;

		case 'unindent':
			$cm->indent--;
			if ($cm->indent < 0) {
			    $cm->indent = 0;
		    }
		    $DB->set_field('course_modules', 'indent', $cm->indent, array('id'=>$modid));
			rebuild_course_cache($cm->course);
			break;

		default:
		    $actionurl = $list->actionurl_mod($elementname, $modid);
		    header("Location: $actionurl");
	}
}

// Header code.
$elementlocalname = $list->get_element_localname($elementname);
$PAGE->set_title($course->fullname);
$PAGE->set_url($thisfilename, $args);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'block_catalogue'));
$listlocalname = $list->get_localname();
$PAGE->navbar->add($listlocalname, 'index.php?name='.$listname.'&course='.$COURSE->id);
$title = $elementlocalname;
$PAGE->navbar->add($title, '');
$PAGE->requires->js("/blocks/catalogue/js/block_catalogue.js");

$sections = $DB->get_recordset('course_sections', array('course' => $COURSE->id));

// Page display.
$USER->editing = 0;
echo $OUTPUT->header();
$list->display_all_buttons($elementname);
echo '<h1>'.$title.'</h1>';
echo '<div style="max-width:50%">';
echo $list->get_element_data($elementname, 'description');
echo '</div>';
echo '<br><br>';
echo '<h2>'.$question.'</h2>';

$renderer = new core_course_renderer($PAGE, '');
$completioninfo = new completion_info($course);
$modinfo = get_fast_modinfo($course);
$herebutton = '<button class="btn btn-secondary">'.get_string('here', 'block_catalogue').'</button>';
$moduleshtml = array();

echo '<table>';

foreach ($sections as $section) {
    if (!$section->visible && !has_capability('moodle/course:viewhiddensections', $coursecontext)) {
	continue;
    }
    if ($COURSE->marker == $section->section) {
	$highlighting = "style='border:2px solid red'";
    } else {
	$highlighting = '';
    }
    if ($section->id == $sectionid) { // If we're moving this section.
	$hidden = "style='color:red'";
    } else if ($section->visible) {
	$hidden = "style='font-weight:bold'";
    } else {
	$hidden = "style='color:gray'";
    }
    echo "<tr $highlighting id='section$section->id'>";
    echo '<td>';
    if ($selectsection) {
	$sectionargs = $args;
	$sectionargs['sectionid'] = $section->id;
	$selectsectionurl = new moodle_url($thisfilename, $sectionargs);
	echo '<a style="padding-left:30px;float:left;margin-top:10px;margin-bottom:30px" href="'.$selectsectionurl.'">';
	echo '<button class="btn btn-secondary">';
    }
    if ($section->name) {
	echo "<span $hidden>$section->name</span>";
    } else {
	echo "<span $hidden>Section $section->section</span>";
    }
    if ($selectsection) {
	echo '</button></a>';
    }
    echo '</td><td> &nbsp; </td><td>';
    if (!empty($modinfo->sections[$section->section])) {
	foreach ($modinfo->sections[$section->section] as $cmid) {
	    $cminfo = $modinfo->cms[$cmid];
	    if ($modulehtml = $renderer->course_section_cm_list_item($course, $completioninfo, $cminfo, null)) {
		if ($betweenmods && ($cmid != $modid)) { // If we're moving a mod, but not this one.
		    $placemodargs = $args;
		    $placemodargs['beforemod'] = $cmid;
		    $placemodargs['tosection'] = $section->id;
		    $placeurl = new moodle_url($thisfilename, $placemodargs);
		    echo '<a style="padding-left:30px;float:left;margin-top:10px;margin-bottom:30px"
				href="'.$placeurl.'">'.$herebutton.'</a>';
		}
		if ($selectmod) {
		    $modargs = $args;
		    $modargs['modid'] = $cmid;
		    $selectmodurl = new moodle_url($thisfilename, $modargs);
		} else {
		    $selectmodurl = '';
		}
		if ($cmid == $modid) { // If we're moving this mod.
		    echo '<div style="color:red">';
		}
		if ($elementname == 'indent' || $elementname == 'unindent') {
		    echo "<div id='jsmod$cmid' onclick='indent(\"$elementname\", \"$cmid\")'>";
		    block_catalogue_chooseplace_modicon($modulehtml, $cmid, '', false);
		    echo "</div>";
		} else {
		    block_catalogue_chooseplace_modicon($modulehtml, $cmid, $selectmodurl, true);
		}
		if ($cmid == $modid) { // If we're moving this mod.
		    echo '</div>';
		}
	    }
	}
    }
    if ($betweenmods) {
	$placemodargs = $args;
	$placemodargs['beforemod'] = 0;
	$placemodargs['tosection'] = $section->id;
	$placeurl = new moodle_url($thisfilename, $placemodargs);
	echo '<a style="padding-left:30px;float:left;margin-top:10px;margin-bottom:30px" href="'.$placeurl.'">'.$herebutton.'</a>';
    }
    echo '</td></tr>';
    echo '<tr><td height="50px;color:gray"><hr></td></tr>';
    if ($betweensections && ($section->id != $sectionid)) { // If we're moving a section, but not this one.
	$placesectionargs = $args;
	$placesectionargs['aftersection'] = $section->id;
	$placeurl = new moodle_url($thisfilename, $placesectionargs);
	echo '<tr><td>';
	echo '<a style="padding-left:30px;float:left;margin-top:10px;margin-bottom:30px" href="'.$placeurl.'">'.$herebutton.'</a>';
	echo '</td></tr>';
	echo '<tr><td height="50px;color:gray"><hr></td></tr>';
    }
}
echo '</table>';

$sections->close();
echo $OUTPUT->footer();
?>
