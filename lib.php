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
 * @copyright  Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : lib.php
 * PHP functions library for this block.
 */

/**
 * Get all the current favorites.
 * @return array of objects
 */
function block_catalogue_all_favorites($listnames) {
    $favorites = array();
    $lists = array();
    foreach ($listnames as $listname) {
		if ($listname == '') {
			$favorites[] = '<br>';
		}
        $list = block_catalogue_instanciate_list($listname);
        if ($list) {
            $lists[] = $list;
            $listcategories = $list->get_availables();
            $visibles = $list->visible_elements();
            if (count($visibles) == 1) {
                $favorite = new stdClass();
                $favorite->listname = $listname;
                $favorite->elementname = current($visibles);
                $favorites[] = $favorite;
            } else {
                $listfavorites = $list->get_favorites();
                foreach ($listfavorites as $listfavorite) {
                    $favorite = new stdClass();
                    $favorite->listname = $listname;
                    $favorite->elementname = $listfavorite;
                    $favorites[] = $favorite;
                }
            }
        }
    }
    $listsandfavorites = new stdClass();
    $listsandfavorites->lists = $lists;
    $listsandfavorites->favorites = $favorites;
    return $listsandfavorites;
}

/**
 * Moves newly created modules to their expected place in section sequences.
 * @global object $DB
 * @param object course
 */
function block_catalogue_check_sequences($course) {
	global $DB, $PAGE;
	$pagepath = $PAGE->url->get_path();
	if (($pagepath != '/course/modedit.php') && ($pagepath != '/blocks/catalogue/chooseplace.php')) {
		$sections = $DB->get_records('course_sections', array('course' => $course->id));
		$rebuild = false;
		foreach ($sections as $section) {
			$restorelastcm = false;
			if (false !== strpos($section->sequence, '-')) {
				// A new mod has just been added at the end of this section or its creation has just been canceled (see chooseplace.php).
				// The negative value's place in the sequence is where the user wanted to add this new mod. 
				$sequence = explode(',', $section->sequence);
				$sectionlastcmid = array_pop($sequence);
				if ($sectionlastcmid < 0) {
					// The new mod was to be added at the end of the section but its creation was cancelled.
					// All we have to do is to remove this negative value from the sequence (and array_pop() already did it).
				}
				$newsequence = '';
				foreach ($sequence as $cmid) {
					if ($cmid < 0) {
						// The new mod was to be added here.
						if ($cmid + $sectionlastcmid == 0) {
							// The last mod in this section is still the same it was before, which means the new mod creation has been cancelled.
							// This negative value must be removed and the last mod (removed by array_pop()) must be restored at the end of the section.
							$restorelastcm = true;
						} else {
							// The new mod has really been created. It's now at the end of the section but we should move it here.
							$rebuild = true;
							$newsequence .= "$sectionlastcmid,";
						}
					} else {
						//There's nothing to change here.
						$newsequence .= "$cmid,";
					}
				}
				if ($restorelastcm) {
					$section->sequence = $newsequence.$sectionlastcmid;
				} else {
					// Remove the last comma.
					$section->sequence = substr($newsequence, 0, -1);
				}
				$DB->update_record('course_sections', $section);
			}
		}

		if ($rebuild) {
			// We moved a mod, so we need to rebuild the course cache.
			rebuild_course_cache();
		}
	}
}

function block_catalogue_chooseplace_modicon($modulehtml, $cmid, $selectmodurl) {
	global $DB, $OUTPUT;

	$modulehtml = str_replace('<li', '<div', $modulehtml);
	$modulehtml = str_replace('</li>', '</div>', $modulehtml);
	$modulehtml = str_replace('<a', '<div', $modulehtml);
	$modulehtml = str_replace('</a>', '</div>', $modulehtml);
	$cm = $DB->get_record('course_modules', array('id' => $cmid));

	if (strpos($modulehtml, '<div class="contentwithoutlink ">')) {
		$module = $DB->get_record('modules', array('id' => $cm->module));
		$pixurl = $OUTPUT->pix_url('icon', "mod_$module->name");
		if ($module->name == 'customlabel') {
			$clabelslist = block_catalogue_instanciate_list('customlabels');
			if ($clabelslist) {
				$customlabel = $DB->get_record('customlabel', array('id' => $cm->instance));
				$pixurl = $clabelslist->get_element_data($customlabel->labelclass, 'iconurl');
			}
		}
		$modoutput = "<img src='$pixurl' width='30px' style='padding-top:15px'>";
	} else {
		$modoutput = $modulehtml;;
	}

	echo '<span style="padding-left:30px;float:left;margin-bottom:30px"> &nbsp; &nbsp; ';
	if ($selectmodurl) {
		echo '<a href="'.$selectmodurl.'">';
		echo '<button class="btn btn-secondary">';
	}
	if (!$cm->visible) {
		echo '<div style="color:gray">';
	}
	echo $modoutput;
	if (!$cm->visible) {
		echo '</div>';
	}
	if ($selectmodurl) {
		echo '</button>';
		echo '</a>';
	}
	echo '</span>';
}

