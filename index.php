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
 * File : index.php
 * Main catalogue page
 */

require_once('../../config.php');
require_once('lib.php');
global $DB, $OUTPUT, $PAGE, $USER;

// Check params.
$thislistname = required_param('name', PARAM_ALPHA);
$courseid = required_param('course', PARAM_INT);
$elementname = optional_param('element', '', PARAM_TEXT);
$edit = optional_param('edit', '', PARAM_ALPHA);
$newvalue = optional_param('newvalue', '', PARAM_TEXT);
$editing = optional_param('editing', 0, PARAM_INT);

$course = get_course($courseid);
require_login($course);
$sitecontext = context_system::instance();
$usereditor = has_capability('block/catalogue:edit', $sitecontext);
$thislist = block_catalogue_instanciate_list($thislistname);
$open = 'block';
$categories = $thislist->get_categories();
if (count($categories) > 1) {
    $open = 'none';
}
$availables = $thislist->get_availables();
?>

<script src="<?php echo $CFG->wwwroot; ?>/blocks/catalogue/js/block_catalogue.js"></script>

<?php
// Apply changes.
$usedcategory = null;
if ($edit && $elementname && $usereditor && confirm_sesskey() && data_submitted()) {
    block_catalogue_update_element($thislistname, $elementname, $edit, $newvalue);
    foreach ($categories as $category) {
        $categorymembers = $availables[$category];
        if (in_array($elementname, $categorymembers)) {
            $usedcategory = $category;
        }
    }
    reset($categories);
}

// Header code.
$args = array('name' => $thislistname, 'course' => $courseid);
$moodlefilename = '/blocks/catalogue/index.php';
$PAGE->set_url($moodlefilename, $args);
$thislistlocalname = $thislist->get_localname();
$PAGE->set_title($thislistlocalname);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading($thislistlocalname);

// Manager interface button.
if ($usereditor) {
    $editlink = $CFG->wwwroot.$moodlefilename.'?name='.$thislistname.'&course='.$courseid;
    if ($editing) {
        $editlink .= '&editing=0';
    } else {
        $editlink .= '&editing=1';
    }
    $editlabel = get_string("edit$editing", 'block_catalogue');
    $PAGE->set_button($OUTPUT->single_button($editlink, $editlabel));
    $usereditor = $editing;
}

// Page style.
$height = 255;
if ($usereditor) {
    $height += 90;
}
?>
<style>
.block_catalogue_categoryin {
    overflow:hidden; 
    position:relative
}
.block_catalogue_categoryout {
    float:left;
    height:<?php echo $height; ?>px;
    width:315px;
    padding-right:12px;
    overflow:hidden;
}
.block_catalogue_flipflop {
    text-align:center;
    width:100%;
    font-weight:bold;
    padding:5px;
    color:white;
    background-color:#7F7F7F;
    border-radius:5px 5px 0 0;
}
</style>
<?php

// Header with tabs.
echo $OUTPUT->header();
block_catalogue_display_tabs($courseid, $thislistname, $editing);

// Main content.
foreach ($categories as $category) {
    $categorylocalname = $thislist->langstring($category);
    ?>
    <br>
    <div onclick="flipflop('<?php echo "$categorylocalname"; ?>');" class='block_catalogue_flipflop'>
        <?php echo $categorylocalname; ?>
        <img src ="pix/open.png" alt="open" style="float: right"  height="15" width="15">
    </div>
    <?php
    if ($category == $usedcategory) {
        $display = 'block';
    } else {
        $display = $open;
    }
    echo "<div id ='$categorylocalname' style='width:100%;display:$display'><br>";
    echo "<div class='block_catalogue_categoryin'>";
    if ($availables[$category]) {
        if (!$editing) {
            echo '<div width="100%" style="text-align:center;font-weight:bold">';
            echo get_string('hover', 'block_catalogue');
            echo '</div>';
        }
        block_catalogue_display_category($course, $usereditor, $thislist, $availables[$category]);
    }
    echo '</div>';
    echo '</div>';
}

// Footer.
echo $OUTPUT->footer();
