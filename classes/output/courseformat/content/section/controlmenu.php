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
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Contains the section control menu output class for format_trail.
 *
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_trail\output\courseformat\content\section;

use core\output\action_menu\link_secondary as action_menu_link_secondary;
use core\output\pix_icon;
use core_courseformat\output\local\content\section\controlmenu as controlmenu_base;

/**
 * Renders the section control menu for format_trail.
 *
 * Extends the core control menu to add the Highlight/Unhighlight action,
 * mirroring the behaviour of format_topics.
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
        $section = $this->section;
        $parentcontrols = parent::section_control_items();

        if ($section->is_orphan() || !$section->sectionnum) {
            return $parentcontrols;
        }

        if (!has_capability('moodle/course:setcurrentsection', $this->coursecontext)) {
            return $parentcontrols;
        }

        return $this->add_control_after($parentcontrols, 'edit', 'highlight', $this->get_section_highlight_item());
    }

    /**
     * Return the highlight/unhighlight action menu item for the section.
     *
     * @return action_menu_link_secondary the menu item
     */
    protected function get_section_highlight_item(): action_menu_link_secondary {
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

        return new action_menu_link_secondary(
            url: $url,
            icon: new pix_icon($icon, ''),
            text: $name,
            attributes: $attributes,
        );
    }
}