/**
 * Displays all the elements of a given category on the index page.
 * @global object $DB
 * @param object $course
 * @param boolean $usereditor
 * @param object $list
 * @param array of strings $elementnames
 * @param int $maxperline
 */
function block_catalogue_display_category($course, $usereditor, $list, $elementnames, $maxperline) {
    global $DB;
    $listname = $list->get_name();
    if ($usereditor) {
        $elementclass = 'block_catalogue_editedelement';
    } else {
        $elementclass = 'block_catalogue_element';
    }
    $onthatline = 0; // No element yet on that line.
    foreach ($elementnames as $elementname) {
        $params = array('listname' => $listname, 'elementname' => $elementname);
        $hidden = $DB->get_record('block_catalogue_hide', $params);
        if ((!$hidden)||$usereditor) {
            echo "<div class='$elementclass'>";
            block_catalogue_display_element($course, $usereditor, $list, $elementname);
            echo '</div>';
			$onthatline++;
			if ($onthatline == $maxperline) {
				echo '<p style="margin-bottom:0"></p>';
				$onthatline = 0;
			}
        }
    }
}

/**
 * Displays one element on the index page.
 * @global object $DB
 * @param object $course
 * @param boolean $usereditor
 * @param object $list
 * @param string $elementname
 */
function block_catalogue_display_element($course, $usereditor, $list, $elementname) {
    global $DB;
    $listname = $list->get_name();
    $hidden = $list->get_hidden($elementname);
    $url = "index.php?name=$listname&course=$course->id";
    $list->flush_pluginfile();
    $description = $list->get_element_data($elementname, 'description');
    $link = $list->get_element_data($elementname, 'link');
    $iconurl = $list->get_element_data($elementname, 'iconurl');
    $list->flush_pluginfile();
    $localname = $list->get_element_localname($elementname);
    $useurl = $list->usage_url($elementname);
    $uselabel = get_string($listname.'_use', 'block_catalogue');

    echo '<table class="block_catalogue_elementtable">';
    echo '<tr class="block_catalogue_elementheader">';
    if ($hidden) {
        $titleclass = 'block_catalogue_hiddentitle';
    } else {
        $titleclass = 'block_catalogue_elementtitle';
    }

    echo "<td class='block_catalogue_iconcell'>";
    echo "<img src='$iconurl' class='block_catalogue_elementicon'>";
    echo '</td>';
    echo "<td class='$titleclass' colspan=2>";
    echo $localname;
    echo '</td>';
    echo '<td width="30px">';
    block_catalogue_toggler($list, $elementname, 'fav');
    echo '</td>';
    echo '</tr><tr class="block_catalogue_elementdescription">';
    echo "<td colspan='4' height='120px'>";
    block_catalogue_show_description($usereditor, $description, $url, $elementname);
    block_catalogue_show_link($link);
    echo '</td>';

    echo '</tr><tr class="block_catalogue_buttonline">';

    $colspan = 4;
    if ($usereditor) {
        $colspan--;
    }
    echo "<td colspan='$colspan' style='text-align:center'>";
    echo "<a href='$useurl'><button class='btn btn-secondary'>$uselabel</button></a>";
    echo '</td>';
    if ($usereditor) {
        echo "<td>";
        block_catalogue_toggler($list, $elementname, 'hide');
        echo '</td>';
    }
    echo '</tr></table>';
    if ($usereditor) {
        block_catalogue_link_editor($url, $elementname, $link);
    }
}

