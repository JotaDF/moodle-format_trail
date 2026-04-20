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
 * Contains the section control menu output class for format_trail.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_trail\output\courseformat\content\section;

use core_courseformat\output\local\content\section\controlmenu as controlmenu_base;
use moodle_url;

/**
 * Renders the section control menu for format_trail.
 *
 * Extends the core control menu to add the Highlight/Unhighlight action,
 * mirroring the behaviour of format_topics. Compatible with Moodle 4.5 and 5.x.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controlmenu extends controlmenu_base {
    /**
     * Generate the edit control items of a section.
     *
     * @return array of edit control items
     */
    public function section_control_items(): array {
        $format = $this->format;
        $section = $this->section;
        $parentcontrols = parent::section_control_items();

        if ($section->is_orphan() || !$section->sectionnum) {
            return $parentcontrols;
        }

        $coursecontext = \context_course::instance($format->get_course()->id);
        if (!has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            return $parentcontrols;
        }

        $controls = ['highlight' => $this->get_section_highlight_item()];

        if (array_key_exists('edit', $parentcontrols)) {
            $merged = [];
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key === 'edit') {
                    $merged = array_merge($merged, $controls);
                }
            }
            return $merged;
        }

        return array_merge($controls, $parentcontrols);
    }

    /**
     * Return the highlight/unhighlight control item for the section.
     *
     * Returns an array compatible with the legacy section control items format
     * used in Moodle 4.x and 5.x.
     *
     * @return array the control item
     */
    protected function get_section_highlight_item(): array {
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_sectionnum();

        $highlightoff = get_string('highlightoff');
        $highlightofficon = 'i/marked';
        $highlighton = get_string('highlight');
        $highlightonicon = 'i/marker';

        $url = new moodle_url('/course/view.php', ['id' => $course->id, 'sesskey' => sesskey()]);
        if (!is_null($sectionreturn)) {
            $url->param('sectionid', $format->get_sectionid());
        }

        if ($course->marker == $section->sectionnum) {
            $url->param('marker', 0);
            return [
                'url' => $url,
                'icon' => $highlightofficon,
                'name' => $highlightoff,
                'pixattr' => ['class' => ''],
                'attr' => [
                    'class' => 'editing_highlight',
                    'data-action' => 'sectionUnhighlight',
                    'data-sectionreturn' => $sectionreturn,
                    'data-id' => $section->id,
                    'data-icon' => $highlightofficon,
                    'data-swapname' => $highlighton,
                    'data-swapicon' => $highlightonicon,
                ],
            ];
        }

        $url->param('marker', $section->sectionnum);
        return [
            'url' => $url,
            'icon' => $highlightonicon,
            'name' => $highlighton,
            'pixattr' => ['class' => ''],
            'attr' => [
                'class' => 'editing_highlight',
                'data-action' => 'sectionHighlight',
                'data-sectionreturn' => $sectionreturn,
                'data-id' => $section->id,
                'data-icon' => $highlightonicon,
                'data-swapname' => $highlightoff,
                'data-swapicon' => $highlightofficon,
            ],
        ];
    }
}
