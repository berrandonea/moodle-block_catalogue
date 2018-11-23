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
 *
 * File : db/upgrade.php
 * Defines what to do when upgrading the block to a new version.
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_block_catalogue_upgrade($oldversion, $block) {
    global $DB, $USER;
    require_once('access.php');
    $table = 'role_capabilities';

    foreach ($capabilities as $capabilityname => $capability) {
        foreach ($capability['archetypes'] as $rolename => $permission) {
            $rc = new stdClass();
            $rc->contextid = 1;
            $rc->roleid = $DB->get_field('role', 'id', array('shortname' => $rolename));
            $rc->capability = $capabilityname;
            $rc->permission = $permission;
            $rc->timemodified = time();
            $rc->modifierid = $USER->id;
            $params = array('contextid' => 1, 'roleid' => $rc->roleid, 'capability' => $capabilityname);
            $oldcapability = $DB->get_record($table, $params);
            if ($oldcapability) {
                if ($oldcapability->permission != $permission) {
                    $rc->id = $oldcapability->id;
                    $DB->update_record($table, $rc);
                }
            } else {
                $DB->insert_record($table, $rc);
            }
        }
    }

    $dbman = $DB->get_manager();
    if ($oldversion < 2016090600) {
        $tablefav = new xmldb_table('block_catalogue_fav');
        $tablehide = new xmldb_table('block_catalogue_hide');
        $field = new xmldb_field('elementname', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'listname');
        $dbman->change_field_precision($tablefav, $field);
        $dbman->change_field_precision($tablehide, $field);
        upgrade_block_savepoint(true, 2016090600, 'catalogue');
    }

    return true;
}