/**
 * Tabs to select a list at the top of the index page.
 * @global object $CFG
 * @global object $DB
 * @param int $courseid
 * @param string $thislistname
 */
function block_catalogue_display_tabs($courseid, $thislistname, $editing) {
    global $CFG, $DB;
    $listdir = "$CFG->wwwroot/blocks/catalogue/list";
    $params = array('plugin' => 'catalogue', 'name' => 'displayedlists');
    $dborder = $DB->get_field('config_plugins', 'value', $params);
    $sortorder = explode(',', $dborder);
    $listnames = block_catalogue_get_listnames($sortorder);
    $html = '';
    foreach ($listnames as $listname) {
        $list = block_catalogue_instanciate_list($listname);
        if ($list) {
            if (!$editing) {
                $visibles = $list->visible_elements();
                if (count($visibles) < 2) {
                    continue;
                }
            }
            $html .= "<div style='float:left;margin-right:30px'>";
            $target = $CFG->wwwroot.'/blocks/catalogue/index.php'."?name=$listname&course=$courseid&editing=$editing";
            $html .= "<a href = '$target'>";
            $html .= '<table><tr>';
            $html .= "<td class='block_catalogue_listtab' style='text-align:center'>";
            $html .= "<img src='$listdir/$listname/";
            if ($listname == $thislistname) {
				$html .= "catalogue_icon.png";
			} else {
				$html .= "shaded_icon.png";
			}
            $html .= "' class='block_catalogue_tabicon' width='40px' height='40px'>";
            $html .= "</td>";
            $html .= '</tr><tr>';
            if ($listname == $thislistname) {
                $listnameclass = 'block_catalogue_thislistname';
            } else {
                $listnameclass = 'block_catalogue_otherlistname';
            }
            $listlocalname = $list->get_localname();
            $listcolor = $list->get_color();
            $html .= "<td class='$listnameclass'>"."<a href='$target' style='color:$listcolor'>".$listlocalname.'</a></td>';
            $html .= '</tr></table>';
            $html .= '</a>';
            $html .= "</div>";
        }
    }
    //~ $html .= '</tr></table>';
    return $html;
}

/**
 * Displays the add/remove favorite icon or the hide/show one.
 * @global object $CFG
 * @param string $picture
 * @param string $label
 */
function block_catalogue_display_toggler($picture, $label) {
    global $CFG;
    echo "<img src='$CFG->wwwroot/blocks/catalogue/pix/$picture' title='$label' width='30px'><br>";
}

/**
 * Extracts the <h1>, <h2> and <h3> titles from a source string
 * and displays them as <h4>, <h5> and <h6>.
 * @param string $srcstring
 * @param int $titlelevel
 */
function block_catalogue_extract_titles($srcstring) {
    $supportedtags = array('<h1>', '<h1 ', '<h2>', '<h2 ', '<h3>', '<h3 ');
    $fromtag = strstr($srcstring, "<h");
    if ($fromtag) {
        $tag = substr($fromtag, 0, 4);
        if (in_array($tag, $supportedtags)) {
            $titlelevel = substr($tag, 2, 1);
            $fromendoftag = strstr($fromtag, '>');
            $aftertag = substr($fromendoftag, 1);
            $title = strstr($aftertag, "</h", true);
            if ($title) {
                $displaylevel = $titlelevel + 3;
                echo "<h$displaylevel>$title</h$displaylevel>";
            }
            $stringend = strstr($aftertag, "</h", false);
        } else {
            $stringend = substr($fromtag, 4);
        }
        block_catalogue_extract_titles($stringend);
    }
}

/**
 * Get the available lists for this catalogue and sort them.
 * @global object $CFG
 * @param array of strings $sortorder
 * @return array of strings
 */
function block_catalogue_get_listnames() {
    global $CFG;
    $displayedlists = get_config('catalogue', 'displayedlists');
    $sortorder = explode(',', $displayedlists);
    $listnames = array();
    $path = "$CFG->dirroot/blocks/catalogue/list";
    foreach ($sortorder as $listname) {
        if (file_exists("$path/$listname/blockcatalogue.list.php")) {
            $listnames[] = $listname;
        }
    }
    return $listnames;
}

