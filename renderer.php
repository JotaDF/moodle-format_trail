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

use core_courseformat\output\section_renderer;
use core_availability\info;
use core_availability\info_section;

require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/trail/lib.php');

/**
 * format_trail_renderer class
 *
 * @package    format_trail
 * @copyright  &copy; 2019 Jose Wilson  in respect to modifications of grid format.
 * @author     &copy; 2012 G J Barnard in respect to modifications of standard topics format.
 */
class format_trail_renderer extends section_renderer {

    /**
     * @var boolean $section0attop to state if section zero is at the top (true) or in the trail (false).
     */
    protected $section0attop;

    /**
     * @var \stdClass $courseformat course format object as defined in lib.php.
     */
    protected $courseformat;

    /**
     * @var array $settings.
     */
    private $settings;

    /**
     * @var array $shadeboxshownarray.
     */
    private $shadeboxshownarray = array();

    /**
     * @var int $portable.
     */
    private $portable = 0; // 1 = mobile, 2 = tablet.
    /**
     * @var int $initialsection.
     */
    protected $initialsection = -1;

    /**
     * Constructor method, calls the parent constructor - MDL-21097
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courseformat = course_get_format($page->course);
        $this->settings = $this->courseformat->get_settings();
        $this->section0attop = $this->courseformat->is_section0_attop();

        /* Since format_trail_renderer::section_edit_controls() only displays the 'Set current section' control when editing
          mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
          other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'gtopics', 'id' => 'gtopics'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('sectionname', 'format_trail');
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render($this->courseformat->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render($this->courseformat->inplace_editable_render_section_name($section, false));
    }

    /**
     * Generate next/previous section links for naviation
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return array associative array with previous and next section link
     */
    protected function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
                or ! $course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        if (!$this->section0attop) {
            $buffer = -1;
        } else if ($this->settings['setsection0ownpagenotrailonesection'] == 2) {
            $buffer = -1;
        } else {
            $buffer = 0;
        }
        while ($back > $buffer and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array();
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
                $previouslink .= get_section_name($course, $sections[$back]);
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $coursenumsections = $this->courseformat->get_last_section_number();
        $forward = $sectionno + 1;
        while ($forward <= $coursenumsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array();
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * @param \stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param \stdClass $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        $o = '';
        $sectionmenu = array();
        $sectionmenu[course_get_url($course)->out(false)] = get_string('maincoursepage');
        $modinfo = get_fast_modinfo($course);
        $section = 1;
        if (!$this->section0attop) {
            $section = 0;
        } else if ($this->settings['setsection0ownpagenotrailonesection'] == 2) {
            $section = 0;
        } else {
            $section = 1;
        }
        $coursenumsections = $this->courseformat->get_last_section_number();
        while ($section <= $coursenumsections) {
            $thissection = $modinfo->get_section_info($section);
            $showsection = $thissection->uservisible or ! $course->hiddensections;
            if (($showsection) && ($section != $displaysection) && ($url = course_get_url($course, $section))) {
                $sectionmenu[$url->out(false)] = get_section_name($course, $section);
            }
            $section++;
        }

        $select = new url_select($sectionmenu, '', array('' => get_string('jumpto')));
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included for when section 0 is in the trail
     * and a single section page.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    protected function section_header_onsectionpage_topic0notattop($section, $course) {
        $o = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o .= html_writer::start_tag('li', array('id' => 'section-' . $section->section,
                    'class' => 'section main clearfix' . $sectionstyle, 'role' => 'region',
                    'aria-label' => get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, true);
        $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, true);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        $sectionname = html_writer::tag('span', $this->section_title($section, $course));
        $o .= $this->output->heading($sectionname, 3, 'sectionname accesshide');

        $o .= html_writer::start_tag('div', array('class' => 'summary'));

        $o .= $this->format_summary_text($section);
        $o .= core_courseformat\output\local\content\section\summary::format_summary_text();
        $o .= html_writer::end_tag('div');

        $o .= $this->section_availability($section);

        return $o;
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        if (($this->section0attop) && ($this->settings['setsection0ownpagenotrailonesection'] == 1)) {
            return parent::print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection);
        } else {
            $modinfo = get_fast_modinfo($course);
            $course = course_get_format($course)->get_course();

            // Can we view the section in question?
            if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
                // This section doesn't exist.
                throw new moodle_exception('unknowncoursesection', 'error', null, $course->fullname);
                return;
            }

            if (!$sectioninfo->uservisible) {
                if (!$course->hiddensections) {
                    echo $this->start_section_list();
                    echo $this->section_hidden($displaysection, $course->id);
                    echo $this->end_section_list();
                }
                // Can't view this section.
                return;
            }

            // Copy activity clipboard..
            echo $this->course_activity_clipboard($course, $displaysection);

            // Start single-section div.
            echo html_writer::start_tag('div', array('class' => 'single-section'));

            // The requested section page.
            $thissection = $modinfo->get_section_info($displaysection);

            // Title with section navigation links.
            $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
            $sectiontitle = '';
            $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
            $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
            $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
            // Title attributes.
            $classes = 'sectionname';
            if (!$thissection->visible) {
                $classes .= ' dimmed_text';
            }
            $sectionname = html_writer::tag('span', get_section_name($course, $displaysection));
            $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

            $sectiontitle .= html_writer::end_tag('div');
            echo $sectiontitle;

            // Now the list of sections..
            echo $this->start_section_list();

            echo $this->section_header_onsectionpage_topic0notattop($thissection, $course);
            // Show completion help icon.
            $completioninfo = new completion_info($course);
            echo $completioninfo->display_help_icon();

            echo $this->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->course_section_add_cm_control($course, $displaysection, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();

            // Display section bottom navigation.
            $sectionbottomnav = '';
            $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
            $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
            $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
            $sectionbottomnav .= html_writer::tag('div',
                    $this->section_nav_selection($course, $sections, $displaysection), array('class' => 'mdl-align'));
            $sectionbottomnav .= html_writer::end_tag('div');
            echo $sectionbottomnav;

            // Close single-section div.
            echo html_writer::end_tag('div');
        }
    }

    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods
     * @param array $modnames
     * @param array $modnamesused
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $USER;

        $coursecontext = context_course::instance($course->id);
        $editing = $this->page->user_is_editing();
        $hascapvishidsect = has_capability('moodle/course:viewhiddensections', $coursecontext);

        if ($editing) {
            $streditsummary = get_string('editsummary');
            $urlpicedit = $this->output->image_url('t/edit');
        } else {
            $urlpicedit = false;
            $streditsummary = '';
        }
        // Contribuition adriano515.
        echo html_writer::start_tag('div', array('id' => 'trailmiddle-column', 'style' => 'overflow: hidden'));
        echo $this->output->skip_link_target();

        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();

        // Start at 1 to skip the summary block or include the summary block if it's in the trail display.
        if ($this->section0attop) {
            $this->section0attop = $this->make_block_topic0($course, $sections[0], $editing, $urlpicedit, $streditsummary, false);
            // For the purpose of the trail shade box shown array topic 0 is not shown.
            $this->shadeboxshownarray[0] = 1;
        }
        echo html_writer::start_tag('div', array('id' => 'trailiconcontainer', 'role' => 'navigation',
            'aria-label' => get_string('trailimagecontainer', 'format_trail')));

        $trailiconsclass = 'trailicons';
        if ($this->settings['sectiontitleboxposition'] == 1) {
            $trailiconsclass .= ' content_inside';
        }
        $defaultcustommousepointers = get_config('format_trail', 'defaultcustommousepointers');
        if ($defaultcustommousepointers == 2) { // Yes.
            $trailiconsclass .= ' trailcursor';
        }

        echo html_writer::start_tag('ul', array('class' => $trailiconsclass));
        // Print all of the image containers.
        $this->make_block_icon_topics($coursecontext->id, $sections, $course, $editing, $hascapvishidsect, $urlpicedit);
        echo html_writer::end_tag('ul');

        echo html_writer::end_tag('div');

        $rtl = right_to_left();

        $coursenumsections = $this->courseformat->get_last_section_number();

        if (!(($course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) && (!$editing))) {
            $trailshadeboxattributes = array('id' => 'trailshadebox');
            if ($defaultcustommousepointers == 2) { // Yes.
                $trailshadeboxattributes['class'] = 'trailcursor';
            }
            echo html_writer::start_tag('div', $trailshadeboxattributes);
            echo html_writer::tag('div', '', array('id' => 'trailshadebox_overlay', 'style' => 'display: none;'));

            $trailshadeboxcontentclasses = array('hide_content');
            if (!$editing) {
                if ($this->settings['fitsectioncontainertowindow'] == 2) {
                    $trailshadeboxcontentclasses[] = 'fit_to_window';
                } else {
                    $trailshadeboxcontentclasses[] = 'absolute';
                }
            }

            echo html_writer::start_tag('div', array('id' => 'trailshadebox_content',
                'class' => implode(' ', $trailshadeboxcontentclasses),
                'role' => 'region',
                'aria-label' => get_string('shadeboxcontent', 'format_trail')));

            $deviceextra = '';
            switch ($this->portable) {
                case 1: // Mobile.
                    $deviceextra = ' trailshadebox_mobile';
                    break;
                case 2: // Tablet.
                    $deviceextra = ' trailshadebox_tablet';
                    break;
                default:
                    break;
            }
            $closeshadebox = get_string('closeshadebox', 'format_trail');
            echo html_writer::tag('img', '', array('id' => 'trailshadebox_close', 'style' => 'display: none;',
                'class' => $deviceextra,
                'src' => $this->output->image_url('close', 'format_trail'),
                'role' => 'link',
                'alt' => $closeshadebox,
                'aria-label' => $closeshadebox));

            if ($this->settings['hidenavside'] == 1) {
                // Only show the arrows if there is more than one box shown.
                if (($coursenumsections > 1) || (($coursenumsections == 1) && (!$this->section0attop))) {
                    if ($rtl) {
                        $previcon = 'right';
                        $nexticon = 'left';
                        $areadir = 'rtl';
                    } else {
                        $previcon = 'left';
                        $nexticon = 'right';
                        $areadir = 'ltr';
                    }
                    $previoussection = get_string('previoussection', 'format_trail');
                    $prev = html_writer::start_tag('div', array('id' => 'trailshadebox_previous',
                                'class' => 'trailshadebox_area trailshadebox_previous_area ' . $areadir,
                                'style' => 'display: none;',
                                'role' => 'link',
                                'aria-label' => $previoussection)
                    );
                    $prev .= html_writer::tag('img', '',
                            array('class' => 'trailshadebox_arrow trailshadebox_previous' . $deviceextra,
                                'src' => $this->output->image_url('fa-arrow-circle-' . $previcon . '-w', 'format_trail'),
                                'alt' => $previoussection,
                                'aria-label' => $previoussection
                                    )
                    );
                    $prev .= html_writer::end_tag('div');
                    $nextsection = get_string('nextsection', 'format_trail');
                    $next = html_writer::start_tag('div', array('id' => 'trailshadebox_next',
                                'class' => 'trailshadebox_area trailshadebox_next_area ' . $areadir,
                                'style' => 'display: none;',
                                'role' => 'link',
                                'aria-label' => $nextsection)
                    );
                    $next .= html_writer::tag('img', '', array('class' => 'trailshadebox_arrow trailshadebox_next' . $deviceextra,
                                'src' => $this->output->image_url('fa-arrow-circle-' . $nexticon . '-w', 'format_trail'),
                                'alt' => $nextsection,
                                'aria-label' => $nextsection
                                    )
                    );
                    $next .= html_writer::end_tag('div');

                    if ($rtl) {
                        echo $next . $prev;
                    } else {
                        echo $prev . $next;
                    }
                }
            }

            echo $this->start_section_list();
            // If currently moving a file then show the current clipboard.
            $this->make_block_show_clipboard_if_file_moving($course);

            // Print Section 0 with general activities.
            if (!$this->section0attop) {
                $this->make_block_topic0($course, $sections[0], $editing, $urlpicedit, $streditsummary, false);
            }

            /* Now all the normal modules by topic.
              Everything below uses "section" terminology - each "section" is a topic/module. */
            $this->make_block_topics($course, $sections, $modinfo, $editing,
                    $hascapvishidsect, $streditsummary, $urlpicedit, false);

            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');
            echo html_writer::tag('div', '&nbsp;', array('class' => 'clearer'));
        }
        echo html_writer::end_tag('div');

        $sectionredirect = null;
        if ($course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            // Get the redirect URL prefix for keyboard control with the 'Show one section per page' layout.
            $sectionredirect = $this->courseformat->get_view_url(null)->out(true);
        }

        // Initialise the shade box functionality:...
        $this->page->requires->js_init_call('M.format_trail.init', array(
            $this->page->user_is_editing(),
            $sectionredirect,
            $coursenumsections,
            $this->initialsection,
            json_encode($this->shadeboxshownarray)));
        if (!$this->page->user_is_editing()) {
            // Initialise the key control functionality...
            $this->page->requires->yui_module('moodle-format_trail-trailkeys',
                    'M.format_trail.trailkeys.init', array(array('rtl' => $rtl)), null, true);
        }
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {

        if (!$this->page->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $controls = array();
        if ($section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $markedthissection = get_string('markedthissection', 'format_trail');
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
                    'name' => $highlightoff,
                    'pixattr' => array('class' => '', 'alt' => $markedthissection),
                    'attr' => array('class' => 'editing_highlight', 'title' => $markedthissection,
                        'data-action' => 'removemarker'));
                $url->param('marker', 0);
            } else {
                $url->param('marker', $section->section);
                $markthissection = get_string('markthissection', 'format_trail');
                $highlight = get_string('highlight');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
                    'name' => $highlight,
                    'pixattr' => array('class' => '', 'alt' => $markthissection),
                    'attr' => array('class' => 'editing_highlight', 'title' => $markthissection,
                        'data-action' => 'setmarker'));
            }
        }

