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
 * File : list/enrols/blockcatalogue.list.php
 * Class definition for the enrols list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

class blockcatalogue_list_enrols extends blockcatalogue_list {
    public function __construct() {
        $this->name = 'enrols';
        $this->prefix = 'enrol';
        $this->color = '#0f3e66';
        $this->categories = array('users', 'methods');
        $this->potentialmembers = array();
        $this->potentialmembers['methods'] = array('enrol_instances', 'enrol_manual', 'enrol_self', 'local_cohortmanager',
                                                   'local_mass_enroll', 'blocks_enrol_demands');
        $this->defaultfavorites = array('user_index', 'local_cohortmanager');
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
        global $CFG, $COURSE;
        $coursecontext = context_course::instance($COURSE->id);
        foreach ($this->categories as $category) {
            $this->availables[$category] = array();
        }
        if (has_capability('moodle/course:viewparticipants', $coursecontext)) {
            $this->availables['users'][] = 'user_index'; // Participants.
        }
        if (has_capability('moodle/course:managegroups', $coursecontext)) {
            $this->availables['users'][] = 'group_index';
        }
        if (has_capability('moodle/course:enrolreview', $coursecontext)) {
            $this->availables['methods'][] = 'enrol_instances';
            $this->availables['users'][] = 'enrol_users';
            if (file_exists("$CFG->dirroot/enrol/vet.php")) {
                $this->availables['methods'][] = 'enrol_vet';
            }
        }
        $methods = array('manual', 'self');
        foreach ($methods as $method) {
            if (has_capability("enrol/$method:config", $coursecontext)) {
                $this->availables['methods'][] = "enrol_$method".'_edit';
            }
        }
        $localplugins = core_component::get_plugin_list('local');
        foreach ($localplugins as $name => $path) {
            if ($name == 'mass_enroll') {
                if (has_capability('moodle/role:assign', $coursecontext)) {
                    $this->availables['methods'][] = 'mass_enroll';
                }
            }
            if ($name == 'cohortmanager') {
                if (has_capability('moodle/role:assign', $coursecontext)) {
                    $this->availables['methods'][] = 'cohortmanager';
                }
            }
        }
        if (file_exists("$CFG->dirroot/group/copygroup.php")) {
            if (has_capability('moodle/course:managegroups', $coursecontext)) {
                $this->availables['methods'][] = 'group_copygroup';
            }
        }
        $blockplugins = core_component::get_plugin_list('block');
        foreach ($blockplugins as $name => $path) {
            if ($name == 'enrol_demands') {
                if (has_capability('moodle/role:assign', $coursecontext)) {
                    $this->availables['methods'][] = 'block_demands';
                }
            }
        }
        $reportplugins = core_component::get_plugin_list('report');
        foreach ($reportplugins as $name => $path) {
            $capability = "report/$name:view";
            if (!get_capability_info($capability)) {
                continue;
            }
            if (has_capability($capability, $coursecontext)) {
                if (($name == 'roster')||($name == 'exportlist')) {
                    $this->availables['users'][] = "report_$name";
                }
            }
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
        global $CFG;
        if ($nature != 'iconurl') {
            return null;
        }
        $nameparts = $this->divide_name($elementname);
        if (isset($nameparts[2])) {
            $nameparts[1] .= '_'.$nameparts[2];
        }
        $localicondir = $nameparts[0].'/'.$nameparts[1].'/pix';
        $iconurl = $this->get_local_iconurl($localicondir, $elementname);
        return $iconurl;
    }

    /**
     * Get the name of this element in the current language.
     * @param string $elementname
     * @return string
     */
    public function get_element_localname($elementname) {
        $nameparts = $this->divide_name($elementname);
        if (count($nameparts) == 3) {
            if (($nameparts[0] == 'enrol')&&($nameparts[2] == 'edit')) {
                return get_string('pluginname', 'enrol_'.$nameparts[1]);
            }
        }
        $localnames = array(
            'enrol_instances' => get_string('enrolmentinstances', 'enrol'),
            'enrol_users' => get_string('enrolledusers', 'enrol'),
            'enrol_vet' => 'Inscription par VET',
            'group_copygroup' => 'Importer des groupes UCP',
            'block_demands' => 'Inscriptions demandées',
            'user_index' => get_string('participants'),
            'group_index' => get_string('groups')
        );
        if (isset($localnames[$elementname])) {
            return $localnames[$elementname];
        }
        if ($elementname == 'mass_enroll') {
            return get_string('mass_enroll', 'local_mass_enroll');
        }
        if ($elementname == 'cohortmanager') {
            return get_string('pluginname', 'local_cohortmanager');
        }
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
        $args = array('id' => $COURSE->id, 'courseid' => $COURSE->id);
        $nameparts = $this->divide_name($elementname);
        $targetpage = $CFG->wwwroot;
        $goodprefix = array('enrol', 'group', 'user');
        if (in_array($nameparts[0], $goodprefix)) {
            if (isset($nameparts[2])) {
                $neweditinstance = ($nameparts[0] == 'enrol')
                                && ($CFG->branch > 30)
                                && ($nameparts[2] == 'edit');
            } else {
                $neweditinstance = false;
            }
            if ($neweditinstance) {
                $targetpage .= "/enrol/editinstance";
                unset ($args['id']);
                $args['type'] = $nameparts[1];
            } else {
                foreach ($nameparts as $namepart) {
                    $targetpage .= "/$namepart";
                    if ($namepart == 'edit') {
                        unset($args['id']);
                    }
                }
            }
            $targetpage .= '.php';
        } else if ($nameparts[0] == 'report') {
            $targetpage .= '/report/'.$nameparts[1].'/index.php';
        } else if ($elementname == 'local_cohortmanager') {
            $targetpage .= '/local/cohortmanager/viewinfo.php';
            $coursecontext = context_course::instance($COURSE->id);
            $args = array('contextid' => $coursecontext->id, 'origin' => 'course');
        } else if ($elementname == 'mass_enroll') {
            $targetpage .= '/local/mass_enroll/mass_enroll.php';
        } else if ($elementname == 'block_demands') {
            $targetpage .= '/blocks/enrol_demands/requests.php';
        }
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
