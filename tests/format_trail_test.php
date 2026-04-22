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
 * Unit and integration tests for the format_trail class.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Tests for the format_trail course format class.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \format_trail
 */
final class format_trail_test extends advanced_testcase {
    /**
     * Returns a minimal valid data array for edit_form_validation.
     *
     * @return array
     */
    private function valid_form_data(): array {
        return [
            'bordercolour'                            => '#dddddd',
            'imagecontainerbackgroundcolour'          => '#f1f2f2',
            'currentselectedsectioncolour'            => '#8E66FF',
            'currentselectedimagecontainercolour'     => '#ffc540',
            'sectiontitletraillengthmaxoption'        => 0,
            'sectiontitleinsidetitletextcolour'       => '#000000',
            'sectiontitleinsidetitlebackgroundcolour' => '#ffffff',
            'sectiontitleboxopacity'                  => '.8',
            'sectiontitlefontsize'                    => 0,
            'sectiontitlesummarytextcolour'           => '#3b53ad',
            'sectiontitlesummarybackgroundcolour'     => '#ffc540',
            'sectiontitlesummarybackgroundopacity'    => '1',
        ];
    }

    /**
     * Test that hex2rgb converts a 6-character hex string correctly.
     *
     * @covers \format_trail::hex2rgb
     */
    public function test_hex2rgb_six_digit(): void {
        $this->assertEquals(['r' => 255, 'g' => 255, 'b' => 255], format_trail::hex2rgb('ffffff'));
        $this->assertEquals(['r' => 0, 'g' => 0, 'b' => 0], format_trail::hex2rgb('000000'));
        $this->assertEquals(['r' => 221, 'g' => 221, 'b' => 221], format_trail::hex2rgb('dddddd'));
        $this->assertEquals(['r' => 142, 'g' => 102, 'b' => 255], format_trail::hex2rgb('8E66FF'));
    }

    /**
     * Test that hex2rgb expands a 3-character hex string correctly.
     *
     * @covers \format_trail::hex2rgb
     */
    public function test_hex2rgb_three_digit(): void {
        $this->assertEquals(['r' => 255, 'g' => 255, 'b' => 255], format_trail::hex2rgb('fff'));
        $this->assertEquals(['r' => 0, 'g' => 0, 'b' => 0], format_trail::hex2rgb('000'));
        $this->assertEquals(['r' => 170, 'g' => 187, 'b' => 204], format_trail::hex2rgb('abc'));
    }

    /**
     * Test that the default image container width is 210.
     *
     * @covers \format_trail::get_default_image_container_width
     */
    public function test_get_default_image_container_width(): void {
        $this->assertSame(210, format_trail::get_default_image_container_width());
    }

    /**
     * Test that the default image container ratio index is 1.
     *
     * @covers \format_trail::get_default_image_container_ratio
     */
    public function test_get_default_image_container_ratio(): void {
        $this->assertSame(1, format_trail::get_default_image_container_ratio());
    }

    /**
     * Test that the default image resize method is 1 (Scale).
     *
     * @covers \format_trail::get_default_image_resize_method
     */
    public function test_get_default_image_resize_method(): void {
        $this->assertSame(1, format_trail::get_default_image_resize_method());
    }

    /**
     * Test that the default border width is 3.
     *
     * @covers \format_trail::get_default_border_width
     */
    public function test_get_default_border_width(): void {
        $this->assertSame(3, format_trail::get_default_border_width());
    }

    /**
     * Test that the default border radius is 2 (On).
     *
     * @covers \format_trail::get_default_border_radius
     */
    public function test_get_default_border_radius(): void {
        $this->assertSame(2, format_trail::get_default_border_radius());
    }

    /**
     * Test that the default section title font size is 0 (derived).
     *
     * @covers \format_trail::get_default_section_title_font_size
     */
    public function test_get_default_section_title_font_size(): void {
        $this->assertSame(0, format_trail::get_default_section_title_font_size());
    }

    /**
     * Test that the default section title alignment is 'center'.
     *
     * @covers \format_trail::get_default_section_title_alignment
     */
    public function test_get_default_section_title_alignment(): void {
        $this->assertSame('center', format_trail::get_default_section_title_alignment());
    }

    /**
     * Test that the default image container alignment is 'center'.
     *
     * @covers \format_trail::get_default_image_container_alignment
     */
    public function test_get_default_image_container_alignment(): void {
        $this->assertSame('center', format_trail::get_default_image_container_alignment());
    }

