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
 * File : list/list.class.php
 * Abstract mother class for all the catalogue's lists.
 */

/**
 * Abstract mother class for all the catalogue's lists.
 *
 * The catalogue contains several lists.
 * Each list contains a few categories.
 * Each category contains items (also called "elements")
 *
 * @copyright 2016 Brice Errandonea <brice.errandonea@u-cergy.fr>, Salma El-mrabah <salma.el-mrabah@u-cergy.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @abstract
 */
abstract class blockcatalogue_list {

    /** @var boolean If true, this list will be skipped. */
    protected $skip = false;

    /** @var string The name of this list (blocks, reports, ...) */
    protected $name;

    /** @var string Common prefix for the list's items names (if they have one) */
    protected $prefix;

    /** @var string Standard remote url to check for documentation. */
    protected $standarddocdir = 'https://docs.moodle.org';

    /** @var string Remote url to check for documentation about third-party plugins. */
    protected $plugindocdir = 'https://moodle.org/plugins';

    /** @var string The content of a remote documentation file about a plugin. */
    protected $pluginfile;

    /** @var array of strings Names of categories for this list's items. */
    protected $categories;

    /** @var array of strings Names of the available items. */
    protected $availables = array();

    /** @var array of arrays of strings Names of elements that will be placed in each category if they are availables. */
    protected $potentialmembers;

    /** @var array of strings Names of a few elements that be marked as favorites by default. */
    protected $defaultfavorites = array();

    /** @var boolean If true, this list's categories will be displayed open on page load. */
    protected $open = true;

    /** @var string The main color for this list. */
    protected $color = '#FFFFFF';