/**
 * Creates an instance of this list's class
 * @global object $CFG
 * @param string $listname
 * @return object
 */
function block_catalogue_instanciate_list($listname) {
    global $CFG;
    $classfile = "$CFG->dirroot/blocks/catalogue/list/$listname/blockcatalogue.list.php";
    if (file_exists($classfile)) {
        include_once($classfile);
        $classname = "blockcatalogue_list_$listname";
        $instance = new $classname();
        if ($instance->get_skip()) {
            return null;
        }
        if ($instance->visible_elements()) {
            return $instance;
        }
    }
    return null;
}

/**
 * Called only if the current user can edit the documentation link.
 * Displays an editor for this link.
 * @param string $url
 * @param string $elementname
 * @param string $link
 */
function block_catalogue_link_editor($url, $elementname, $link) {
    ?>
    <form action='<?php  echo $url; ?>' method='post' enctype='multipart/form-data' style='text-align:center'>
        <input type='hidden' name='edit' value='link'>
        <input type='hidden' name='element' value='<?php echo $elementname; ?>'>
        <input type='hidden' name='sesskey' value='<?php echo sesskey(); ?>'>
        <input type='text' name='newvalue' value='<?php echo $link; ?>'>&nbsp;
        <input type='submit' value='OK'>
    </form>
    <?php
}

/**
 * Displays the main table in the small block.
 * @param array of strings $listnames
 * @param object $course
 * @param string $bgcolor
 * @param boolean $showtabs
 * @return string HTML code
 */
function block_catalogue_main_table($listnames, $course, $bgcolor, $showtabs) {
    global $OUTPUT;

    $listsandfavorites = block_catalogue_all_favorites($listnames);
    $lists = $listsandfavorites->lists;
    $favorites = $listsandfavorites->favorites;
    $iconstyle = "text-align:center;background-color:$bgcolor";
    $maintable = '<table width="100%" style="border-collapse:collapse"><tr>';
    $coursecontext = context_course::instance($course->id);
    $viewlists = has_capability("block/catalogue:viewlists", $coursecontext);

    if ($viewlists && $showtabs) {
        $nblists = count($listnames);
        $nbshownlists = 0;
        $rowtitles = array();
        foreach ($lists as $list) {
            $listcategories = $list->get_availables();
            $visibles = $list->visible_elements();
            if (count($visibles) > 1) {
                $maintable .= "<td style='$iconstyle'>".$list->main_table_icon($course).'</td>';
                $nbshownlists++;
                $column = $nbshownlists % 2;
                $rowtitles[$column] = $list->main_table_title($course);
                if (($column == 0) && ($nbshownlists < $nblists)) {
                    $maintable .= '</tr>';
                    foreach ($rowtitles as $rowtitle) {
                        $maintable .= "<td style='$iconstyle'>$rowtitle</td>";
                    }
                    if ($nbshownlists < $nblists) {
                        $rowtitles = array();
                        $maintable .= '<tr>';
                    }
                }
            }
        }
        $maintable .= '<tr>';
        foreach ($rowtitles as $rowtitle) {
            $maintable .= "<td style='$iconstyle'>$rowtitle</td>";
        }
        $maintable .= '</tr>';
        $maintable .= '<tr><td colspan=2> </td></tr>';
        $favtitle = get_string('favorites', 'block_catalogue');
        $favstyle = 'text-align:center;font-weight:bold';
        $helper = $OUTPUT->help_icon('favorites', 'block_catalogue');
        $maintable .= "<tr><td colspan=2 style='$favstyle'>$favtitle $helper</td></tr>";
    }

    $maintable .= '</table>';
    if ($favorites) {
        $maintable .= "<div id='block-catalogue-favorites'>";
        $maintable .= block_catalogue_show_favorites($favorites, $bgcolor);
        $maintable .= '</div>';
    } else if ($viewlists && has_capability("block/catalogue:togglefav", $coursecontext)) {
        $nofavs = get_string('nofavs', 'block_catalogue');
        $maintable .= "<p style='$iconstyle'>$nofavs</p>";
    }    
    return $maintable;
}

/**
 * Displays a section's table of content.
 * @param int $sectionid
 */
