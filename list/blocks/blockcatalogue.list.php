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
 * File : list/blocks/blockcatalogue.list.php
 * Class definition for the activities and resources list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");

class blockcatalogue_list_blocks extends blockcatalogue_list {

    public function __construct() {
        $this->name = 'blocks';
        $this->prefix = 'block';
        $this->categories = array('monitor', 'communicate', 'other');
        $this->potentialmembers = array(
            'monitor' => array('progress', 'badges', 'selfcompletion', 'xp', 'completionstatus', 'grade_me',
                               'analytics_graphs', 'ranking', 'report_certificates', 'gismo', 'engagement', 'mentees',
                               'participants', 'autoattend', 'configurable_reports', 'dedication'),
            'communicate' => array('online_users', 'news_items', 'calendar_upcoming', 'messages', 'search_forums',
                                   'recent_activity', 'comments', 'rate_course', 'ucpslotbooking')
        );
        $this->open = false;
    }

    /**
     * Adds an instance of the selected block to the course main page.
     * @global object $DB
     * @param string $elementname
     * @param int $courseid
     * @param string $region
     */
    public function add_block($elementname, $courseid, $region) {
        global $DB;
        $params = array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $courseid);
        $coursecontext = $DB->get_record('context', $params);
        $blockinstance = new stdClass();
        $blockinstance->blockname = $elementname;
        $blockinstance->parentcontextid = $coursecontext->id;
        $blockinstance->showinsubcontexts = 0;
        $blockinstance->pagetypepattern = '*';
        $blockinstance->defaultregion = $region;
        $blockinstance->defaultweight = 0;
        $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);
    }

    /**
     * Can this user add this element in this course ?
     * @global object $COURSE
     * @param string $elementname
     * @return boolean
     */
    public function can_add($elementname) {
        global $PAGE;
        $permitted = has_capability("block/$elementname:addinstance", $PAGE->context);
        return $permitted;
    }

    /**
     * Can this favorite be used here ?
     * @param string $blockname
     * @return boolean
     */
    public function favorite_here($blockname) {
        $bi = block_instance($blockname);
        $allowmultiple = $bi->instance_allow_multiple();
        $present = $this->is_present($blockname);
        if ($allowmultiple || !$present) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Finds the elements available (to this user in this course) for the
     * current list, sorted out by category.
     * @return array of arrays of strings
     *
     * @global object $COURSE
     * @global object $PAGE
     * @return boolean
     */
    public function fill_availables() {
        global $COURSE, $PAGE;
        $availableblocks = array();
        $allblocks = blocks_get_record();
        $unaddableblocks = block_manager::get_undeletable_block_types();
        $pageformat = "course-view-$COURSE->format";
        foreach ($allblocks as $block) {
            if (!$bi = block_instance($block->name)) {
                continue;
            }
            $present = $this->is_present($block->name);
            if ($block->visible && !in_array($block->name, $unaddableblocks) &&
                    ($bi->instance_allow_multiple() || !$present) &&
                    blocks_name_allowed_in_format($block->name, $pageformat) &&
                    $bi->user_can_addto($PAGE)) {
                $availableblocks[$block->name] = $block;
            }
        }
        foreach ($this->categories as $category) {
            $this->availables[$category] = array();
        }
        foreach ($availableblocks as $availableblock) {
            $this->sortout($availableblock->name);
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
    public function get_local_data($blockname, $nature) {
        global $CFG;
        switch ($nature) {
            case 'link' :
                $cataloguelink = get_string($this->name."_link_$blockname", 'block_catalogue');
                $control = substr($cataloguelink, 0, 2);
                if ($cataloguelink && ($control != '[[')) {
                    $fulllink = "$this->standarddocdir/$CFG->branch/$cataloguelink";
                    return $fulllink;
                }
                return null;

            case 'iconurl' :
                $localicondir = "blocks/$blockname/pix";
                $iconurl = $this->get_local_iconurl($localicondir, $blockname);
                return $iconurl;

            default :
                return null;
        }
    }

    /**
     * Is this block already present on the course page ?
     * @global object $COURSE
     * @global object $DB
     * @param string $blockname
     * @return boolean
     */
    private function is_present($blockname) {
        global $COURSE, $DB;
        $coursecontext = $DB->get_record('context', array('contextlevel' => CONTEXT_COURSE, 'instanceid' => $COURSE->id));
        $courseblockinstance = $DB->get_record('block_instances',
                array('blockname' => $blockname, 'parentcontextid' => $coursecontext->id));
        if ($courseblockinstance) {
            return true;
        }
        $parentcontextids = explode('/', $coursecontext->path);
        foreach ($parentcontextids as $parentcontextid) {
            if (!is_int($parentcontextid)) {
                continue;
            }
            $params = array('blockname' => $blockname,
                            'parentcontextid' => $parentcontextid,
                            'showninsubcontexts' => 1);
            $parentblockinstance = $DB->get_record('block_instances', $params);
            if ($parentblockinstance) {
                return true;
            }
        }
        return false;
    }

    /**
     * Chooses a category for an element of this list
     * @global object $CFG
     * @param string $elementname
     * @param object $coursecontext
     * @return boolean
     */
    public function sortout($elementname) {
        global $CFG;
        $common = $this->common_sortout($elementname);
        if ($common) {
            return true;
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
        $args = array('list' => $this->name, 'course' => $COURSE->id, 'block' => $elementname);
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
