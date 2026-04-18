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
 * @copyright  2019 Jose Wilson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// phpcs:ignore moodle.Files.MoodleInternal
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/format/trail/lib.php');
require_login();

$course = optional_param('course', '', PARAM_INT);
$showsummary = optional_param('showsummary', 0, PARAM_INT);

// Ensure format_trail_summary field status exists.
$courseformat = course_get_format($course);
$summarystatus = $courseformat->get_summary_visibility($course);
$DB->set_field(
    'format_trail_summary',
    'showsummary',
    $showsummary,
    ['courseid' => $course, 'id' => $summarystatus->id]
);

redirect($CFG->wwwroot . "/course/view.php?id=" . $course);
