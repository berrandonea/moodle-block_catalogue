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
 * File : list/activities/blockcatalogue.list.php
 * Class definition for the activities and resources list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

class blockcatalogue_list_activities extends blockcatalogue_list {
    public function __construct() {
        $this->name = 'activities';
        $this->prefix = 'mod';
        $this->categories = array('exercise', 'collaborative', 'other');
        $this->potentialmembers = array(
            'exercise' => array('adaptivequiz', 'assign', 'assignment', 'lesson', 'quiz',
                                'workshop', 'elang', 'realtimequiz', 'taskchain'),
            'collaborative' => array('chat', 'data', 'forum', 'bigbluebuttonbn', 'depotetudiant', 'etherpadlite', 'wiki')
        );
        $this->defaultfavorites = array('assign', 'quiz');
        $this->open = false;
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
        $permitted = has_capability("mod/$elementname:addinstance", $coursecontext);
        return $permitted;
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
        global $COURSE, $DB;
        $coursecontext = context_course::instance($COURSE->id);
        $enabledmods = $DB->get_records('modules', array('visible' => 1));
        foreach ($this->categories as $category) {
            $this->availables[$category] = array();
        }
        foreach ($enabledmods as $enabledmod) {
            $this->sortout($enabledmod->name, $coursecontext);
        }
        foreach ($this->categories as $category) {
            $this->sort_by_localname($category);
        }
        return true;
    }

    /**
     * Searches the local code for data about an element
     * @global object $CFG
     * @param string $modname
     * @param string $nature
     * @return string
     */
    public function get_local_data($modname, $nature) {
        global $CFG;
        $component = "mod_$modname";
        switch ($nature) {
            case 'description' :
                $description = get_string('modulename_help', $component);
                return $this->control_string($description);

            case 'link' :
                $sm = get_string_manager();
                if ($sm->string_exists('modulename_link', $component)) {
                    $link = "$this->standarddocdir/$CFG->branch/$CFG->lang/".get_string('modulename_link', $component);
                    return $link;
                } else {
                    return null;
                }

            case 'iconurl' :
                $localicondir = "mod/$modname/pix";
                $iconurl = $this->get_local_iconurl($localicondir, $modname);
                return $iconurl;

            default :
                return null;
        }
    }

    /**
     * Chooses a category for an element of this list
     * @global object $CFG
     * @param string $elementname
     * @param object $coursecontext
     * @return boolean
     */
    public function sortout($elementname, $coursecontext) {
        global $CFG;
        if (!has_capability("mod/$elementname:addinstance", $coursecontext)) {
            return false;
        }
        $common = $this->common_sortout($elementname);
        if ($common) {
            return true;
        }
        $libfile = "$CFG->dirroot/mod/$elementname/lib.php";
        if (!file_exists($libfile)) {
            return false;
        }
        include_once($libfile);
        $supportfunction = $elementname.'_supports';
        if ($supportfunction(FEATURE_MOD_ARCHETYPE) == MOD_ARCHETYPE_RESOURCE) {
            return false;
        }
        $this->availables['other'][] = $elementname;
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
        $args = array('list' => $this->name, 'course' => $COURSE->id, 'mod' => $elementname, 'type' => '');
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