    /**
     * Test that all default colour methods return valid CSS hex values.
     *
     * @covers \format_trail::get_default_border_colour
     * @covers \format_trail::get_default_image_container_background_colour
     * @covers \format_trail::get_default_current_selected_section_colour
     * @covers \format_trail::get_default_current_selected_image_container_colour
     * @covers \format_trail::get_default_current_selected_image_container_text_colour
     * @covers \format_trail::get_default_section_title_inside_title_text_colour
     * @covers \format_trail::get_default_section_title_inside_title_background_colour
     * @covers \format_trail::get_default_section_title_summary_text_colour
     * @covers \format_trail::get_default_section_title_summary_background_colour
     */
    public function test_default_colours_are_valid_hex(): void {
        $pattern = '/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/';
        $colours = [
            format_trail::get_default_border_colour(),
            format_trail::get_default_image_container_background_colour(),
            format_trail::get_default_current_selected_section_colour(),
            format_trail::get_default_current_selected_image_container_colour(),
            format_trail::get_default_current_selected_image_container_text_colour(),
            format_trail::get_default_section_title_inside_title_text_colour(),
            format_trail::get_default_section_title_inside_title_background_colour(),
            format_trail::get_default_section_title_summary_text_colour(),
            format_trail::get_default_section_title_summary_background_colour(),
        ];
        foreach ($colours as $colour) {
            $this->assertMatchesRegularExpression($pattern, $colour, "Invalid hex colour: '$colour'.");
        }
    }

    /**
     * Test that get_image_container_widths returns positive integer keys.
     *
     * @covers \format_trail::get_image_container_widths
     */
    public function test_get_image_container_widths(): void {
        $widths = format_trail::get_image_container_widths();
        $this->assertIsArray($widths);
        $this->assertNotEmpty($widths);
        foreach (array_keys($widths) as $key) {
            $this->assertIsInt($key);
            $this->assertGreaterThan(0, $key);
        }
    }

    /**
     * Test that get_image_container_ratios returns a non-empty array.
     *
     * @covers \format_trail::get_image_container_ratios
     */
    public function test_get_image_container_ratios(): void {
        $ratios = format_trail::get_image_container_ratios();
        $this->assertIsArray($ratios);
        $this->assertNotEmpty($ratios);
    }

    /**
     * Test that get_border_widths covers the full range 0–10.
     *
     * @covers \format_trail::get_border_widths
     */
    public function test_get_border_widths(): void {
        $widths = format_trail::get_border_widths();
        $this->assertIsArray($widths);
        $this->assertArrayHasKey(0, $widths);
        $this->assertArrayHasKey(10, $widths);
    }

    /**
     * Test that get_horizontal_alignments contains a 'center' key.
     *
     * @covers \format_trail::get_horizontal_alignments
     */
    public function test_get_horizontal_alignments_contains_center(): void {
        $alignments = format_trail::get_horizontal_alignments();
        $this->assertIsArray($alignments);
        $this->assertArrayHasKey('center', $alignments);
    }

    /**
     * Test that get_image_path returns a non-empty string.
     *
     * @covers \format_trail::get_image_path
     */
    public function test_get_image_path(): void {
        $path = format_trail::get_image_path();
        $this->assertIsString($path);
        $this->assertNotEmpty($path);
    }

    /**
     * Test that get_maximum_image_width returns a positive integer.
     *
     * @covers \format_trail::get_maximum_image_width
     */
    public function test_get_maximum_image_width(): void {
        $this->assertGreaterThan(0, format_trail::get_maximum_image_width());
    }

    /**
     * Test calculate_image_container_properties with non-default values triggers recalculation.
     *
     * @covers \format_trail::calculate_image_container_properties
     */
    public function test_calculate_image_container_properties_recalculates(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        // Width=192, ratio=1 (3-2), borderwidth=2 — all differ from the static cache defaults.
        $result = $format->calculate_image_container_properties(192, 1, 2);

        $this->assertArrayHasKey('height', $result);
        $this->assertArrayHasKey('margin-top', $result);
        $this->assertArrayHasKey('margin-left', $result);

        // Height: calculate_height(192, ratio=1) → basewidth=192/3=64 → height=64*2=128.
        $this->assertEquals(128, $result['height']);
        // Margin-top: 128 - (42 - 2) = 88.
        $this->assertEquals(88, $result['margin-top']);
        // Margin-left: (192 - 95) + 2 = 99.
        $this->assertEquals(99, $result['margin-left']);
    }

