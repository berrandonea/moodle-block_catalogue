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
 * File : list/reports/blockcatalogue.list.php
 * Class definition for the reports list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

class blockcatalogue_list_reports extends blockcatalogue_list {
    public function __construct() {
        $this->name = 'reports';
        $this->prefix = '';
        $this->categories = array('report');
        $this->potentialmembers = array();
        foreach ($this->categories as $category) {
            $this->potentialmembers[$category] = array();
        }
        $this->defaultfavorites = array();
    }

    /**
     * Separates the prefix and the proper name of this element.
     * @param string $elementname
     * @return array of strings
     */
    public function divide_name($elementname) {
        $nameparts = explode('_', $elementname);
        return $nameparts;
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
        global $COURSE;
        $coursecontext = context_course::instance($COURSE->id);
        $this->availables['report'] = array();
        $reportplugins = core_component::get_plugin_list('report');
        foreach ($reportplugins as $name => $path) {
            if (($name == 'performance')||($name == 'security')) {
                continue;
            }
            if ($name == 'competency') {
                $competenciesviewer = has_capability('moodle/competency:usercompetencyview', $coursecontext);
                if ($competenciesviewer) {
                    $this->availables['report'][] = "report_competency";
                }
                continue;
            }
            $capability = "report/$name:view";
            if (!get_capability_info($capability)) {
                continue;
            }
            if (has_capability($capability, $coursecontext)) {
                $this->availables['report'][] = "report_$name";
            }
        }
        foreach ($this->categories as $category) {
            $this->sort_by_localname($category);
        }
        return true;
    }

    /**
     * Searches the local code for data about an element
     * @global object $CFG
     * @param string $blockname
     * @param string $nature
     * @return string
     */
    public function get_local_data($elementname, $nature) {
        if ($nature == 'iconurl') {
                $nameparts = $this->divide_name($elementname);
                $localicondir = $nameparts[0].'/'.$nameparts[1].'/pix';
                $iconurl = $this->get_local_iconurl($localicondir, $elementname);
                return $iconurl;
        }
        return null;
    }

    /**
     * Get the name of this element in the current language.
     * @param string $elementname
     * @return string
     */
    public function get_element_localname($elementname) {
        return get_string('pluginname', "$elementname");
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
        $args = array('id' => $COURSE->id, 'course' => $COURSE->id);
        $nameparts = $this->divide_name($elementname);
        if ($nameparts[0] == 'report') {
            $targetpage = "$CFG->wwwroot/report/".$nameparts[1]."/index.php";
        } else {
            $targetpage = "$CFG->wwwroot/".$nameparts[0]."/".$nameparts[1].".php";
        }
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
