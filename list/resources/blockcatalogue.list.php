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
 * File : list/activities/blockcatalogue.list.php
 * Class definition for the resources list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

/**
 * Class definition for the resources list.
 *
 * @copyright 2016 Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blockcatalogue_list_resources extends blockcatalogue_list {
    /**
     * Constructor for the class.
     */
    public function __construct() {
        $this->name = 'resources';
        $this->prefix = 'mod';
        $this->color = '#ffc000';
        $this->categories = array('resource');
        $this->potentialmembers = array();
        $this->defaultfavorites = array('folder');
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
     * @global object $OUTPUT
     * @param string $modname
     * @param string $nature
     * @return string
     */
    public function get_local_data($modname, $nature) {
        global $CFG, $OUTPUT;
        $component = "mod_$modname";
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
                if ($manager->string_exists('modulename_link', $component)) {
                    $link = "$this->standarddocdir/$CFG->branch/$CFG->lang/".get_string('modulename_link', $component);
                    return $link;
                } else {
                    return null;
                }

            case 'iconurl' :
                $iconurl = block_catalogue_pixurl('icon', "mod_$modname");
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
        global $CFG, $DB;
        $capability = "mod/$elementname:addinstance";
        if (!$DB->record_exists('capabilities', array('name' => $capability))) {
            return false;
        }
        if (!has_capability($capability, $coursecontext)) {
            return false;
        }
        $libfile = "$CFG->dirroot/mod/$elementname/lib.php";
        if (!file_exists($libfile)) {
            return false;
        }
        $common = $this->common_sortout($elementname);
        if ($common) {
            return true;
        }
        include_once($libfile);
        $supportfunction = $elementname.'_supports';
        if ($supportfunction(FEATURE_MOD_ARCHETYPE) == MOD_ARCHETYPE_RESOURCE) {
            $this->availables['resource'][] = $elementname;
            return true;
        }
        return false;
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
