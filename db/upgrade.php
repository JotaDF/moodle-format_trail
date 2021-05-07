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
 * Trail Format - A topics based format that uses a trail of user selectable images to popup a light box of the section.
 *
 * @package    format_trail
 * @copyright  &copy; 2019 Jose Wilson  in respect to modifications of grid format.
 * @author     &copy; 2012 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Paul Krix and Julian Ridden.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * Process update plugin.
 *
 * @param int $oldversion
 * @return boolean.
 */
function xmldb_format_trail_upgrade($oldversion = 0) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011041802) {
        // Define table course_trail_summary to be created.
        $table = new xmldb_table('course_trail_summary');

        // Adding fields to table course_trail_summary.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('show_summary', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '0', null);
        $table->add_field('course_id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);

        // Adding keys to table course_trail_summary.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Launch create table for course_trail_summary.
        $dbman->create_table($table);
        upgrade_plugin_savepoint(true, '2011041802', 'format', 'trail');
    }

    if ($oldversion < 2012011701) {
        // Rename the tables.
        if ($dbman->table_exists('course_trail_icon')) {
            $table = new xmldb_table('course_trail_icon');
            if (!$dbman->table_exists('format_trail_icon')) {
                $dbman->rename_table($table, 'format_trail_icon');
            } else {
                // May as well tidy up the db.
                $dbman->drop_table($table);
            }
        }

        if ($dbman->table_exists('course_trail_summary')) {
            $table = new xmldb_table('course_trail_summary');
            if (!$dbman->table_exists('format_trail_summary')) {
                $dbman->rename_table($table, 'format_trail_summary');
            } else {
                // May as well tidy up the db.
                $dbman->drop_table($table);
            }
        }

        upgrade_plugin_savepoint(true, '2012011701', 'format', 'trail');
    }

    if ($oldversion < 2012071500) {
        $table = new xmldb_table('format_trail_summary');

        $field = new xmldb_field('course_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
        // Rename course_id.
        $dbman->rename_field($table, $field, 'courseid');

        $field = new xmldb_field('show_summary', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        // Rename show_summary.
        $dbman->rename_field($table, $field, 'showsummary');

        // Add fields and change to unsigned.
        $table = new xmldb_table('format_trail_icon');

        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1', 'sectionid');
        // Conditionally launch add field courseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, '2012071500', 'format', 'trail');
    }

    if ($oldversion < 2013110400) {
        $table = new xmldb_table('format_trail_icon');

        $field = new xmldb_field('imagepath', XMLDB_TYPE_TEXT, null, null, null, null, null);
        // Rename imagepath.
        $dbman->rename_field($table, $field, 'image');

        $field = new xmldb_field('displayedimageindex', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        // Conditionally launch add field displayediconpath.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, '2013110400', 'format', 'trail');
    }

    // Automatic 'Purge all caches'....
    if ($oldversion < 2114052000) {
        purge_all_caches();
    }

    return true;
}