        $parentcontrols = $this->section_edit_control_items_orig($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            // We can't use splice because we are using associative arrays.
            // Step through the array and merge the arrays.
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
    }

    /**
     * Generate block section
     *
     * @param \stdClass $course The course entry from DB
     * @param \stdClass $sectionzero
     * @param string $editing
     * @param string $urlpicedit
     * @param string $streditsummary
     * @param string $onsectionpage
     * @return boolean
     */
    private function make_block_topic0($course, $sectionzero, $editing, $urlpicedit, $streditsummary, $onsectionpage) {

        if ($this->section0attop) {
            echo html_writer::start_tag('ul', array('class' => 'gtopics-0'));
        }

        $sectionname = $this->courseformat->get_section_name($sectionzero);
        echo html_writer::start_tag('li', array(
            'id' => 'section-0',
            'class' => 'section main' . ($this->section0attop ? '' : ' trail_section hide_section'),
            'role' => 'region',
            'aria-label' => $sectionname)
        );

        echo html_writer::start_tag('div', array('class' => 'content'));

        if (!$onsectionpage) {
            echo $this->output->heading($sectionname, 3, 'sectionname');
        }

        echo html_writer::start_tag('div', array('class' => 'summary'));

        echo $this->format_summary_text($sectionzero);

        if ($editing) {
            echo html_writer::link(
                    new moodle_url('editsection.php',
                            array('id' => $sectionzero->id)),
                    html_writer::empty_tag('img', array('src' => $urlpicedit,
                        'alt' => $streditsummary,
                        'class' => 'iconsmall edit')), array('title' => $streditsummary));
        }
        echo html_writer::end_tag('div');

        echo $this->course_section_cm_list($course, $sectionzero, 0);

        if ($editing) {
            echo $this->courserenderer->course_section_add_cm_control($course, $sectionzero->section, 0, 0);
        }
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('li');

        if ($this->section0attop) {
            echo html_writer::end_tag('ul');
        }
        return true;
    }

