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
 * File : list/grades/blockcatalogue.list.php
 * Class definition for the grades list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

/**
 * Class definition for the resources list.
 *
 * @copyright 2016 Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blockcatalogue_list_grades extends blockcatalogue_list {
    public function __construct() {
        $this->name = 'grades';
        $this->prefix = '';
        $this->categories = array('gradesetting', 'gradereport', 'outcome');
        $this->potentialmembers = array();
        foreach ($this->categories as $category) {
            $this->potentialmembers[$category] = array();
        }
        $this->potentialmembers['outcome'] = array('gradesetting_outcome',
                                                   'gradesetting_outcomecourse',
                                                   'gradereport_outcomes',
                                                   'report_competency',
                                                   'admintool_coursecompetencies');
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
     * Adds a prefix before each name found in $newreports and filters out unusable (grade) reports.
     * @param object $context
     * @param array of strings $newreports
     * @param string $prefix
     * @return array of objects
     */
    public function enlist($context, $newreports, $prefix) {
        $reportsarray = array();
        foreach ($newreports as $name => $path) {
            if (!$this->filter_report($context, $prefix, $name)) {
                continue;
            }
            $report = new stdClass();
            $report->name = $prefix.'_'.$name;
            $reportsarray[] = $report;
        }
        return $reportsarray;
    }

    /**
     * Is this (grade) report usable in this context ?
     * @global object $CFG
     * @param object $context
     * @param string $prefix
     * @param string $name
     * @return boolean
     */
    public function filter_report($context, $prefix, $name) {
        if ($prefix == 'gradesetting') {
            if ($name == 'scale') {
                return has_capability('moodle/course:managescales', $context);
            } else {
                return has_capability('moodle/grade:manage', $context);
            }
        }
        $capable = has_capability("gradereport/$name:view", $context);
        return $capable;
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
        global $COURSE, $DB;
        $coursecontext = context_course::instance($COURSE->id);
        foreach ($this->categories as $category) {
            $this->availables[$category] = array();
        }
        $this->sortout_reports($coursecontext, 'gradereport');
        $this->sortout_reports($coursecontext, 'gradesetting');
        $this->availables['outcome'][] = 'badges_mybadges';
        if (has_capability('moodle/badges:createbadge', $coursecontext)) {
            $this->availables['outcome'][] = 'badges_index';
        }        
        // Competencies.
        $params = array('capability' => 'moodle/competency:usercompetencyview');
        $supportscompetencies = $DB->record_exists('role_capabilities', $params);
        if ($supportscompetencies) {
            $this->availables['outcome'][] = "admintool_coursecompetencies";
            $competenciesviewer = has_capability('moodle/competency:usercompetencyview', $coursecontext);
            if ($competenciesviewer) {
                $this->availables['outcome'][] = "report_competency";
            }
        }
        foreach ($this->categories as $category) {
            $this->sort_by_localname($category);
        }
        return true;
    }

    /**
     * Lists the grade settings pages
     * @global object $CFG
     * @return array of strings
     */
    public function get_grade_settings() {
        global $CFG;
        $settingsdir = "$CFG->dirroot/grade/edit";
        $settingnames = array('outcome', 'tree', 'settings', 'scale', 'letter');
        $gradesettings = array('outcomecourse' => "$settingsdir/outcome/course.php");
        foreach ($settingnames as $settingname) {
            $gradesettings[$settingname] = "$settingsdir/$settingname";
        }
        return $gradesettings;
    }

    /**
     * Searches the local code for data about an element
     * @param string $elementname
     * @param string $nature
     * @return string
     */
    public function get_local_data($elementname, $nature) {
        if ($nature == 'iconurl') {
            $iconurl = $this->get_local_iconurl('', $elementname);
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
        $nameparts = $this->divide_name($elementname);
        switch($nameparts[0]) {
            case 'badges':
                if ($nameparts[1] == 'index') {
                    return get_string('managebadges', 'badges');
                } else {
                    return get_string($nameparts[1], 'badges');
                }                
                break;
            case 'admintool':
                return get_string('coursecompetencies', 'tool_lp');
                break;
            case 'gradesetting':
                $identifier = array('outcomecourse' => 'outcomescourse',
                                'outcome' => 'outcomes',
                                'tree' => 'categoriesanditems',
                                'settings' => 'coursegradesettings',
                                'scale' => 'coursescales',
                                'letter' => 'letters');
                return get_string($identifier[$nameparts[1]], 'grades');
                break;
            default:
                return get_string('pluginname', "$elementname");
        }        
    }

    /**
     * Specific build of the "availables" array attributes for the grades and reports.
     * @param object $coursecontext
     * @param string $prefix
     */
    public function sortout_reports($coursecontext, $prefix) {        
        if ($prefix == 'gradesetting') {
            $reports = $this->get_grade_settings();
        } else {
            $reports = core_component::get_plugin_list($prefix);
        }
        foreach ($reports as $name => $path) {            
            $elementname = $prefix.'_'.$name;            
            if ($this->filter_report($coursecontext, $prefix, $name)) {
                $common = $this->common_sortout($elementname);
                if (!$common) {
                    $this->availables[$prefix][] = $elementname;
                }
            }
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
        $args = array('id' => $COURSE->id, 'course' => $COURSE->id);
        $nameparts = $this->divide_name($elementname);
        switch($nameparts[0]) {
            case 'report' :
                $targetpage = "$CFG->wwwroot/report/".$nameparts[1]."/index.php";
                break;
            case 'gradereport':
                $targetpage = "$CFG->wwwroot/grade/report/".$nameparts[1]."/index.php";
                break;
            case 'badges':
                $targetpage = "$CFG->wwwroot/badges/index.php";
                $args['type'] = 2;
                break;
            case 'gradesetting':
                if ($nameparts[1] == 'outcomecourse') {
                    $targetpage = "$CFG->wwwroot/grade/edit/outcome/course.php";
                } else {
                    $targetpage = "$CFG->wwwroot/grade/edit/".$nameparts[1]."/index.php";
                }
                break;
            case 'admintool':
                $targetpage = "$CFG->wwwroot/admin/tool/lp/coursecompetencies.php";
                $args['courseid'] = $COURSE->id;
                break;
            default:
                return null;
        }
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
