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
 * @copyright 2016 Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : list/editing/blockcatalogue.list.php
 * Class definition for the list of editing tools.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

/**
 * Class definition for the editing list.
 *
 * @copyright 2017 Brice Errandonea <brice.errandonea@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blockcatalogue_list_editing extends blockcatalogue_list {
    /**
     * Constructor for the class.
     */
    public function __construct() {
        $this->name = 'editing';
        $this->prefix = 'editing';
        $this->categories = array('coursesections');
        $this->potentialmembers = array();
        $this->defaultfavorites = array('edit', 'delete', 'move');
        $this->color = '#000000';
    }


	public function actionurl_mod($elementname, $modid) {
		global $CFG, $DB;
		$cm = $DB->get_record('course_modules', array('id' => $modid));
		$page = 'mod';
		$args = "sesskey=".sesskey()."&sr=0";
		switch($elementname) {
			case 'delete':
				$args .= "&delete=$modid";
				break;
			case 'edit':
				$args .= "&update=$modid";
				break;
			case 'hideshow':
				if ($cm->visible) {
					$args .= "&hide=$modid";
				} else {
					$args .= "&show=$modid";
				}
				break;
			case 'move':
				break;
			default:
		}
		$actionurl = "$CFG->wwwroot/course/$page.php?$args";
		return $actionurl;
	}


	public function actionurl_section($elementname, $sectionid) {
		global $CFG, $DB, $USER;
		$section = $DB->get_record('course_sections', array('id' => $sectionid));
		switch($elementname) {
			case 'delete':
				$page = 'editsection';
				$args = "sr=0&id=$sectionid&delete=1";
				break;
			case 'edit':
				$page = 'editsection';			
				$args = "sr=0&id=$sectionid";
				break;
			case 'hideshow':
				$page = 'view';
				$args = "id=$section->course&sesskey=".sesskey();
				if ($section->visible) {
					$args .= "&hide=$section->section";
				} else {
					$args .= "&show=$section->section";
				}
				break;
			case 'highlight':
				$marker = $section->section;
				$DB->set_field("course", "marker", $marker, array('id' => $section->course));
				format_base::reset_course_cache($section->course);
				$page = 'view';
				$args = "id=$section->course#section-$section->section";
				break;
			case 'move':
				// This function should never be called for element "move".
				break;
			case 'picture':
				$page = 'format/grid/editimage';
				$coursecontext = context_course::instance($section->course);
				$args = "contextid=$coursecontext->id&userid=$USER->id&sectionid=$sectionid";
				break;
			default:			
		}
		$actionurl = "$CFG->wwwroot/course/$page.php?$args";
		return $actionurl;
	}

    /**
     * Finds the elements available (to this user in this course) for the
     * current list, sorted out by category.
     * @return array of arrays of strings
     *
     * @global object $COURSE
     * @global object $DB
     * @return boolean
     */
    public function fill_availables() {
        global $COURSE;
        $coursecontext = context_course::instance($COURSE->id);
        $this->availables['coursesections'] = array();
        if (has_capability('moodle/course:update', $coursecontext)) {
            $this->availables['coursesections'][] = 'add';
            $this->availables['coursesections'][] = 'remove';
            $this->availables['coursesections'][] = 'edit';
            $this->availables['coursesections'][] = 'move';
            $this->availables['coursesections'][] = 'delete';
            $this->availables['coursesections'][] = 'highlight';
            if ($COURSE->format == 'grid') {
                $this->availables['coursesections'][] = 'picture';
            }
        }
        if (has_capability('moodle/course:sectionvisibility', $coursecontext)) {
            $this->availables['coursesections'][] = 'hideshow';
        }
        foreach ($this->categories as $category) {
            $this->sort_by_localname($category);
        }
        return true;
    }
    
    /**
     * Tells whether a given action is allowed to this user in this course.
     * 
     * @global object $COURSE
     * @param string $elementname
     * @return boolean
     */
    public function can_do($elementname) {
		global $COURSE;
		$coursecontext = context_course::instance($COURSE->id);
		if ($elementname == 'hideshow') {
			return has_capability('moodle/course:sectionvisibility', $coursecontext);
		} else {
			return has_capability('moodle/course:update', $coursecontext);
		}
	}

    /**
     * Get the name of this element in the current language.
     * Some children override it.
     * @param string $elementname
     * @return string
     */
    public function get_element_localname($elementname) {
        return get_string($this->name.'_'.$elementname, 'block_catalogue');
    }

    /**
     * Searches the local code for data about an element
     * @global object $CFG
     * @global object $OUTPUT
     * @param string $elementname
     * @param string $nature
     * @return string
     */
    public function get_local_data($elementname, $nature) {
		$manager = get_string_manager();
        switch ($nature) {
            case 'description' :
                if ($manager->string_exists('modulename_help', $component)) {
					$description = get_string('modulename_help', $component);
					return $description;
				} else {
					return null;
				}
            case 'link' :
                return null;
            case 'iconurl' :
                $iconurl = $this->get_local_iconurl('', $elementname);
                return $iconurl;
            default :
                return null;
        }
    }

	public function select_mod($elementname) {
		$elements = array('delete', 'edit', 'hideshow', 'move');
		return in_array($elementname, $elements);
	}

	public function select_section($elementname) {
		$elements = array('delete', 'edit', 'hideshow', 'highlight', 'move', 'picture');
		return in_array($elementname, $elements);
	}


    /**
     * Which URL must be visited to use this element ?
     * @global object $CFG
     * @global object $COURSE
     * @param string $elementname
     * @return \moodle_url
     */
    public function usage_url($elementname) {
        global $CFG, $COURSE;
        if ($elementname == 'add') {
            $targetpage = "$CFG->wwwroot/course/changenumsections.php";
            $args = array('courseid' => $COURSE->id,
                          'increase' => 1,
                          'sesskey' => sesskey());
		} else if ($elementname == 'remove') {
			$targetpage = "$CFG->wwwroot/course/changenumsections.php";
            $args = array('courseid' => $COURSE->id,
                          'increase' => 0,
                          'sesskey' => sesskey());
        } else {
            $targetpage = "$CFG->wwwroot/blocks/catalogue/list/editing/chooseobject.php";
            $args = array('course' => $COURSE->id, 'action' => $elementname);
        }
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
