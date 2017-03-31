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
 * Choose : - at the bottom of which section the new mod will be added.
 *    or    - where you want to go, now.
 */

require_once('../../config.php');
require_once('lib.php');
require_once("$CFG->dirroot/course/renderer.php");

// Check params.
$courseid = required_param('course', PARAM_INT);
$args = array('course' => $courseid);
$map = optional_param('map', 0, PARAM_INT);
if (!$map) {
	$listname = required_param('list', PARAM_ALPHA);
	$args['list'] = $listname;
	if ($listname == 'blocks') {
		$elementname = required_param('block', PARAM_TEXT);
		$args['blocks'] = $elementname;
	} else {
		$mod = required_param('mod', PARAM_TEXT);
		$args['mod'] = $mod;
		$type = optional_param('type', '', PARAM_TEXT);
		$args['type'] = $type;
		if ($type) {
			$elementname = $type;
		} else {
			$elementname = $mod;
		}
		$sectionid = optional_param('sectionid', 0, PARAM_INT);
		$aftermod = optional_param('aftermod', 0, PARAM_INT);
	}
} else {
	$args['map'] = 1;
}

// Access control.
$course = get_course($courseid);
require_login($course);
$coursepage = "$CFG->wwwroot/course/view.php?id=$courseid";
$coursecontext = context_course::instance($courseid);
$thisfilename = '/blocks/catalogue/chooseplace.php';
$coursenbsections = $DB->get_field('course_format_options', 'value',
                                   array('courseid' => $COURSE->id, 'name' => 'numsections'));
if ($map) {
    $editinglist = block_catalogue_instanciate_list('editing');
} else {
	$list = block_catalogue_instanciate_list($listname);
	$listlocalname = $list->get_localname();
	$elementlocalname = $list->get_element_localname($elementname);
	$permitted = $list->can_add($elementname);
	if (!$permitted) {
        header("Location: $coursepage");
    }
    $targetfilename = $list->get_modedit();
    $targetcommonurl = "$CFG->wwwroot/$targetfilename?add=$mod&type=$type&course=$courseid&return=0&sr=0";
}

// Once the user has chosen where to add a module.
if (isset($sectionid)) {
	if ($sectionid) {
	    $section = $DB->get_record('course_sections', array('id' => $sectionid, 'course' => $courseid), '*', MUST_EXIST);
	    $sequence = explode(',', $section->sequence);
	    $sectionlastcmid = end($sequence); //cmid of the current last mod in this section.
	    reset($sequence);
	    if ($aftermod) {
		    $newsequence = '';
	    } else { // If the new mod is placed at the beginning of the section.
		    $newsequence = -$sectionlastcmid.',';
	    }
	    foreach ($sequence as $cmid) {
		    $newsequence .= "$cmid,";
		    if ($cmid == $aftermod) {
			    $newsequence .= -$sectionlastcmid.',';
		    }
	    }
	    $section->sequence = substr($newsequence, 0, -1); //Remove the last comma.
	    $DB->update_record('course_sections', $section);
	    $url = $targetcommonurl."&section=$section->section";
	    header("Location: $url");
    }
}

// Header code.
$PAGE->set_title($course->fullname);
$PAGE->set_url($thisfilename, $args);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('pluginname', 'block_catalogue'));
if ($map) {
	$title = get_string('coursemap', 'block_catalogue');
} else {
	$PAGE->navbar->add($listlocalname, 'index.php?name='.$listname.'&course='.$COURSE->id);
    $title = get_string('addnew', 'block_catalogue').' '.$elementlocalname;    
}
$PAGE->navbar->add($title, '');

// Add block to left column.
if (isset($listname)) {
    if ($listname == 'blocks') {
        $list->add_block($elementname, $courseid, 'side-pre');
        header("Location: $coursepage");
    }
}

$sections = $DB->get_recordset('course_sections', array('course' => $COURSE->id));