    /**
     * Test calculate_image_container_properties with cached (default) values returns cached data.
     *
     * @covers \format_trail::calculate_image_container_properties
     */
    public function test_calculate_image_container_properties_cached(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        // Use default values to hit the cached-result branch (width=210, ratio=1, border=3).
        $result = $format->calculate_image_container_properties(210, 1, 3);

        $this->assertArrayHasKey('height', $result);
        $this->assertArrayHasKey('margin-top', $result);
        $this->assertArrayHasKey('margin-left', $result);
        $this->assertGreaterThan(0, $result['height']);
    }

    /**
     * Test that get_summary_visibility creates and returns a record when none exists.
     *
     * @covers \format_trail::get_summary_visibility
     */
    public function test_get_summary_visibility_creates_record(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        // Ensure no pre-existing record.
        $DB->delete_records('format_trail_summary', ['courseid' => $course->id]);

        $visibility = $format->get_summary_visibility($course->id);

        $this->assertIsObject($visibility);
        $this->assertTrue(property_exists($visibility, 'showsummary'));
        $this->assertSame(1, (int) $visibility->showsummary);
        $this->assertTrue($DB->record_exists('format_trail_summary', ['courseid' => $course->id]));
    }

    /**
     * Test that get_summary_visibility returns the existing record without duplicating it.
     *
     * @covers \format_trail::get_summary_visibility
     */
    public function test_get_summary_visibility_returns_existing_record(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        $DB->delete_records('format_trail_summary', ['courseid' => $course->id]);
        $DB->insert_record('format_trail_summary', (object)['courseid' => $course->id, 'showsummary' => 0]);

        $visibility = $format->get_summary_visibility($course->id);

        $this->assertSame(0, (int) $visibility->showsummary);
        $this->assertSame(1, $DB->count_records('format_trail_summary', ['courseid' => $course->id]));
    }

    /**
     * Test that get_images returns false when no section images exist.
     *
     * @covers \format_trail::get_images
     */
    public function test_get_images_returns_false_when_empty(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail', 'numsections' => 2]);
        $format = format_trail::get_instance($course->id);

        $result = $format->get_images($course->id);

        $this->assertFalse($result);
    }

    /**
     * Test that edit_form_validation returns no errors for fully valid form data.
     *
     * @covers \format_trail::edit_form_validation
     */
    public function test_edit_form_validation_valid_data(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        $errors = $format->edit_form_validation($this->valid_form_data(), [], []);

        $this->assertIsArray($errors);
        $this->assertEmpty($errors);
    }

    /**
     * Test that an invalid border colour generates a validation error.
     *
     * @covers \format_trail::edit_form_validation
     */
    public function test_edit_form_validation_invalid_border_colour(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        $data = $this->valid_form_data();
        $data['bordercolour'] = 'notacolour';

        $errors = $format->edit_form_validation($data, [], []);

        $this->assertArrayHasKey('bordercolour', $errors);
    }

    /**
     * Test that a negative sectiontitletraillengthmaxoption generates a validation error.
     *
     * @covers \format_trail::edit_form_validation
     */
    public function test_edit_form_validation_negative_trail_length(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        $data = $this->valid_form_data();
        $data['sectiontitletraillengthmaxoption'] = -1;

        $errors = $format->edit_form_validation($data, [], []);

        $this->assertArrayHasKey('sectiontitletraillengthmaxoption', $errors);
    }

    /**
     * Test that an invalid opacity value generates a validation error.
     *
     * @covers \format_trail::edit_form_validation
     */
    public function test_edit_form_validation_invalid_opacity(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        $data = $this->valid_form_data();
        $data['sectiontitleboxopacity'] = '1.5';

        $errors = $format->edit_form_validation($data, [], []);

        $this->assertArrayHasKey('sectiontitleboxopacity', $errors);
    }

    /**
     * Test that an invalid section title font size generates a validation error.
     *
     * @covers \format_trail::edit_form_validation
     */
    public function test_edit_form_validation_invalid_font_size(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $format = format_trail::get_instance($course->id);

        $data = $this->valid_form_data();
        $data['sectiontitlefontsize'] = 99;

        $errors = $format->edit_form_validation($data, [], []);

        $this->assertArrayHasKey('sectiontitlefontsize', $errors);
    }
}
