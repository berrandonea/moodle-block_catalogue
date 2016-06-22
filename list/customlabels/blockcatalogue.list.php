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
 * File : list/customlabels/blockcatalogue.list.php
 * Class definition for the customlabels list.
 */

require_once($CFG->dirroot."/blocks/catalogue/list/list.class.php");
$customlabellib = "$CFG->dirroot/mod/customlabel/locallib.php";
if (file_exists($customlabellib)) {
    require_once($customlabellib);
}

class blockcatalogue_list_customlabels extends blockcatalogue_list {
    public function __construct() {
        global $CFG;
        $hascustomlabels = file_exists("$CFG->dirroot/mod/customlabel/locallib.php");
        $this->name = 'customlabels';
        if (!$hascustomlabels) {
            $this->skip = true;
        }
        $this->prefix = 'customlabeltype';
        $this->categories = array('pedagogic', 'structure', 'other');
        $this->potentialmembers = array();
        foreach ($this->categories as $category) {
            $this->potentialmembers[$category] = array();
        }
        $this->defaultfavorites = array('definition', 'keypoints', 'worktodo');
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
        $permitted = has_capability("mod/customlabel:addinstance", $coursecontext);
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
        global $COURSE;
        $coursecontext = context_course::instance($COURSE->id);
        $enabledcustomlabels = customlabel_get_classes($coursecontext);
        foreach ($this->categories as $category) {
            $this->availables[$category] = array();
        }
        foreach ($enabledcustomlabels as $enabledcustomlabel) {
            $this->sortout($enabledcustomlabel);
        }
        foreach ($this->categories as $category) {
            $this->sort_by_localname($category);
        }
        return true;
    }

    /**
     * Get the name of this element in the current language.
     * @param string $elementname
     * @return string
     */
    public function get_element_localname($elementname) {
        $localname = get_string('typename', "customlabeltype_$elementname");
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
        global $CFG;
        switch ($nature) {
            case 'description' :
                $localname = $this->get_element_localname($elementname);
                $lastcharacter = substr($localname, -1);
                if ($lastcharacter == 's' || $lastcharacter == 'x') {
                    $description = $this->langstring('help_plural');
                } else {
                    $description = $this->langstring('help_singular');
                }
                $description .= " ".$this->get_element_localname($elementname);
                $description .= " ".$this->langstring('inyourcourse');
                return $description;

            case 'link' :
                $link = $this->langstring('doclink');
                return $link;

            case 'iconurl' :
                $localicondir = "mod/customlabel/type/$elementname";
                $iconurl = $this->get_first_icon($localicondir);
                if ($iconurl) {
                    return $iconurl;
                }
                $iconurl = $this->get_local_iconurl(null, $elementname);
                return $iconurl;

            default :
                return null;
        }
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
        $args = array('list' => $this->name, 'course' => $COURSE->id, 'mod' => 'customlabel', 'type' => $elementname);
        $url = new moodle_url($targetpage, $args);
        return $url;
    }
}