    /**
     * States if the icon is to be greyed out.
     *
     * For logic see: section_availability_message() + a bit more!
     *
     * @param stdClass $course The course entry from DB
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return bool Grey out the section icon, true or false?
     */
    protected function section_greyedout($course, $section, $canviewhidden) {
        global $CFG;
        $sectiongreyedout = false;
        if (!$section->visible) {
            if ($canviewhidden) {
                $sectiongreyedout = true;
            } else if (!$course->hiddensections) { // Hidden sections in collapsed form.
                $sectiongreyedout = true;
            }
        } else if (!$section->uservisible) {
            if (($section->availableinfo) && ((!$course->hiddensections) || ($canviewhidden))) {
                // Hidden sections in collapsed form.
                // Note: We only get to this function if availableinfo is non-empty,
                // so there is definitely something to print.
                $sectiongreyedout = true;
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new \core_availability\info_section($section);
            $fullinfo = $ci->get_full_information();
            $information = '';
            if ($fullinfo && (!$ci->is_available($information))) {
                $sectiongreyedout = true;
                $information = '';
            }
        }
        return $sectiongreyedout;
    }

    /**
     * Makes the trail image containers.
     *
     * @param int $contextid
     * @param array $sections
     * @param \stdClass $course
     * @param string $editing
     * @param string $hascapvishidsect
     * @param string $urlpicedit
     * @return none
     */
    private function make_block_icon_topics($contextid, $sections, $course, $editing, $hascapvishidsect, $urlpicedit) {
        global $CFG, $USER;

        $coursecontext = context_course::instance($course->id);

        // Get the section images for the course.
        $sectionimages = $this->courseformat->get_images($course->id);

        // CONTRIB-4099.
        $trailimagepath = $this->courseformat->get_image_path();

        if ($course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
            $singlepageurl = $this->courseformat->get_view_url(null)->out(true);
        }

        if ($this->settings['showsectiontitlesummary'] == 2) {
            $this->page->requires->js_call_amd('format_trail/tooltip', 'init');
        }

        // Start at 1 to skip the summary block or include the summary block if it's in the trail display.
        $coursenumsections = $this->courseformat->get_last_section_number();
        // Contribuition adriano515.
        $count = 1;
        foreach ($sections as $section => $thissection) {
            if ((($this->section0attop) && ($section == 0)) || ($section > $coursenumsections)) {
                continue;  // Section 0 at the top and not in the trail / orphaned section.
            }

            // Check if section is visible to user.
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available &&
                    !empty($thissection->availableinfo) && ((!$course->hiddensections) || ($hascapvishidsect)));

            // If we should grey it out, flag that here.
            $sectiongreyedout = false;
            if ($this->settings['greyouthidden'] == 2) {
                $sectiongreyedout = $this->section_greyedout($course, $thissection, $hascapvishidsect);
            }

            if ($showsection || $sectiongreyedout) {
                // We now know the value for the trail shade box shown array.
                $this->shadeboxshownarray[$section] = 2;

                $sectionname = $this->courseformat->get_section_name($thissection);
                $sectiontitleattribues = array();
                if ($this->settings['hidesectiontitle'] == 1) {
                    $displaysectionname = $sectionname;
                } else {
                    $displaysectionname = '';
                    $sectiontitleattribues['aria-label'] = $sectionname;
                }
                if ($this->settings['sectiontitletraillengthmaxoption'] != 0) {
                    $sectionnamelen = core_text::strlen($displaysectionname);
                    if ($sectionnamelen !== false) {
                        if ($sectionnamelen > $this->settings['sectiontitletraillengthmaxoption']) {
                            $displaysectionname = core_text::substr($displaysectionname, 0,
                                    $this->settings['sectiontitletraillengthmaxoption']) . '...';
                        }
                    }
                }
                $sectiontitleclass = 'icon_content';
                if ($this->settings['sectiontitleboxposition'] == 1) {
                    // Only bother if there is a section name to show.
                    $canshow = false;
                    $sectionnamelen = core_text::strlen($displaysectionname);
                    if (($sectionnamelen !== false) && ($sectionnamelen > 0)) {
                        if ($sectionnamelen == 1) {
                            if ($displaysectionname[0] != ' ') {
                                $canshow = true;
                            }
                        } else {
                            $canshow = true;
                        }
                    }
                    if ($canshow) {
                        $sectiontitleclass .= ' content_inside';
                        if ($this->settings['sectiontitleboxinsideposition'] == 2) {
                            $sectiontitleclass .= ' middle';
                        } else if ($this->settings['sectiontitleboxinsideposition'] == 3) {
                            $sectiontitleclass .= ' bottom';
                        }
                    }
                }

                $sectiontitleattribues['id'] = 'trailsectionname-' . $thissection->section;
                $sectiontitleattribues['class'] = $sectiontitleclass;
                if ($this->settings['showsectiontitlesummary'] == 2) {
                    $summary = strip_tags($thissection->summary);
                    $summary = str_replace("&nbsp;", ' ', $summary);
                    $summarylen = core_text::strlen($summary);
                    if ($summarylen > 0) {
                        if ($this->settings['sectiontitlesummarymaxlength'] != 0) {
                            if ($summarylen > $this->settings['sectiontitlesummarymaxlength']) {
                                $summary = core_text::substr($summary, 0, $this->settings['sectiontitlesummarymaxlength']) . '...';
                            }
                        }
                        $sectiontitleattribues['title'] = $summary;
                        $sectiontitleattribues['data-toggle'] = 'trailtooltip';
                        $sectiontitleattribues['data-placement'] =
                                $this->courseformat->get_set_show_section_title_summary_position();
                    }
                }

                /* Roles info on based on: http://www.w3.org/TR/wai-aria/roles.
                  Looked into the 'trail' role but that requires 'row' before 'trailcell' and there are none as the trail
                  is responsive, so as the container is a 'navigation' then need to look into converting the containing
                  'div' to a 'nav' tag (www.w3.org/TR/2010/WD-html5-20100624/sections.html#the-nav-element) when I'm
                  that all browsers support it against the browser requirements of Moodle. */
                $liattributes = array(
                    'role' => 'region',
                    'aria-labelledby' => 'trailsectionname-' . $thissection->section
                );
                if ($this->courseformat->is_section_current($section)) {
                    $liattributes['class'] = 'currenticon';
                }
                if (!empty($summary)) {
                    $liattributes['aria-describedby'] = 'trailsectionsummary-' . $thissection->section;
                }
                // Contribuition adriano515.
                if ($count == 3) {
                    echo html_writer::end_tag('ul');
                    echo html_writer::start_tag('ul', array('class' => 'trailicons impar trailcursor'));
                } else if ($count == 4) {
                    echo html_writer::end_tag('ul');
                    echo html_writer::start_tag('ul', array('class' => 'trailicons par trailcursor'));
                } else if ($count >= 5) {
                    $count = 2;
                }
                $count ++;

                echo html_writer::start_tag('li', $liattributes);

                // Ensure the record exists.
                if (($sectionimages === false) || (!array_key_exists($thissection->id, $sectionimages))) {
                    // Method get_image has 'repair' functionality for when there are issues with the data.
                    $sectionimage = $this->courseformat->get_image($course->id, $thissection->id);
                } else {
                    $sectionimage = $sectionimages[$thissection->id];
                }

                // If the image is set then check that displayedimageindex is greater than 0 otherwise create the displayed image.
                // This is a catch-all for existing courses.
                if (isset($sectionimage->image) && ($sectionimage->displayedimageindex < 1)) {
                    // Set up the displayed image:...
                    $sectionimage->newimage = $sectionimage->image;
                    $icbc = $this->courseformat->hex2rgb($this->settings['imagecontainerbackgroundcolour']);
                    $sectionimage = $this->courseformat->setup_displayed_image($sectionimage, $contextid, $this->settings, $icbc);
                }

                if ($course->coursedisplay != COURSE_DISPLAY_MULTIPAGE) {
                    if (($editing) && ($section == 0)) {
                        $this->make_block_icon_topic0_editing($course);
                    }

                    echo html_writer::start_tag('a', array(
                        'href' => '#section-' . $thissection->section,
                        'id' => 'trailsection-' . $thissection->section,
                        'class' => 'trailicon_link',
                        'role' => 'link')
                    );

                    if ($this->settings['sectiontitleboxposition'] == 2) {
                        echo html_writer::tag('div', $displaysectionname, $sectiontitleattribues);
                    }

                    if (($this->settings['newactivity'] == 2) && (isset($sectionupdated[$thissection->id]))) {
                        // The section has been updated since the user last visited this course, add NEW label.
                        echo html_writer::empty_tag('img', array(
                            'class' => 'new_activity',
                            'src' => $urlpicnewactivity,
                            'alt' => ''));
                    }

                    $imageclass = 'image_holder';
                    if ($sectiongreyedout) {
                        $imageclass .= ' inaccessible';
                    }
                    echo html_writer::start_tag('div', array('class' => $imageclass));
                    // Alterado por Jota.
                    if ($this->is_check_section($USER->id, $course->id, $thissection->id)) {
                        $classcheck = '';
                        $titlecheck = get_string('checked', 'format_trail');
                        if ($this->settings['showcheckstar'] == 2) {
                            $classcheck = 'check';
                        }
                        if ($this->settings['showcheckstar'] == 3) {
                            $classcheck = 'star';
                        }
                        if ($this->settings['showcheckstar'] == 4) {
                            $classcheck = 'like';
                        }
                        echo html_writer::start_tag('div', array('id' => $classcheck, 'title' => $titlecheck));
                        echo html_writer::end_tag('div');
                    } else {
                        // Alteração por Jota.
                        $bloqueado = 0;
                        if ($this->settings['hidesectionlock'] == 3 || $this->settings['hidesectionlock'] == 4) {
                            $bloqueado = $this->get_section_availability_bloqueado($thissection,
                                    has_capability('moodle/course:viewhiddensections', $coursecontext));
                        }
                        if ($bloqueado > 0) {
                            $cssid = 'lock';
                            if ($this->settings['hidesectionlock'] == 4) {
                                $cssid = 'lock_treasure';
                            }
                            echo html_writer::start_tag('div', array('id' => $cssid,
                                'title' => get_string('locked', 'format_trail')));
                            echo html_writer::end_tag('div');
                        }
                    }

                    if ($this->settings['sectiontitleboxposition'] == 1) {
                        echo html_writer::tag('div', $displaysectionname, $sectiontitleattribues);
                    }

                    if (!empty($summary)) {
                        echo html_writer::tag('div', '', array('id' => 'trailsectionsummary-' . $thissection->section,
                            'hidden' => true, 'aria-label' => $summary));
                    }
                    // Alteração por Jota.
                    $bloqueado = 0;
                    if ($this->settings['hidesectionlock'] == 2) {
                        $bloqueado = $this->get_section_availability_bloqueado($thissection,
                                has_capability('moodle/course:viewhiddensections', $coursecontext));
                    }
                    echo $this->courseformat->output_section_image($section, $sectionname, $sectionimage,
                            $contextid, $thissection, $trailimagepath, $this->output, $bloqueado);

                    echo html_writer::end_tag('div');
                    echo html_writer::end_tag('a');

                    if ($editing) {
                        $this->make_block_icon_topics_editing($thissection, $contextid, $urlpicedit);
                    }
                    echo html_writer::end_tag('li');
                } else {
                    $content = '';
                    if ($this->settings['sectiontitleboxposition'] == 2) {
                        $content .= html_writer::tag('div', $displaysectionname, $sectiontitleattribues);
                    }

                    if (($this->settings['newactivity'] == 2) && (isset($sectionupdated[$thissection->id]))) {
                        $content .= html_writer::empty_tag('img', array(
                                    'class' => 'new_activity',
                                    'src' => $urlpicnewactivity));
                    }

                    // Grey out code: Justin 2016/05/14.
                    $imageclass = 'image_holder';
                    if ($sectiongreyedout) {
                        $imageclass .= ' inaccessible';
                    }
                    $content .= html_writer::start_tag('div', array('class' => $imageclass));

                    if ($this->settings['sectiontitleboxposition'] == 1) {
                        $content .= html_writer::tag('div', $displaysectionname, $sectiontitleattribues);
                    }

                    if (!empty($summary)) {
                        $content .= html_writer::tag('div', '', array('id' => 'trailsectionsummary-' . $thissection->section,
                                    'hidden' => true, 'aria-label' => $summary));
                    }

                    $content .= $this->courseformat->output_section_image(
                            $section, $sectionname, $sectionimage, $contextid, $thissection, $trailimagepath, $this->output);

                    $content .= html_writer::end_tag('div');

                    if ($editing) {
                        if ($section == 0) {
                            $this->make_block_icon_topic0_editing($course);
                        }
                        // Section greyed out by Justin 2016/05/14.
                        if (!$sectiongreyedout) {
                            echo html_writer::link($singlepageurl . '#section-' . $thissection->section, $content, array(
                                'id' => 'trailsection-' . $thissection->section,
                                'class' => 'trailicon_link',
                                'role' => 'link'));
                        } else {
                            // Need an enclosing 'span' for IE.
                            echo html_writer::tag('span', $content);
                        }
                        $this->make_block_icon_topics_editing($thissection, $contextid, $urlpicedit);
                    } else {
                        if (!$sectiongreyedout) {
                            echo html_writer::link($singlepageurl . '&section=' . $thissection->section, $content, array(
                                'id' => 'trailsection-' . $thissection->section,
                                'class' => 'trailicon_link',
                                'role' => 'link'));
                        } else {
                            // Need an enclosing 'span' for IE.
                            echo html_writer::tag('span', $content);
                        }
                    }
                    echo html_writer::end_tag('li');
                }
            } else {
                // We now know the value for the trail shade box shown array.
                $this->shadeboxshownarray[$section] = 1;
            }
        }
    }

