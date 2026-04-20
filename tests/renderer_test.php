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
 * Testes básicos do renderer do format_trail.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use core_courseformat\base as format_base;

/**
 * Testes do renderer do format_trail.
 *
 * @group format_trail
 */
class format_trail_renderer_test extends advanced_testcase {
    /**
     * Testa se o renderer renderiza a página de múltiplas seções sem erro.
     */
    public function test_print_multiple_section_page_renders_without_error(): void {
        global $PAGE, $DB;
        $this->resetAfterTest();

        // Cria um curso de teste.
        $course = $this->getDataGenerator()->create_course(['format' => 'trail']);
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 1]);
        $context = context_course::instance($course->id);
        $PAGE->set_context($context);
        $PAGE->set_course($course);
        $PAGE->set_pagelayout('course');
        $PAGE->set_url('/course/view.php', ['id' => $course->id]);

        // Obtém o renderer do plugin.
        $renderer = $PAGE->get_renderer('format_trail');
        $format = course_get_format($course);

        ob_start();
        try {
            $renderer->print_multiple_section_page($course, null, null, null, null);
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            $this->fail('Exceção lançada ao renderizar: ' . $e->getMessage());
        }
        $this->assertNotEmpty($output, 'A saída do renderer está vazia.');
        $this->assertStringContainsString('section', $output, 'A saída não contém "section".');
    }
}
