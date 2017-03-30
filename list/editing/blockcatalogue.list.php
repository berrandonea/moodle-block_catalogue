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
        $this->categories = array('sectionsandmods', 'sectionsonly', 'modsonly');
        $this->potentialmembers = array('sectionsandmods' => array('edit', 'hideshow', 'move', 'delete'),
										'sectionsonly' => array('highlight', 'add', 'remove', 'picture'),
										'modsonly' => array('indent', 'unindent', 'duplicate', 'roles'));
        $this->defaultfavorites = array('edit');
        $this->color = '#000000';
    }

    public function actionurl_mod($elementname, $modid) {
        global $CFG, $DB;
        $cm = $DB->get_record('course_modules', array('id' => $modid));
        $page = 'course/mod';
        $args = "sesskey=".sesskey()."&sr=0";
        switch($elementname) {
  	        case 'delete':
	            $args .= "&delete=$modid";
			    break;
	        case 'edit':
	            $args .= "&update=$modid";
		        break;	     
	        case 'duplicate':
	            $args .= "&duplicate=$modid";
	            break;
	        case 'roles':
				$modcontext = context_module::instance($modid);				
				$args = "contextid=$modcontext->id";
				$page = 'admin/roles/assign';
	        default:
	    }
	    $actionurl = "$CFG->wwwroot/$page.php?$args";
	    return $actionurl;
    }

    public function action_section($elementname, $section, $aftersection) {
		global $COURSE, $DB;
		$context = context_course::instance($COURSE->id);
		switch ($elementname) {
		    case 'move':
		        if ($aftersection) { // Move the section. 	            
	                $aftersectionrecord = $DB->get_record('course_sections', array('id' => $aftersection));
	                print_object($aftersectionrecord);
	                $destination = $aftersectionrecord->section;
	                if ($section->section > $destination) {
		                $destination++;
	                }
	                // We change the sections' order so we must update the course's marker.
	                if ($COURSE->marker) {
	                    $highlightedsectionid = $DB->get_field('course_sections', 'id',
					                                           array('course' => $COURSE->id, 'section' => $COURSE->marker));
	                }
	                print_object($destination);
	                move_section_to($COURSE, $section->section, $destination);
	                format_base::reset_course_cache($section->course);
	                $highlightedsection = $DB->get_record('course_sections', array('id' => $highlightedsectionid));
	                $DB->set_field("course", "marker", $highlightedsection->section, array('id' => $section->course));
	                $COURSE->marker = $highlightedsection->section;
	            }
	        break;	    

			case 'delete':
				$page = 'editsection';
				$args = "sr=0&id=$sectionid&delete=1";
				$this->goto_page($page, $args);
				break;

			case 'edit':
				$page = 'editsection';
				$args = "sr=0&id=$sectionid";
				$this->goto_page($page, $args);
				break;

			case 'hideshow':
				require_capability('moodle/course:sectionvisibility', $context);
			    if ($section->visible) {
				    $newvisibility = 0;
			    } else {
				    $newvisibility = 1;
			    }
			    set_section_visible($COURSE->id, $section->section, $newvisibility);
	            break;

			case 'highlight':
				$marker = $section->section;
				$DB->set_field("course", "marker", $marker, array('id' => $section->course));
				format_base::reset_course_cache($section->course);
				$page = 'view';
				$args = "id=$section->course#section-$section->section";
				$this->goto_page($page, $args);
				break;

			case 'picture':
				$page = 'format/grid/editimage';
				$coursecontext = context_course::instance($section->course);
				$args = "contextid=$coursecontext->id&userid=$USER->id&sectionid=$sectionid";
				$this->goto_page($page, $args);
				break;

			default:
		}
		//~ $actionurl = "$CFG->wwwroot/course/$page.php?$args";
	    //~ case 'hideshow':
			//~ if ($section->visible) {
				//~ $newvisibility = 0;
			//~ } else {
				//~ $newvisibility = 1;
			//~ }
			//~ set_section_visible($course->id, $section->id, $newvisibility);
	        //~ break;
	        
	    //~ default:
	        //~ $actionurl = $list->actionurl_section($elementname, $sectionid);
	        //~ header("Location: $actionurl&method=catalogue");
	//~ }
	}

	public function goto_page($page, $args) {
		$actionurl = "$CFG->wwwroot/course/$page.php?$args";
	    header("Location: $actionurl&method=catalogue");
	}

	public function has_ajax_mod($elementname) {
		$array = array('indent', 'unindent', 'hideshow');
		return in_array($elementname, $array);
	}

	public function has_ajax_section($elementname) {
		$array = array();
		return in_array($elementname, $array);
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
            $this->availables['sectionsonly'][] = 'add';
            $this->availables['sectionsonly'][] = 'remove';
            $this->availables['sectionsandmods'][] = 'edit';
            $this->availables['sectionsandmods'][] = 'move';
            $this->availables['sectionsandmods'][] = 'delete';
            $this->availables['sectionsonly'][] = 'highlight';
            $this->availables['modsonly'][] = 'indent';
            $this->availables['modsonly'][] = 'unindent';
            $this->availables['modsonly'][] = 'duplicate';
            $this->availables['modsonly'][] = 'roles';
            if ($COURSE->format == 'grid') {
                $this->availables['sectionsonly'][] = 'picture';
            }
        }
        if (has_capability('moodle/course:sectionvisibility', $coursecontext)) {
            $this->availables['sectionsandmods'][] = 'hideshow';
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
		if ($this->has_ajax_mod($elementname)) {
			return false;
		}
		if (in_array($elementname, $this->potentialmembers['sectionsandmods'])) {
		    return true;
		}
		if (in_array($elementname, $this->potentialmembers['modsonly'])) {
		    return true;
		}
		return false;
		//$elements = array('delete', 'edit', 'hideshow', 'move');
		//return in_array($elementname, $elements);
	}

	public function select_section($elementname) {
		$array = array('delete', 'edit', 'hideshow', 'highlight', 'move', 'picture');
		return in_array($elementname, $array);
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
        $targetpage = "$CFG->wwwroot/blocks/catalogue/list/editing/chooseobject.php";
        $args = array('course' => $COURSE->id, 'action' => $elementname);
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