// Page display.
$USER->editing = 0;
echo $OUTPUT->header();
echo '<h1>'.$title.'</h1>';
if (isset($list)) {
    echo '<div max-width="50%">';
    echo $list->get_element_data($elementname, 'description');
    echo '</div>';
    echo '<br><br>';
    echo '<h2>'.get_string('addwhere', 'block_catalogue').'</h2>';
} else {
	echo '<h2>'.get_string('gowhere', 'block_catalogue').'</h2>';
}

$renderer = new core_course_renderer($PAGE, '');
$completioninfo = new completion_info($course);
$modinfo = get_fast_modinfo($course);

if ($map) {
	$editinglist->display_all_buttons('');
	echo '<div style="text-align:center;size:20">';
	echo "<a href='$coursepage'><button class='btn btn-secondary'>$COURSE->fullname</button></a>";
	echo '</div>';
	$herebutton = '';
} else {
	$herebutton = '<button class="btn btn-secondary">'.get_string('here', 'block_catalogue').'</button>';
    $args = array('mod' => $mod,
                  'type' => $type,
                  'course' => $courseid,
                  'list' => $listname);
    $selectmodurl = '';
}

echo '<table>';
foreach ($sections as $section) {
	if ($section->section > $coursenbsections) {
		$section->visible = 0;
		$section->name = get_string('orphanedactivitiesinsectionno', '', $section->section);
	}
	if (!$section->visible && !has_capability('moodle/course:viewhiddensections', $coursecontext)) {
		continue;
	}
	$args['sectionid'] = $section->id;
	if ($COURSE->marker == $section->section) {
		$highlighting = "style='border:2px solid red'";
	} else {
		$highlighting = '';
	}
	if ($section->visible) {
		$hidden = "style='font-weight:bold'";
	} else {
		$hidden = "style='color:gray'";
	}
	echo "<tr $highlighting id='section$section->id'>";
	echo '<td>';
	if ($map) {
		echo "<a href='$coursepage&section=$section->section'>";
		echo '<button class="btn btn-secondary">';
	}
	if ($section->name) {
		echo "<span $hidden>$section->name</span>";
	} else {
		echo "<span $hidden>Section $section->section</span>";
	}
	if ($map) {
		echo '</button>';
		echo "</a>";
	}
	echo '</td><td> &nbsp; </td><td>';
    $args['aftermod'] = 0;
	$placeurl = new moodle_url($thisfilename, $args);
	echo '<a style="padding-left:30px;float:left;margin-top:10px;margin-bottom:30px" href="'.$placeurl.'">'.$herebutton.'</a>';
	if (!empty($modinfo->sections[$section->section])) {
		foreach ($modinfo->sections[$section->section] as $cmid) {			
			$cminfo = $modinfo->cms[$cmid];			
			if ($modulehtml = $renderer->course_section_cm_list_item($course,
							$completioninfo, $cminfo, null)) {
			    if ($map) {
					$cm = $DB->get_record('course_modules', array('id' => $cmid));
			        $module = $DB->get_record('modules', array('id' => $cm->module));
			        $selectmodurl = "$CFG->wwwroot/mod/$module->name/view.php?id=$cmid";
					//~ echo "<a href='$modurl'><button class='btn btn-secondary'>";
				}
				block_catalogue_chooseplace_modicon($modulehtml, $cmid, $selectmodurl, true, false);
				if ($map) {
					//~ echo "</button></a>";
				} else {
					$args['aftermod'] = $cmid;
				    $placeurl = new moodle_url($thisfilename, $args);
				    echo '<a style="padding-left:30px;float:left;margin-top:10px;margin-bottom:30px" href="'.$placeurl.'">'.$herebutton.'</a>';
				}				
			}
		}
	}
	echo '</td></tr>';
	echo '<tr><td height="50px;color:gray"><hr></td></tr>';
}
echo '</table>';

$sections->close();
echo $OUTPUT->footer();
