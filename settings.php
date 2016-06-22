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
 * File : list/activities/blockcatalogue.list.php
 * Global settings.
 */

$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_catalogue'),
            get_string('descconfig', 'block_catalogue')
        ));

$settings->add(new admin_setting_configtext(
            'catalogue/displayedlists',
            get_string('displayedlists', 'block_catalogue'),
            get_string('descdisplayedlists', 'block_catalogue'),
            'resources,activities,enrols,grades,reports,blocks'
        ));

$settings->add(new admin_setting_configcheckbox(
            'catalogue/getremotedata',
            get_string('getremotedata', 'block_catalogue'),
            get_string('descgetremotedata', 'block_catalogue'),
            '1'
        ));