function block_catalogue_section_toc($sectionid) {
    global $DB;
    $section = $DB->get_record('course_sections', array('id' => $sectionid));
    block_catalogue_extract_titles($section->summary);
    $cmids = explode(',', $section->sequence);
    foreach ($cmids as $cmid) {
        $cm = $DB->get_record('course_modules', array('id' => $cmid));
        if ($cm) {
            $module = $DB->get_record('modules', array('id' => $cm->module));
            if ($module->name == 'customlabel') {
                $customlabel = $DB->get_record('customlabel', array('id' => $cm->instance));
                block_catalogue_extract_titles($customlabel->processedcontent);
            } else if ($module->name == 'label') {
                $label = $DB->get_record('label', array('id' => $cm->instance));
                block_catalogue_extract_titles($label->intro);
            }
        }
    }
}

/**
 * Displays a description for the given element ; in an editor if the current user can edit it.
 * @param boolean $usereditor
 * @param string $description
 * @param string $url
 * @param string $elementname
 */
function block_catalogue_show_description($usereditor, $description, $url, $elementname) {
    if ($usereditor) {
        ?>
        <form action='<?php  echo $url; ?>' method='post' enctype='multipart/form-data' style='text-align:center'>
            <input type='hidden' name='edit' value='description'>
            <input type='hidden' name='element' value='<?php echo $elementname; ?>'>
            <input type='hidden' name='sesskey' value='<?php echo sesskey(); ?>'>
            <textarea name='newvalue' rows='5'><?php echo $description; ?></textarea>
            <br>
            <input type='submit' value='OK'>
        </form>
        <?php
    } else {
        if (!$description) {
            $description = '&nbsp;';
        }
        echo '<div title="'.$description.'" style="text-align:left">';
        $maxlen = 150;
        echo substr($description, 0, $maxlen);
        if (strlen($description) > $maxlen) {
            echo '...';
        } else if (strlen($description) < ($maxlen - 50)) {
            echo '<br><br>';
        }
        echo "</div>";
    }
}

/**
 * Show all the current user's favorites in the small block.
 * @param array of objects $favorites
 * @return string HTML code
 */
function block_catalogue_show_favorites($favorites, $bgcolor) {
    $nbfavs = count($favorites);
    $nbshownfavs = 0;
    $favlists = array();
    $favstring = '<table width="100%">';
    $style = "max-width:50px;text-align:center;background-color:$bgcolor";
    $nbcolumns = 3;
    foreach ($favorites as $favorite) {
		if ($favorite === '<br>') {
			print_object($favorites);
			$favstring .= '</tr><tr><td></td></tr><tr>';
		}
        if (!isset($favlists[$favorite->listname])) {
            $favlists[$favorite->listname] = block_catalogue_instanciate_list($favorite->listname);
        }
        if (!$favlists[$favorite->listname]->favorite_here($favorite->elementname)) {
            continue;
        }
        if ($nbshownfavs % $nbcolumns == 0) {
            $favstring .= "<tr>";
        }
        $url = $favlists[$favorite->listname]->usage_url($favorite->elementname);
        $favstring .= "<td style='$style'><a href='$url'>";
        $favstring .= $favlists[$favorite->listname]->display_favorite($favorite->elementname);
        $favstring .= "</a></td>";
        $nbshownfavs++;
        if ($nbshownfavs % $nbcolumns == 0) {
            $favstring .= "</tr>";
        }
    }
    if ($nbfavs % $nbcolumns) {
        while ($nbfavs % $nbcolumns) {
            $favstring .= "<td style='$style'></td>";
            $nbfavs++;
        }
        $favstring .= "</tr>";
    }
    $favstring .= '</table>';
    return $favstring;
}

/**
 * Displays a documentation link.
 * @param string $link
 */
function block_catalogue_show_link($link) {
    $control = substr($link, 0, 4);
    echo  "<div style='text-align:right;font-size:12px'>";
    if ($link && ($control == 'http')) {
        echo "<a href='$link' target='_blanck'>";
        echo get_string('doc', 'block_catalogue');
        echo '</a>';
    }
    echo '</div>';
}

/**
 * Another way to display the favorites. Used by theme_catalogue.
 * @return string HTML code
 */