    /**
     * Validates the font size that was entered by the user.
     *
     * @param string $userid the font size integer to validate.
     * @param string $courseid the font size integer to validate.
     * @param string $sectionid the font size integer to validate.
     * @return true|false
     */
    private function is_check_section($userid, $courseid, $sectionid) {
        global $DB;
        $countatvok = $DB->get_record_sql("SELECT COUNT(c.id) AS total FROM {course_modules_completion} c"
                . " INNER JOIN {course_modules} m ON c.coursemoduleid = m.id WHERE c.userid="
                . $userid . " AND m.course=" . $courseid . " AND m.section="
                . $sectionid . " AND m.completion > 0 AND c.completionstate > 0 AND m.deletioninprogress = 0");
        $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        $wheregroup = "";
        // Verification group mode.
        if ($course->groupmode >= 1) {
            $groups = \groups_get_user_groups($courseid, $userid);
            $iswhere = false;
            foreach ($groups as $group) {
                $countgroups = count($group);
                for ($i = 0; $i <= $countgroups; $i++) {
                    if ($wheregroup == "" && isset($group[$i])) {
                        $wheregroup = 'AND (availability IS NULL  OR availability NOT LIKE \'%{"type":"group"%\'';
                        $iswhere = true;
                    }
                    if (isset($group[$i])) {
                        $wheregroup .= ' OR availability LIKE \'%{"type":"group","id":' . $group[$i] . '}%\' ';
                    }
                }
            }
            if ($iswhere) {
                $wheregroup .= ') ';
            }
        }
        $sql = "SELECT COUNT(id) AS total FROM {course_modules} WHERE course="
                . $courseid . " AND section=" . $sectionid . " AND completion > 0 AND deletioninprogress = 0 " . $wheregroup;
        $countatv = $DB->get_record_sql($sql);
        if ($countatvok->total == $countatv->total && $countatv->total != 0) {
            return true;
        }
        return false;
    }

