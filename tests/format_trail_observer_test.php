<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tests for format_trail_observer.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Tests for the format_trail event observer.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \format_trail_observer
 */
final class format_trail_observer_test extends advanced_testcase {
    /**
     * Builds the data array required by course_content_deleted::create().
     *
     * @param stdClass $course The course object.
     * @return array
     */
    private function event_data(stdClass $course): array {
        return [
            'objectid' => $course->id,
            'context'  => context_course::instance($course->id),
            'other'    => [
                'shortname' => $course->shortname,
                'idnumber'  => $course->idnumber,
                'format'    => 'trail',
                'options'   => [],
            ],
        ];
    }

    /**
     * Test that when course content is deleted, the format_trail_summary row is removed.
     *
     * @covers \format_trail_observer::course_content_deleted
     */
    public function test_course_content_deleted_removes_summary(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'trail', 'numsections' => 1]);

        // Ensure a summary row exists for this course.
        $DB->delete_records('format_trail_summary', ['courseid' => $course->id]);
        $DB->insert_record('format_trail_summary', (object)[
            'courseid'    => $course->id,
            'showsummary' => 1,
        ]);

        $this->assertTrue($DB->record_exists('format_trail_summary', ['courseid' => $course->id]));

        \core\event\course_content_deleted::create($this->event_data($course))->trigger();

        $this->assertFalse($DB->record_exists('format_trail_summary', ['courseid' => $course->id]));
    }

    /**
     * Test that the observer handles a course with no summary row without throwing an exception.
     *
     * @covers \format_trail_observer::course_content_deleted
     */
    public function test_course_content_deleted_with_no_summary_row(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'trail', 'numsections' => 1]);

        // Remove any summary row so the observer must handle the absence gracefully.
        $DB->delete_records('format_trail_summary', ['courseid' => $course->id]);

        \core\event\course_content_deleted::create($this->event_data($course))->trigger();

        $this->assertFalse($DB->record_exists('format_trail_summary', ['courseid' => $course->id]));
    }
}
