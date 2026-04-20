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
     * Uses add_control_after() with action_menu_link_secondary on Moodle 5.x,
     * and falls back to manual array merge with legacy array format on Moodle 4.x.
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

        if (method_exists($this, 'add_control_after')) {
            // Moodle 5.x: add_control_after() and action_menu_link_secondary are available.
            return $this->add_control_after(
                $parentcontrols,
                'edit',
                'highlight',
                $this->build_highlight_item_modern()
            );
        }

        // Moodle 4.x: manual array merge using the legacy control item format.
        $controls = ['highlight' => $this->build_highlight_item_legacy()];
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
     * Return the highlight/unhighlight item for Moodle 5.x using action_menu_link_secondary.
     *
     * @return \core\output\action_menu\link_secondary the menu item
     */
    protected function build_highlight_item_modern(): \core\output\action_menu\link_secondary {
        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $sectionreturn = $format->get_sectionnum();

        $highlightoff = get_string('highlightoff');
        $highlightofficon = 'i/marked';
        $highlighton = get_string('highlight');
        $highlightonicon = 'i/marker';

        if ($course->marker == $section->sectionnum) {
            $action = 'section_unhighlight';
            $icon = $highlightofficon;
            $name = $highlightoff;
            $attributes = [
                'class' => 'editing_highlight',
                'data-action' => 'sectionUnhighlight',
                'data-sectionreturn' => $sectionreturn,
                'data-id' => $section->id,
                'data-icon' => $highlightofficon,
                'data-swapname' => $highlighton,
                'data-swapicon' => $highlightonicon,
            ];
        } else {
            $action = 'section_highlight';
            $icon = $highlightonicon;
            $name = $highlighton;
            $attributes = [
                'class' => 'editing_highlight',
                'data-action' => 'sectionHighlight',
                'data-sectionreturn' => $sectionreturn,
                'data-id' => $section->id,
                'data-icon' => $highlightonicon,
                'data-swapname' => $highlightoff,
                'data-swapicon' => $highlightofficon,
            ];
        }

        $url = $this->format->get_update_url(
            action: $action,
            ids: [$section->id],
            returnurl: $this->baseurl,
        );

        return new \core\output\action_menu\link_secondary(
            url: $url,
            icon: new \core\output\pix_icon($icon, ''),
            text: $name,
            attributes: $attributes,
        );
    }

    /**
     * Return the highlight/unhighlight item for Moodle 4.x using the legacy array format.
     *
     * @return array the control item
     */
    protected function build_highlight_item_legacy(): array {
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