    /**
     * Make topics icons.
     *
     * @param \stdClass $thissection
     * @param int $contextid
     * @param string $urlpicedit
     * @return none
     */
    private function make_block_icon_topics_editing($thissection, $contextid, $urlpicedit) {
        global $USER;

        $streditimage = get_string('editimage', 'format_trail');
        $streditimagealt = get_string('editimage_alt', 'format_trail');

        echo html_writer::link(
                $this->courseformat->trail_moodle_url('editimage.php', array(
                    'sectionid' => $thissection->id,
                    'contextid' => $contextid,
                    'userid' => $USER->id,
                    'role' => 'link',
                    'aria-label' => $streditimagealt)
                ), html_writer::empty_tag('img', array(
                    'src' => $urlpicedit,
                    'alt' => $streditimagealt,
                    'role' => 'img',
                    'aria-label' => $streditimagealt)) . '&nbsp;' . $streditimage, array('title' => $streditimagealt)
        );
    }

    /**
     * Make topic0 icons.
     *
     * @param \stdClass $course
     * @return none
     */
    private function make_block_icon_topic0_editing($course) {
        $strdisplaysummary = get_string('display_summary', 'format_trail');
        $strdisplaysummaryalt = get_string('display_summary_alt', 'format_trail');

        echo html_writer::link(
                $this->courseformat->trail_moodle_url('mod_summary.php', array(
                    'sesskey' => sesskey(),
                    'course' => $course->id,
                    'showsummary' => 1,
                    'role' => 'link',
                    'aria-label' => $strdisplaysummaryalt)
                ), html_writer::empty_tag('img', array(
                    'src' => $this->output->image_url('out_of_trail', 'format_trail'),
                    'alt' => $strdisplaysummaryalt,
                    'role' => 'img',
                    'aria-label' => $strdisplaysummaryalt)) . '&nbsp;' . $strdisplaysummary, array('title' => $strdisplaysummaryalt)
        );
    }