    /**
     * If an element's name is mentionned in his list 'potentialmembers' table,
     * sorts this element out in the associate category and returns true.
     * Returns false if the element can't be sorted out that way.
     * @param string $elementname
     * @return boolean
     */
    public function common_sortout($elementname) {
        foreach ($this->potentialmembers as $category => $potentialmembers) {
            if (in_array($elementname, $potentialmembers)) {
                $this->availables[$category][] = $elementname;
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the available elements that are not hidden by the manager, regardless of their categories.
     * @return array of strings
     */
    public function visible_elements() {
        if (!$this->availables) {
            $this->fill_availables();
        }
        $visibles = array();
        foreach ($this->availables as $elementnames) {
            foreach ($elementnames as $elementname) {
                if (!$this->get_hidden($elementname)) {
                    $visibles[] = $elementname;
                }
            }
        }
        return $visibles;
    }

    /**
     * Do we have to skip this list ?
     * @return boolean
     */
    public function get_skip() {
        return $this->skip;
    }

    /**
     * If true, the categories of this list will be shown open by default
     * @return boolean
     */
    public function get_open() {
        return $this->open;
    }

    /**
     * Displays the list's active icon in the small block.
     * @global object $CFG
     * @param object $course
     * @return string HTML code
     */
    public function main_table_icon($course) {
        global $CFG;
        $url = $this->index_url($course);
        $picturefile = "$CFG->wwwroot/blocks/catalogue/list/$this->name/catalogue_icon.png";
        $text = "<a href = '$url'>";
        $text .= "<img src='$picturefile' height='31px' width='35px'>";
        $text .= '</a>';
        return $text;
    }

    /**
     * Displays the list's local name in the small block.
     * @global object $CFG
     * @param object $course
     * @return string HTML code
     */
    public function main_table_title($course) {
        $url = $this->index_url($course);
        $label = $this->get_localname();
        $color = $this->color;
        $text = "<a href='$url' style='color:$color'>";
        $text .= $label;
        $text .= '</a>';
        return $text;
    }

    /**
     * Builds the URL for the catalogue's index page in this course.
     * @param object $course
     * @return object $url
     */
    public function index_url($course) {
        $args = array('name' => $this->name, 'course' => $course->id);
        $url = new moodle_url('/blocks/catalogue/index.php', $args);
        return $url;
    }

    /**
     * Displays the active icon of a given favorite in the small block.
     * @param string $elementname
     * @return string HTML code
     */
    public function display_favorite($elementname) {
        global $COURSE;
        $coursecontext = context_course::instance($COURSE->id);
        $title = '';
        if (has_capability('block/catalogue:viewlists', $coursecontext)) {
            $title .= $this->get_localname().' : ';
        }
        $title .= $this->get_element_localname($elementname);
        $iconurl = $this->get_element_data($elementname, 'iconurl');
        $listcolor = $this->get_color();
        $favstring = "<img style='border-left: 4px solid $listcolor' src='$iconurl' ".'title="'.$title.'" width="35px">';
        return $favstring;
    }

    /**
     * Display all elements of the list as small buttons.
     * @param string $elementname
     */
    public function display_all_buttons($currentelementname) {
        echo '<div style="position:fixed;right:100px;top:300px">';
        $categories = $this->get_categories();
        $availables = $this->get_availables();
        echo '<table>';
        foreach ($categories as $category) {
  	    echo '<tr>';
	    foreach ($availables[$category] as $elementname) {
	        $localname = $this->get_element_localname($elementname);
	        $iconurl = $this->get_element_data($elementname, 'iconurl');
	        $usageurl = $this->usage_url($elementname);
		if ($elementname == $currentelementname) {
		    $border = 'border:2px solid red';
		} else {
		    $border = '';
		}
	        echo "<td style='margin-right:5px'>"."<a href='$usageurl' title='$localname'>"."<button style='$border'>";
	        echo "<img width='30px' height='30px' src='$iconurl' alt='$localname'>";
	        echo '</button>'."</a>".'</td>';
	    }
	    echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
    }

    /**
     * Tells whether this favorite can be used in the current course.
     * Overriden in subclasses when necessary.
     * @param string $elementname
     * @return boolean
     */
    public function favorite_here($elementname) {
        return true;
    }

    /**
     * Empty the content of attribute 'pluginfile' for this list.
     */
    public function flush_pluginfile() {
        $this->pluginfile = '';
    }

    /**
     * Get the elements available (to this user in this course) for the
     * current list, sorted out by category.
     * @return array of arrays of strings
     */
    public function get_availables() {
        if (!$this->availables) {
            $this->fill_availables();
        }
        return $this->availables;
    }

    /**
     * Get a data from this list's lang directory.
     * @param string $elementname
     * @param string $nature
     * @return string
     */
    public function get_lang_data($elementname, $nature) {
        global $CFG;        
		$cataloguestringname = $this->name.'_'.$nature.'_'.$elementname;
        if (get_string_manager()->string_exists($cataloguestringname, 'block_catalogue')) {
			$cataloguestring = get_string($cataloguestringname, 'block_catalogue');
		} else {
			return null;
		}        
        if (($nature == 'link') && (substr($cataloguestring, 0, 4) != 'http')) {
            $fulllink = "$this->standarddocdir/$CFG->branch/$cataloguestring";
            return $fulllink;
        }
        return $cataloguestring;
    }

    /**
     * Get all the category names for this list
     * @return array of strings
     */
    public function get_categories() {
        return $this->categories;
    }

    /**
     * Get a data stored in the database for the given element.
     * @global object $DB
     * @param string $elementname
     * @param string $nature
     * @return string
     */
    public function get_db_data($elementname, $nature) {
        global $DB;
        $lang = current_language();
        $params = array('listname' => $this->name,
                        'elementname' => $elementname,
                        'nature' => $nature,
                        'lang' => $lang);
        $dblangrecord = $DB->get_record('block_catalogue_data', $params);
        if ($dblangrecord) {
            return $dblangrecord->data;
        }
        $params['lang'] = 'en';
        $dbrecord = $DB->get_record('block_catalogue_data', $params);
        if ($dbrecord) {
            return $dbrecord->data;
        }
        return null;
    }

    /**
     * Returns names of the default favorites for this list.
     * @return array of strings
     */
    public function get_default_favorites() {
        return $this->defaultfavorites;
    }

    /**
     * Get the requested data for the given element.
     * @global object $CFG
     * @param string $elementname
     * @param string $nature ('description', 'link' or 'iconurl')
     * @return string
     */
    public function get_element_data($elementname, $nature) {
        global $CFG, $DB;
        $dbdata = $this->get_db_data($elementname, $nature);
        if ($dbdata) {
            return $dbdata;
        }
        $cataloguedata = $this->get_lang_data($elementname, $nature);
        if ($cataloguedata) {
            return $cataloguedata;
        }
        $localdata = $this->get_local_data($elementname, $nature);
        if ($localdata) {
            return $localdata;
        }
        $params = array('plugin' => 'catalogue', 'name' => 'getremotedata');
        $getremotedata = $DB->get_field('config_plugins', 'value', $params);
        if ($getremotedata) {
            $remotedata = $this->get_remote_data($elementname, $nature);
            if ($remotedata) {
                $this->memorize_data($elementname, $nature, 'en', $remotedata);
                return $remotedata;
            }
        }
        if ($nature == 'iconurl') {
            return "$CFG->wwwroot/blocks/catalogue/list/moodle.PNG";
        }
        return null;
    }

    /**
     * Get the name of this element in the current language.
     * Some children override it.
     * @param string $elementname
     * @return string
     */
    public function get_element_localname($elementname) {
        return get_string('pluginname', $this->prefix.'_'.$elementname);
    }

    /**
     * Get names of this user's favorite elements in this list.
     * An element is favorite if it's present in table 'block_catalogue_fav'
     * or in defaultfavorites but not in both.
     * @global object $DB
     * @global object $USER
     * @return array of strings
     */
    public function get_favorites() {
        global $COURSE, $DB, $USER;

        $favorites = array();
        $coursecontext = context_course::instance($COURSE->id);

        if (has_capability("block/catalogue:viewlists", $coursecontext)) {
            $defaultfavorites = $this->defaultfavorites;
            $table = 'block_catalogue_fav';
            $params = array('userid' => $USER->id, 'listname' => $this->name);
            foreach ($defaultfavorites as $defaultfavorite) {
                $params['elementname'] = $defaultfavorite;
                if (!$DB->record_exists($table, $params)) {
                    $favorites[] = $defaultfavorite;
                }
            }
            unset($params['elementname']);
            $bddfavorites = $DB->get_records($table, $params);
            foreach ($bddfavorites as $bddfavorite) {
                if (!in_array($bddfavorite->elementname, $defaultfavorites)) {
                    $favorites[] = $bddfavorite->elementname;
                }
            }
        } else {
            // If the user can't view the lists, all the elements availables for him are favorites.
            foreach ($this->availables as $categoryelements) {
                foreach ($categoryelements as $elementname) {
                    $favorites[] = $elementname;
                }
            }
        }
        return $favorites;
    }

    /**
     * Is this element hidden by the site manager ?
     * @param string $elementname
     * @return boolean
     */
    public function get_hidden($elementname) {
        global $DB;
        $params = array('listname' => $this->name, 'elementname' => $elementname);
        $hidden = $DB->get_record('block_catalogue_hide', $params);
        return $hidden;
    }

    /**
     * Searches the element's code directory for an icon.
     * @global object $CFG
     * @param string $localicondir
     * @param string $elementname
     * @return string URL of the found icon (or null if not found)
     */
    public function get_local_iconurl($localicondir, $elementname) {
        global $CFG;
        if ($localicondir) {
            $formats = array('svg', 'png', 'gif');
            foreach ($formats as $format) {
                if (file_exists("$CFG->dirroot/$localicondir/icon.$format")) {
                    return "$CFG->wwwroot/$localicondir/icon.$format";
                }
            }
        }
        $catalogueicon = "blocks/catalogue/list/$this->name/icons/$elementname.png";
        if (file_exists("$CFG->dirroot/$catalogueicon")) {
            return "$CFG->wwwroot/$catalogueicon";
        }
        return null;
    }

    /**
     * Get this list's name in the current language.
     * @return string
     */
    public function get_localname() {
        $localname = get_string($this->name.'_listname', 'block_catalogue');
        return $localname;
    }
    
    /**
     * Page to edit a mod from this list (doesn't apply if it's not a mod list).
     * @return string
     */
    public function get_modedit() {
		return 'course/modedit.php';
	}

    /**
     * Get this list's technical name.
     * @return string
     */
    public function get_name() {
        return $this->name;
    }
    
    /**
     * Get the main color for this list.
     * @return string
     */
    public function get_color() {
		return $this->color;
	}

    /**
     * Get the prefix for this list. Can be overriden in subclasses, where it can depend on elementname.
     * @param string $elementname
     * @return string
     */
    public function get_prefix($elementname) {
        return $this->prefix;
    }

    /**
     * Searches the Moodle plugins directory for data about this element.
     * @param string $elementname
     * @param string $nature
     * @return string
     */
    public function get_remote_data($elementname, $nature) {
        if (strlen($this->pluginfile)) {
            $filecontent = $this->pluginfile;
        } else {
            $prefix = $this->get_prefix($elementname);
            if ($prefix) {
                $filename = "$this->plugindocdir/$prefix".'_'.$elementname;
            } else {
                $filename = "$this->plugindocdir/$elementname";
            }
            $fileheaders = get_headers($filename);
            if (strpos($fileheaders[0], '200')) {
				$filecontent = file_get_contents($filename);
				if (!$filecontent || strpos($filecontent, "<title>Plugin not found</title>")) {
					$filecontent = '';
				}
			} else {
				$filecontent = '';
			}            
            $this->pluginfile = $filecontent;
        }
        if ($filecontent) {
            $headdelimiter = array('description' => '<div class="shortdescription">',
                                   'link' => 'class="li documentationurl">'
                                           . '<a onclick="this.target=&quot;_blank&quot;" class="external" href="',
                                   'iconurl' => '<div class="plugin-logo"><img src="');

            $feetdelimiter = array('description' => '</div>',
                                   'link' => '">',
                                   'iconurl' => '"');

            $crophead = explode($headdelimiter[$nature], $filecontent);
            if (!isset($crophead[1])) {
                return null;
            }
            $cropfeet = explode($feetdelimiter[$nature], $crophead[1]);
            if (!isset($cropfeet[0])) {
                return null;
            }
            $remotedata = $cropfeet[0];
            return $remotedata;
        }
        return null;
    }

    /**
     * Stores a data found in the Moodle plugins directory in the local database, for quicker access in the future.
     * @global object $DB
     * @param string $elementname
     * @param string $nature
     * @param string $lang
     * @param string $value
     * Doesn't return anything.
     */
    public function memorize_data($elementname, $nature, $lang, $value) {
        global $DB;
        $table = 'block_catalogue_data';
        $params = array('listname' => $this->name, 'elementname' => $elementname, 'nature' => $nature, 'lang' => $lang);
        $dbrecord = $DB->get_record($table, $params);
        if ($dbrecord) {
            if ($dbrecord->data != $value) {
                $DB->set_field($table, $nature, $value, array('id' => $dbrecord->id));
            }
        } else {
            $newdata = new stdClass();
            $newdata->listname = $this->name;
            $newdata->elementname = $elementname;
            $newdata->lang = $lang;
            $newdata->nature = $nature;
            $newdata->data = $value;
            $newdata->id = $DB->insert_record($table, $newdata);
        }
    }

    /**
     * Sorts the elements of a given category by their names in the current language.
     * @param string $category
     * Doesn't return anything.
     */
    public function sort_by_localname($category) {
        $localnames = array();
        foreach ($this->availables[$category] as $elementname) {
            $localname = $this->get_element_localname($elementname);
            $localnames[$localname] = $elementname;
        }
        ksort($localnames);
        $this->availables[$category] = $localnames;
    }

    /**
     * Display tab for this list on top of a page.
     * @param
     */
    public function tab($selectedlistname, $editing) {
	    global $CFG, $COURSE;
	    $listdir = "$CFG->wwwroot/blocks/catalogue/list";
	    $html = "<div style='float:left;margin-right:30px'>";
        $target = $CFG->wwwroot.'/blocks/catalogue/index.php'.
            "?name=$this->name&course=$COURSE->id&editing=$editing";
        $html .= "<a href = '$target'>";
        $html .= '<table><tr>';
        $html .= "<td class='block_catalogue_listtab' style='text-align:center'>";
	    if (!$selectedlistname || $this->name == $selectedlistname) {
		    $opacity = 1;
	    } else {
		    $opacity = 0.5;
	    }
	    $html .= "<img src='$listdir/$this->name/catalogue_icon.png' style='opacity:$opacity'";            
        $html .= "' class='block_catalogue_tabicon' width='40px' height='40px'>";
        $html .= "</td>";
        $html .= '</tr><tr>';
        if ($this->name == $selectedlistname) {
            $listnameclass = 'block_catalogue_thislistname';
        } else {
            $listnameclass = 'block_catalogue_otherlistname';
        }
        $listlocalname = $this->get_localname();
        if (strlen($listlocalname) > 12) {
	        $nameparts = explode(' ', $listlocalname);
		    $listlocalname = '';
		    foreach($nameparts as $key => $namepart) {
		        $listlocalname .= $namepart;
			    if ($key) {
				    $listlocalname .= ' ';
			    } else {
				    $listlocalname .= '<br>';
			    }
		    }				
	    }
        $listcolor = $this->get_color();
        $html .= "<td class='$listnameclass'>"."<a href='$target' style='color:$listcolor'>".$listlocalname.'</a></td>';
        $html .= '</tr></table>';
        $html .= '</a>';
        $html .= "</div>";
        return $html;
    }
}
