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
 * File : list/sections/blockcatalogue.list.php
 * Class definition for the list of section management tools.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

/**
 * Class definition for the resources list.
 *
 * @copyright 2016 Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blockcatalogue_list_sections extends blockcatalogue_list {
    /**
     * Constructor for the class.
     */
    public function __construct() {
        $this->name = 'sections';
        $this->prefix = 'section';
        $this->categories = array('coursesections');
        $this->potentialmembers = array();
        $this->defaultfavorites = array('goto','add');
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
        $this->availables['coursesections'] = array('goto');
        if (has_capability('moodle/course:update', $coursecontext)) {
            $this->availables['coursesections'][] = 'add';
            $this->availables['coursesections'][] = 'edit';
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
        switch ($nature) {
            case 'description' :
                $description = get_string($this->name.'_description_'.$elementname, 'block_catalogue');
                return $this->control_string($description);
            case 'link' :
                return null;
            case 'iconurl' :
                $iconurl = $this->get_local_iconurl('', $elementname);
                return $iconurl;
            default :
                return null;
        }
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
        } else {
            $targetpage = "$CFG->wwwroot/blocks/catalogue/list/sections/choosesection.php";
            $args = array('course' => $COURSE->id, 'action' => $elementname);
        }
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