function block_catalogue_theme_favorites() {
    $favtitle = get_string('favorites', 'block_catalogue');
    //~ $helper = $OUTPUT->help_icon('favorites', 'block_catalogue');
    //~ echo  "<span style='font-weight:bold'>$favtitle :</span> &nbsp; ";
    $html = '';
	$listnames = block_catalogue_get_listnames();
	$listsandfavorites = block_catalogue_all_favorites($listnames);
	$favorites = $listsandfavorites->favorites;
	$favlists = array();
	$favstyle = "max-width:50px;text-align:center;margin-right:15px";
	print_object($favorites);
	foreach ($favorites as $favorite) {
		if (!isset($favlists[$favorite->listname])) {
			$favlists[$favorite->listname] = block_catalogue_instanciate_list($favorite->listname);
		}
		if (!$favlists[$favorite->listname]->favorite_here($favorite->elementname)) {				
			continue;
		}
		$url = $favlists[$favorite->listname]->usage_url($favorite->elementname);
		$html .= "<a href='$url' style='$favstyle'>";
		$html .= $favlists[$favorite->listname]->display_favorite($favorite->elementname);
		$html .= "</a>";
	}	
	return $html;
}

/**
 * Prepares display of the add/remove favorite icon or the hide/show button on the index page,
 * then calls block_catalogue_display_toggler() to actually display it.
 * @global object $CFG
 * @global object $COURSE
 * @global object $DB
 * @global object $USER
 * @param object $list
 * @param string $elementname
 * @param string $toggler 'fav' ou 'hide'
 */
function block_catalogue_toggler($list, $elementname, $toggler) {
    global $CFG, $COURSE, $DB, $USER;
    $coursecontext = context_course::instance($COURSE->id);
    $permission = has_capability("block/catalogue:toggle$toggler", $coursecontext);
    if ($permission) {
        $listname = $list->get_name();
        $params = array('listname' => $listname, 'elementname' => $elementname);
        $default = 0;
        if ($toggler == 'fav') {
            $params['userid'] = $USER->id;
            $defaultfavorites = $list->get_default_favorites();
            if (in_array($elementname, $defaultfavorites)) {
                $default = 1;
            }
            $favorites = $list->get_favorites();
            $recorded = in_array($elementname, $favorites);
        } else {
            $recorded = $DB->get_record("block_catalogue_$toggler", $params);
        }
        if ($recorded) {
            $picture = "off_$toggler.png";
            $label = get_string("off_$toggler", 'block_catalogue');
        } else {
            $picture = "on_$toggler.png";
            $label = get_string("on_$toggler", 'block_catalogue');
        }
        echo '<div style="text-align:center">';
        echo "<div id='$toggler"."tog-$elementname' "
                . "onclick='javascript:toggle(".'"'.$listname.'", "'.$elementname.'", "'
                .$toggler.'", "'.$COURSE->id.'", "'.$default.'", "'.$CFG->wwwroot.'/blocks/catalogue/toggle.php"'.")'>";
        block_catalogue_display_toggler($picture, $label);
        echo '</div>';
        echo '</div>';
    }
}

/**
 * Updates a data for an element in the database.
 *
 * @global object $CFG
 * @global object $DB
 * @param string $listname
 * @param string $elementname
 * @param string $nature What kind of data is the edited one ?
 * @param string $newvalue
 */
function block_catalogue_update_element($listname, $elementname, $nature, $newvalue) {
    global $CFG, $DB;
    $table = 'block_catalogue_data';
    $params = array('listname' => $listname, 'elementname' => $elementname, 'lang' => $CFG->lang, 'nature' => $nature);
    $currentdata = $DB->get_record($table, $params);
    if ($currentdata) {
        if (!$newvalue || ($newvalue == '')) {
            $DB->delete_records($table, $params);
        } else if ($currentdata->data != $newvalue) {
            $DB->set_field($table, 'data', $newvalue, $params);
        }
    } else if ($newvalue) {
        $newdata = new stdClass();
        $newdata->listname = $listname;
        $newdata->elementname = $elementname;
        $newdata->lang = $CFG->lang;
        $newdata->nature = $nature;
        $newdata->data = $newvalue;
        $newdata->id = $DB->insert_record($table, $newdata);
    }
}

?>
