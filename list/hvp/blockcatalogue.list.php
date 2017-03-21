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
 * File : list/hvp/blockcatalogue.list.php
 * Class definition for the H5P list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");
$hvplib = "$CFG->dirroot/mod/hvp/locallib.php";
if (file_exists($hvplib)) {
    require_once($hvplib);
}

class blockcatalogue_list_hvp extends blockcatalogue_list {
    public function __construct() {
        global $CFG;
        $hashvp = file_exists("$CFG->dirroot/mod/hvp/locallib.php");
        $this->name = 'hvp';
        if (!$hashvp) {
            $this->skip = true;
        }
        $this->prefix = '';
        $this->color = '#000000';
        $this->categories = array('hvp');
        $this->potentialmembers = array();
        foreach ($this->categories as $category) {
            $this->potentialmembers[$category] = array();
        }
        $this->defaultfavorites = array();
        $this->open = true;
    }

    /**
     * Can this user add this element in this course ?
     * @global object $COURSE
     * @param string $elementname
     * @return boolean
     */
    public function can_add($elementname) {
        global $COURSE;
        $coursecontext = context_course::instance($COURSE->id);
        $permitted = has_capability("mod/hvp:addinstance", $coursecontext);
        return $permitted;
    }

    /**
     * Finds the elements available (to this user in this course) for the
     * current list, sorted out by category.
     * @return array of arrays of strings
     *
     * @global object $COURSE
     * @return boolean
     */
    public function fill_availables() {
        global $DB;
        
        //~ 
        //~ $coursecontext = context_course::instance($COURSE->id);
        //~ $enabledcustomlabels = customlabel_get_classes($coursecontext);
        foreach ($this->categories as $category) {
            $this->availables[$category] = array();
        }
        $runnablelibs = $DB->get_records('hvp_libraries', array('runnable' => 1));
        foreach ($runnablelibs as $runnablelib) {
			$nameparts = explode('.', $runnablelib->machine_name);
			if (isset($nameparts[1])) {
				$elementname = $nameparts[1];
			} else {
				$elementname = $nameparts[0];
			}
			$this->availables['hvp'][] = $elementname;
		}
        //~ foreach ($this->categories as $category) { TODO : classer par ordre alphabétique
            //~ $this->sort_by_localname($category);
        //~ }
        return true;
    }

    /**
     * Get the name of this element in the current language.
     * @param string $elementname
     * @return string
     */
    public function get_element_localname($elementname) {
        $localname = get_string("hvp_$elementname", 'block_catalogue');
        return $localname;
    }

    /**
     * Returns the URL of a png icon from the given directory which name doesn't end with 'small'.
     * @global type $CFG
     * @param type $localicondir
     * @return type
     */
    public function get_first_icon($localicondir) {
        global $CFG;
        $dirhandler = opendir("$CFG->dirroot/$localicondir");
        while ($filename = readdir($dirhandler)) {
            $begin = substr($filename, 0, 4);
            $end = substr($filename, -4);
            $longend = substr($filename, -9);
            if (($begin == 'icon') && ($end == '.png') && ($longend != 'small.png')) {
                $iconurl = "$CFG->wwwroot/$localicondir/$filename";
                return $iconurl;
            }
        }
        return null;
    }

    /**
     * Searches the local code for data about an element
     * @global object $CFG
     * @param string $modname
     * @param string $nature
     * @return string
     */
    public function get_local_data($elementname, $nature) {
        global $CFG, $DB, $OUTPUT;
        switch ($nature) {
            case 'description' :
                $localname = $this->get_element_localname($elementname);
                
                return $elementname;

            case 'link' :
				$link = $DB->get_field('hvp_libraries', 'tutorial_url', array('machine_name' => "H5P.$elementname")); 
				if ($link) {
					return $link;
				} else {
					return null;
				}

            case 'iconurl' :
				$icondir = "blocks/catalogue/list/hvp/icons";
				if (file_exists("$CFG->dirroot/$icondir/$elementname.png")) {
					$iconurl = "$CFG->wwwroot/$icondir/$elementname.png";
					return $iconurl;
				} else {
					return $OUTPUT->pix_url('icon', 'mod_hvp');
				}

            default :
                return null;
        }
    }

	/**
     * Page to edit a mod from this list.
     * @return string
     */
    public function get_modedit() {
		return 'blocks/catalogue/list/hvp/modedit.php';
	}

    /**
     * Chooses a category for an element of this list
     * @global object $CFG
     * @param object $element
     * @param object $coursecontext
     * @return boolean
     */
    public function sortout($element) {
        global $CFG;
        $common = $this->common_sortout($element->id);
        if ($common) {
            return true;
        }
        if (in_array($element->family, $this->categories)) {
            $this->availables[$element->family][] = $element->id;
            return true;
        }
        $this->availables['other'][] = $element->id;
        return true;
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
        $targetpage = "$CFG->wwwroot/blocks/catalogue/chooseplace.php";
        $args = array('list' => $this->name, 'course' => $COURSE->id, 'mod' => 'hvp', 'type' => $elementname);
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