    /**
     * Make if currently moving a file then show the current clipboard..
     *
     * @param \stdClass $course
     * @return none
     */
    private function make_block_show_clipboard_if_file_moving($course) {
        global $USER;

        if (is_object($course) && ismoving($course->id)) {
            $strcancel = get_string('cancel');

            $stractivityclipboard = clean_param(format_string(
                            get_string('activityclipboard', '', $USER->activitycopyname)), PARAM_NOTAGS);
            $stractivityclipboard .= '&nbsp;&nbsp;('
                    . html_writer::link(new moodle_url('/mod.php', array(
                        'cancelcopy' => 'true',
                        'sesskey' => sesskey())), $strcancel);

            echo html_writer::tag('li', $stractivityclipboard, array('class' => 'clipboard'));
        }
    }

    /**
     * Make topics block.
     *
     * @param \stdClass $course
     * @param array $sections
     * @param string $modinfo
     * @param string $editing
     * @param string $hascapvishidsect
     * @param string $streditsummary
     * @param string $urlpicedit
     * @param string $onsectionpage
     * @return none
     */
    private function make_block_topics($course, $sections, $modinfo, $editing,
            $hascapvishidsect, $streditsummary, $urlpicedit, $onsectionpage) {
        $coursecontext = context_course::instance($course->id);
        unset($sections[0]);

        $coursenumsections = $this->courseformat->get_last_section_number();

        foreach ($sections as $section => $thissection) {
            if (!$hascapvishidsect && !$thissection->visible && $course->hiddensections) {
                unset($sections[$section]);
                continue;
            }
            if ($section > $coursenumsections) {
                // Orphaned section.
                continue;
            }

            $sectionstyle = 'section main';
            if (!$thissection->visible) {
                $sectionstyle .= ' hidden';
            }
            if ($this->courseformat->is_section_current($section)) {
                $sectionstyle .= ' current';
            }
            $sectionstyle .= ' trail_section hide_section';

            $sectionname = $this->courseformat->get_section_name($thissection);
            if ($editing) {
                $title = $this->section_title($thissection, $course);
            } else {
                $title = $sectionname;
            }
            echo html_writer::start_tag('li', array(
                'id' => 'section-' . $section,
                'class' => $sectionstyle,
                'role' => 'region',
                'aria-label' => $sectionname)
            );

            if ($editing) {
                // Note, 'left side' is BEFORE content.
                $leftcontent = $this->section_left_content($thissection, $course, $onsectionpage);
                echo html_writer::tag('div', $leftcontent, array('class' => 'left side'));
                // Note, 'right side' is BEFORE content.
                $rightcontent = $this->section_right_content($thissection, $course, $onsectionpage);
                echo html_writer::tag('div', $rightcontent, array('class' => 'right side'));
            }

            echo html_writer::start_tag('div', array('class' => 'content'));
            if ($hascapvishidsect || ($thissection->visible && $thissection->available)) {
                // If visible.
                echo $this->output->heading($title, 3, 'sectionname');

                echo html_writer::start_tag('div', array('class' => 'summary'));

                echo $this->format_summary_text($thissection);

                if ($editing) {
                    echo html_writer::link(
                            new moodle_url('editsection.php',
                                    array('id' => $thissection->id)),
                            html_writer::empty_tag('img', array('src' => $urlpicedit, 'alt' => $streditsummary,
                                'class' => 'iconsmall edit')), array('title' => $streditsummary));
                }
                echo html_writer::end_tag('div');

                echo $this->section_availability_message($thissection,
                        has_capability('moodle/course:viewhiddensections', $coursecontext));

                echo $this->course_section_cm_list($course, $thissection, 0);
                echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
            } else {
                echo html_writer::tag('h2', $this->get_title($thissection));
                echo html_writer::tag('p', get_string('hidden_topic', 'format_trail'));

                echo $this->section_availability_message($thissection,
                        has_capability('moodle/course:viewhiddensections', $coursecontext));
            }

            echo html_writer::end_tag('div');
            echo html_writer::end_tag('li');

            unset($sections[$section]);
        }

        if ($editing) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $coursenumsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo $this->change_number_sections($course, 0);
        } else {
            echo $this->end_section_list();
        }
    }

    /**
     * Attempts to return a 40 character title for the section image container.
     *
     * @param \stdClass $section
     * @return string
     */
    private function get_title($section) {
        $title = is_object($section) && isset($section->name) &&
                is_string($section->name) ? trim($section->name) : '';

        if (!empty($title)) {
            // Apply filters and clean tags.
            $title = trim(format_string($section->name, true));
        }

        if (empty($title)) {
            $title = trim(format_text($section->summary));

            // Finds first header content. If it is not found, then try to find the first paragraph.
            foreach (array('h[1-6]', 'p') as $tag) {
                if (preg_match('#<(' . $tag . ')\b[^>]*>(?P<text>.*?)</\1>#si', $title, $m)) {
                    if (!$this->is_empty_text($m['text'])) {
                        $title = $m['text'];
                        break;
                    }
                }
            }
            $title = trim(clean_param($title, PARAM_NOTAGS));
        }

        if (core_text::strlen($title) > 40) {
            $title = $this->text_limit($title, 40);
        }

        return $title;
    }

    /**
     * States if the text is empty.
     * @param string $text The text to test.
     * @return boolean Yes(true) or No(false).
     */
    public function is_empty_text($text) {
        return empty($text) ||
                preg_match('/^(?:\s|&nbsp;)*$/si', htmlentities($text, 0 /* ENT_HTML401 */, 'UTF-8', true));
    }

    /**
     * Cuts long texts up to certain length without breaking words.
     *
     * @param string $text
     * @param int $length
     * @param string $replacer
     * @return string
     */
    private function text_limit($text, $length, $replacer = '...') {
        if (core_text::strlen($text) > $length) {
            $text = wordwrap($text, $length, "\n", true);
            $pos = strpos($text, "\n");
            if ($pos === false) {
                $pos = $length;
            }
            $text = trim(core_text::substr($text, 0, $pos)) . $replacer;
        }
        return $text;
    }

    /**
     * Checks whether there has been new activity.
     *
     * @param \stdClass $course
     * @return string
     */
    private function new_activity($course) {
        global $CFG, $USER, $DB;

        $sectionsedited = array();
        if (isset($USER->lastcourseaccess[$course->id])) {
            $course->lastaccess = $USER->lastcourseaccess[$course->id];
        } else {
            $course->lastaccess = 0;
        }

        $sql = "SELECT id, section FROM {$CFG->prefix}course_modules " .
                "WHERE course = :courseid AND added > :lastaccess";

        $params = array(
            'courseid' => $course->id,
            'lastaccess' => $course->lastaccess);

        $activity = $DB->get_records_sql($sql, $params);
        foreach ($activity as $record) {
            $sectionsedited[$record->section] = true;
        }

        return $sectionsedited;
    }

    /**
     * Checks whether there has been new activity.
     *
     * @param string $portable
     * @return none
     */
    public function set_portable($portable) {
        $this->portable = $portable;
    }

    /**
     * Checks whether there has been new activity.
     *
     * @param string $initialsection
     * @return none
     */
    public function set_initialsection($initialsection) {
        $this->initialsection = $initialsection;
    }

    /**
     * If section is not visible, display the message about that ('Not available
     * until...', that sort of thing). Otherwise, returns blank.
     *
     * For users with the ability to view hidden sections, it shows the
     * information even though you can view the section and also may include
     * slightly fuller information (so that teachers can tell when sections
     * are going to be unavailable etc). This logic is the same as for
     * activities.
     *
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return string HTML to output
     */
    protected function get_section_availability_bloqueado($section, $canviewhidden) {
        global $CFG;
        $o = 0;
        if (!$section->uservisible) {
            if ($section->availableinfo) {
                $o = 1;
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new \core_availability\info_section($section);
            $fullinfo = $ci->get_full_information();
            if ($fullinfo) {
                $o = 1;
            }
        }
        return $o;
    }

    /**
     * Returns controls in the bottom of the page to increase/decrease number of sections
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $course
     * @param int|null $sectionreturn
     */
    protected function change_number_sections($course, $sectionreturn = null) {

        $format = course_get_format($course);
        if ($sectionreturn) {
            $format->set_section_number($sectionreturn);
        }
        $outputclass = $format->get_output_classname('content\\addsection');
        $widget = new $outputclass($format);
        echo $this->render($widget);
    }

    /**
     * Generate html for a section summary text
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {

        $format = course_get_format($section->course);
        if (!($section instanceof section_info)) {
            $modinfo = $format->get_modinfo();
            $section = $modinfo->get_section_info($section->section);
        }
        $summaryclass = $format->get_output_classname('content\\section\\summary');
        $summary = new $summaryclass($format, $section);
        return $summary->format_summary_text();
    }

    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This function calls core_course_renderer::course_section_cm().
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */
    public function course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = []) {
        global $USER;

        $output = '';

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();

        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // Check if we are currently in the process of moving a module with JavaScript disabled.
        $ismoving = $format->show_editor() && ismoving($course->id);

        if ($ismoving) {
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one).
        $moduleshtml = [];
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // Do not display moving mod.
                    continue;
                }

                if ($modulehtml = $this->course_section_cm_list_item($course,
                        $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li', html_writer::link($movingurl, '',
                            array('title' => $strmovefull, 'class' => 'movehere')),
                            array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li', html_writer::link($movingurl, '',
                        array('title' => $strmovefull, 'class' => 'movehere')),
                        array('class' => 'movehere'));
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));

        return $output;
    }

    /**
     * Renders HTML to display one course module for display within a section.
     *
     * @deprecated since 4.0 - use core_course output components or course_format::course_section_updated_cm_item instead.
     *
     * This function calls: core_course_renderer::course_section_cm().
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return String
     */
    public function course_section_cm_list_item($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = []) {

        $output = '';
        if ($modulehtml = $this->course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {
            $modclasses = 'activity ' . $mod->modname . ' modtype_' . $mod->modname . ' ' . $mod->extraclasses;
            $output .= html_writer::tag('li', $modulehtml, array('class' => $modclasses, 'id' => 'module-' . $mod->id));
        }
        return $output;
    }

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = []) {

        if (!$mod->is_visible_on_course_page()) {
            return '';
        }

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        // Output renderers works only with real section_info objects.
        if ($sectionreturn) {
            $format->set_section_number($sectionreturn);
        }
        $section = $modinfo->get_section_info($format->get_section_number());

        $cmclass = $format->get_output_classname('content\\cm');
        $cm = new $cmclass($format, $section, $mod, $displayoptions);
        // The course outputs works with format renderers, not with course renderers.
        $renderer = $format->get_renderer($this->page);
        $data = $cm->export_for_template($renderer);
        return $this->output->render_from_template('core_courseformat/local/content/cm', $data);
    }

    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage) {
        $o = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o = get_accesshide(get_string('currentsection', 'format_' . $course->format));
            }
        }

        return $o;
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage) {
        $o = $this->output->spacer();

        $controls = $this->section_edit_control_items($course, $section, $onsectionpage);
        $o .= $this->section_edit_control_menu($controls, $course, $section);

        return $o;
    }

    /**
     * Generate the edit control action menu
     *
     * @param array $controls The edit control items from section_edit_control_items
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function section_edit_control_menu($controls, $course, $section) {
        $o = "";
        if (!empty($controls)) {
            $menu = new action_menu();
            $menu->set_menu_trigger(get_string('edit'));
            $menu->attributes['class'] .= ' section-actions';
            foreach ($controls as $value) {
                $url = empty($value['url']) ? '' : $value['url'];
                $icon = empty($value['icon']) ? '' : $value['icon'];
                $name = empty($value['name']) ? '' : $value['name'];
                $attr = empty($value['attr']) ? array() : $value['attr'];
                $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
                $al = new action_menu_link_secondary(
                        new moodle_url($url), new pix_icon($icon, '', null, array('class' => "smallicon " . $class)), $name, $attr
                );
                $menu->add($al);
            }

            $o .= html_writer::div($this->render($menu), 'section_action_menu', array('data-sectionid' => $section->id));
        }

        return $o;
    }

    /**
     * Generate the edit control items of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of edit control items
     */
    protected function section_edit_control_items_orig($course, $section, $onsectionpage = false) {
        if (!$this->page->user_is_editing()) {
            return array();
        }

        $sectionreturn = $onsectionpage ? $section->section : null;

        $coursecontext = context_course::instance($course->id);
        $numsections = course_get_format($course)->get_last_section_number();
        $isstealth = $section->section > $numsections;

        $baseurl = course_get_url($course, $sectionreturn);
        $baseurl->param('sesskey', sesskey());

        $controls = array();

        if (!$isstealth && has_capability('moodle/course:update', $coursecontext)) {
            if ($section->section > 0 && get_string_manager()->string_exists('editsection', 'format_' . $course->format)) {
                $streditsection = get_string('editsection', 'format_' . $course->format);
            } else {
                $streditsection = get_string('editsection');
            }

            $controls['edit'] = array(
                'url' => new moodle_url('/course/editsection.php', array('id' => $section->id, 'sr' => $sectionreturn)),
                'icon' => 'i/settings',
                'name' => $streditsection,
                'pixattr' => array('class' => ''),
                'attr' => array('class' => 'icon edit'));
        }

        if ($section->section) {
            $url = clone($baseurl);
            if (!$isstealth) {
                if (has_capability('moodle/course:sectionvisibility', $coursecontext)) {
                    if ($section->visible) { // Show the hide/show eye.
                        $strhidefromothers = get_string('hidefromothers', 'format_' . $course->format);
                        $url->param('hide', $section->section);
                        $controls['visiblity'] = array(
                            'url' => $url,
                            'icon' => 'i/hide',
                            'name' => $strhidefromothers,
                            'pixattr' => array('class' => ''),
                            'attr' => array('class' => 'icon editing_showhide',
                                'data-sectionreturn' => $sectionreturn, 'data-action' => 'hide'));
                    } else {
                        $strshowfromothers = get_string('showfromothers', 'format_' . $course->format);
                        $url->param('show', $section->section);
                        $controls['visiblity'] = array(
                            'url' => $url,
                            'icon' => 'i/show',
                            'name' => $strshowfromothers,
                            'pixattr' => array('class' => ''),
                            'attr' => array('class' => 'icon editing_showhide',
                                'data-sectionreturn' => $sectionreturn, 'data-action' => 'show'));
                    }
                }

                if (!$onsectionpage) {
                    if (has_capability('moodle/course:movesections', $coursecontext)) {
                        $url = clone($baseurl);
                        if ($section->section > 1) { // Add a arrow to move section up.
                            $url->param('section', $section->section);
                            $url->param('move', -1);
                            $strmoveup = get_string('moveup');
                            $controls['moveup'] = array(
                                'url' => $url,
                                'icon' => 'i/up',
                                'name' => $strmoveup,
                                'pixattr' => array('class' => ''),
                                'attr' => array('class' => 'icon moveup'));
                        }

                        $url = clone($baseurl);
                        if ($section->section < $numsections) { // Add a arrow to move section down.
                            $url->param('section', $section->section);
                            $url->param('move', 1);
                            $strmovedown = get_string('movedown');
                            $controls['movedown'] = array(
                                'url' => $url,
                                'icon' => 'i/down',
                                'name' => $strmovedown,
                                'pixattr' => array('class' => ''),
                                'attr' => array('class' => 'icon movedown'));
                        }
                    }
                }
            }

            if (course_can_delete_section($course, $section)) {
                if (get_string_manager()->string_exists('deletesection', 'format_' . $course->format)) {
                    $strdelete = get_string('deletesection', 'format_' . $course->format);
                } else {
                    $strdelete = get_string('deletesection');
                }
                $url = new moodle_url('/course/editsection.php', array(
                    'id' => $section->id,
                    'sr' => $sectionreturn,
                    'delete' => 1,
                    'sesskey' => sesskey()));
                $controls['delete'] = array(
                    'url' => $url,
                    'icon' => 'i/delete',
                    'name' => $strdelete,
                    'pixattr' => array('class' => ''),
                    'attr' => array('class' => 'icon editing_delete'));
            }
        }

        return $controls;
    }

    /**
     * If section is not visible, display the message about that ('Not available
     * until...', that sort of thing). Otherwise, returns blank.
     *
     * For users with the ability to view hidden sections, it shows the
     * information even though you can view the section and also may include
     * slightly fuller information (so that teachers can tell when sections
     * are going to be unavailable etc). This logic is the same as for
     * activities.
     *
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return string HTML to output
     */
    protected function section_availability_message($section, $canviewhidden) {
        global $CFG;
        $o = '';

        if (!$section->visible) {
            if ($canviewhidden) {
                $o .= $this->availability_info(get_string('hiddenfromstudents'), 'ishidden');
            } else {
                // We are here because of the setting "Hidden sections are shown in collapsed form".
                // Student can not see the section contents but can see its name.
                $o .= $this->availability_info(get_string('notavailable'), 'ishidden');
            }
        } else if (!$section->uservisible) {
            if ($section->availableinfo) {
                // Note: We only get to this function if availableinfo is non-empty,
                // so there is definitely something to print.
                $formattedinfo = info::format_info(
                                $section->availableinfo, $section->course);
                $o .= $this->availability_info($formattedinfo, 'isrestricted');
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new info_section($section);
            $fullinfo = $ci->get_full_information();
            if ($fullinfo) {
                $formattedinfo = info::format_info(
                                $fullinfo, $section->course);
                $o .= $this->availability_info($formattedinfo, 'isrestricted isfullinfo');
            }
        }
        return $o;
    }

    /**
     * Displays availability info for a course section or course module
     *
     * @param string $text
     * @param string $additionalclasses
     * @return string
     */
    public function availability_info($text, $additionalclasses = '') {

        $data = ['text' => $text, 'classes' => $additionalclasses];
        $additionalclasses = array_filter(explode(' ', $additionalclasses));

        if (in_array('ishidden', $additionalclasses)) {
            $data['ishidden'] = 1;
        } else if (in_array('isstealth', $additionalclasses)) {
            $data['isstealth'] = 1;
        } else if (in_array('isrestricted', $additionalclasses)) {
            $data['isrestricted'] = 1;

            if (in_array('isfullinfo', $additionalclasses)) {
                $data['isfullinfo'] = 1;
            }
        }

        $datax = (object) [
                    'info' => $data,
                    'hasavailability' => !empty($data),
        ];

        return $this->render_from_template('format_trail/availability', $datax);
    }

}
