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
 * @category   event
 * @copyright  &copy; 2019 Jose Wilson  in respect to modifications of grid format.
 * @author     &copy; 2012 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Paul Krix and Julian Ridden.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Event observers supported by this format.
 */
class format_trail_observer {

    /**
     * Observer for the event course_content_deleted.
     *
     * Deletes the settings entry for the given course upon course deletion.
     *
     * @param \core\event\course_content_deleted $event
     */
    public static function course_content_deleted(\core\event\course_content_deleted $event) {
        if (class_exists('format_trail', false)) {
            // If class format_trail was never loaded, this is definitely not a course in 'Trail' format.
            global $DB;
            /* Delete any images associated with the course.
               Done this way so will work if the course has
               been a trail format course in the past even if
               it is not now. */
            $courseformat = format_trail::get_instance($event->objectid);
            $courseformat->delete_images();
            unset($courseformat);  // Destruct.

            $DB->delete_records("format_trail_summary", array("courseid" => $event->objectid));
        }
    }
}
