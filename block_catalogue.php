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
 * File : block_catalogue.php
 * Block class definition
 */

defined('MOODLE_INTERNAL') || die();

$blockdir = $CFG->dirroot.'/blocks/catalogue';

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/format/lib.php');
require_once($CFG->libdir.'/blocklib.php' );
require_once("$blockdir/lib.php");

class block_catalogue extends block_base {

    public function init() {
        global $DB;
        $this->title = get_string('pluginname', 'block_catalogue');
        $this->clabelsenabled = $DB->record_exists('modules', array('name' => 'customlabel'));
    }

    public function specialization() {
        $this->title = get_config('catalogue', 'maintitle');
    }

    public function applicable_formats() {
        return array('site-index' => true, 'course-view' => true);
    }

    public function get_content() {
        global $PAGE;
        $pagetype = explode('-', $PAGE->pagetype);
        if (($pagetype[0] != 'course')||($pagetype[1] != 'view')) {
            return '';
        }
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        if (empty($this->instance)) {
            return $this->content;
        }
        $format = course_get_format($this->page->course);
        $course = $format->get_course();
        $displayedlists = get_config('catalogue', 'displayedlists');
        $bgcolor = get_config('catalogue', 'bgcolor');
        $sortorder = explode(',', $displayedlists);
        $listnames = block_catalogue_get_listnames($sortorder);
        $coursecontext = context_course::instance($course->id);
        $canview = has_capability('block/catalogue:view', $coursecontext);
        if ($listnames && $canview) {
            $this->content->text = block_catalogue_main_table($listnames, $course, $bgcolor);
        } else {
            $this->content->text = '';
        }
        return $this->content;
    }

    public function has_config() {
        return true;
    }
}
