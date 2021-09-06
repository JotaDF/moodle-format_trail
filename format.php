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
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

// Horrible backwards compatible parameter aliasing..
if ($topic = optional_param('topic', 0, PARAM_INT)) { // Topics and Trail old section parameter.
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic / trail param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($ctopic = optional_param('ctopics', 0, PARAM_INT)) { // Collapsed Topics old section parameter.
    $url = $PAGE->url;
    $url->param('section', $ctopic);
    debugging('Outdated collapsed topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($week = optional_param('week', 0, PARAM_INT)) { // Weeks old section parameter.
    $url = $PAGE->url;
    $url->param('section', $week);
    debugging('Outdated week param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing..

$coursecontext = context_course::instance($course->id);

// Retrieve course format option fields and add them to the $course object.
$courseformat = course_get_format($course);
$course = $courseformat->get_course();

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $coursecontext) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure all sections are created.
course_create_sections_if_missing($course, range(0, $course->numsections));

$renderer = $PAGE->get_renderer('format_trail');

$devicetype = core_useragent::get_device_type(); // In /lib/classes/useragent.php.
if ($devicetype == "mobile") {
    $portable = 1;
} else if ($devicetype == "tablet") {
    $portable = 2;
} else {
    $portable = 0;
}
$renderer->set_portable($portable);

$gfsettings = $courseformat->get_settings();
$imageproperties = $courseformat->calculate_image_container_properties(
        $gfsettings['imagecontainerwidth'], $gfsettings['imagecontainerratio'], $gfsettings['borderwidth']);

echo '<style type="text/css" media="screen">';
echo '/* <![CDATA[ */';
echo ' #trailiconcontainer  ul.trailicons { position: relative;
    width: 565px;
    height: 200px;
    background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/trilha'.$gfsettings['showbackground'].'_topo.png");
    background-repeat: no-repeat; ';
$imagecontaineralignment = $gfsettings['imagecontaineralignment'];
if ($imagecontaineralignment == 'left') {
    $imagecontaineralignment = 'flex-start';
} else if ($imagecontaineralignment == 'right') {
    $imagecontaineralignment = 'flex-end';
}
echo 'justify-content: ' . $imagecontaineralignment . ';';
echo '} ';
echo '#lock {
        z-index: 5;
        position:absolute;
        top: 0px;
        right: 1px;
        width: 32px;
        height: 32px;
        background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/lock_mini.png");
        background-repeat: no-repeat;
    }';
echo '#lock_treasure {
        z-index: 5;
        position:absolute;
        top: 0px;
        right: 1px;
        width: 32px;
        height: 32px;
        background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/lock_mini_tesouro.png");
        background-repeat: no-repeat;
    }';
echo '#check {
        z-index: 5;
        position:absolute;
        top: 0px;
        right: 1px;
        width: 32px;
        height: 32px;
        background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/check.png");
        background-repeat: no-repeat;
    }';
echo '#star {
        z-index: 5;
        position:absolute;
        top: 0px;
        right: 1px;
        width: 32px;
        height: 32px;
        background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/star.png");
        background-repeat: no-repeat;
    }';
echo '#like {
        z-index: 5;
        position:absolute;
        top: 0px;
        right: 1px;
        width: 32px;
        height: 32px;
        background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/like.png");
        background-repeat: no-repeat;
    }';
echo '#trailiconcontainer  ul.impar { position: relative;
    width: 565px;
    height: 200px;
    background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/trilha'.$gfsettings['showbackground'].'_meio_impar.png");
    background-repeat: no-repeat; ';
echo 'justify-content: ' . $imagecontaineralignment . ';';
echo '} ';
echo '#trailiconcontainer  ul.par { position: relative;
    width: 565px;
    height: 200px;
    background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/trilha'.$gfsettings['showbackground'].'_meio_par.png");
    background-repeat: no-repeat; ';
echo 'justify-content: ' . $imagecontaineralignment . ';';
echo '}';
echo '#trailiconcontainer ul.trailicons li {
	padding: 0px;
	margin: 20px;
     }';
echo '.course-content ul.trailicons li .icon_content {';
if ($gfsettings['sectiontitlefontsize']) { // Font size is set.
    echo 'font-size: ' . $gfsettings['sectiontitlefontsize'] . 'px;';
    if (($gfsettings['sectiontitlefontsize'] + 4) > 20) {
        echo 'height: ' . ($gfsettings['sectiontitlefontsize'] + 4) . 'px;';
    }
}
echo 'text-align: ' . $gfsettings['sectiontitlealignment'] . ';';
if ($gfsettings['sectiontitleboxposition'] == 1) { // Inside.
    echo 'width: ' . ($gfsettings['imagecontainerwidth'] - 20) . 'px;';
} else {
    echo 'width: ' . ($gfsettings['imagecontainerwidth'] + ($gfsettings['borderwidth'] * 2)) . 'px;';
}
echo '}';
echo '.course-content ul.trailicons li .image_holder {';
echo 'width: ' . $gfsettings['imagecontainerwidth'] . 'px;';
echo 'height: ' . $imageproperties['height'] . 'px;';
echo 'border-color: ';
if ($gfsettings['bordercolour'][0] != '#') {
    echo '#';
}
echo $gfsettings['bordercolour'] . ';';

if ($gfsettings['imagecontainerbackgroundcolour'] != '999999'
        && $gfsettings['imagecontainerbackgroundcolour'] != '#999999') {
    echo 'background-color: ';
    if ($gfsettings['imagecontainerbackgroundcolour'][0] != '#') {
        echo '#';
    }
    echo $gfsettings['imagecontainerbackgroundcolour'] . ';';
}

echo 'border-width: ' . $gfsettings['borderwidth'];
if ($gfsettings['borderwidth']) {
    echo 'px';
}
echo ';';
if ($gfsettings['borderradius'] == 2) { // On.
    echo 'border-radius: ' . $gfsettings['borderwidth'];
    if ($gfsettings['borderwidth']) {
        echo 'px';
    }
    echo ';';
}
echo '}';

$startindex = 0;
if ($gfsettings['bordercolour'][0] == '#') {
    $startindex++;
}
$red = hexdec(substr($gfsettings['bordercolour'], $startindex, 2));
$green = hexdec(substr($gfsettings['bordercolour'], $startindex + 2, 2));
$blue = hexdec(substr($gfsettings['bordercolour'], $startindex + 4, 2));

echo '.course-content ul.trailicons li:hover .image_holder {';
echo 'box-shadow: 0 0 0 ' . $gfsettings['borderwidth'];
if ($gfsettings['borderwidth']) {
    echo 'px';
}
echo ' rgba(' . $red . ',' . $green . ',' . $blue . ', 0.3);';
echo '}';

echo '.course-content ul.trailicons li.currenticon .image_holder {';
echo 'box-shadow: 0 0 2px 4px ';
if ($gfsettings['currentselectedsectioncolour'][0] != '#') {
    echo '#';
}
echo $gfsettings['currentselectedsectioncolour'] . ';';
echo '}';

echo '.course-content ul.trailicons li.currentselected {';
if ($gfsettings['currentselectedimagecontainercolour'] == '999999'
        || $gfsettings['currentselectedimagecontainercolour'] == '#999999') {
    echo 'background-color: rgba(12,11,44,0.2); border-radius: 15px;}';
} else {
    echo 'background-color: ';
    if ($gfsettings['currentselectedimagecontainercolour'][0] != '#') {
        echo '#';
    }
    echo $gfsettings['currentselectedimagecontainercolour'] . ';';
    echo '}';
}
if ($gfsettings['sectiontitleboxposition'] == 1) { // Inside.
    echo '.course-content ul.trailicons li .icon_content.content_inside {';
    echo 'background-color: ';
    if ($gfsettings['sectiontitleinsidetitlebackgroundcolour'][0] != '#') {
        echo '#';
    }
    echo $gfsettings['sectiontitleinsidetitlebackgroundcolour'] . ';';
    echo 'color: ';
    if ($gfsettings['sectiontitleinsidetitletextcolour'][0] != '#') {
        echo '#';
    }
    echo $gfsettings['sectiontitleinsidetitletextcolour'] . ';';
    echo 'height: ';
    if ($gfsettings['sectiontitleboxheight'] == 0) {
        echo round(($imageproperties['height'] * 0.25), 0, PHP_ROUND_HALF_UP);
    } else {
        echo $gfsettings['sectiontitleboxheight'];
    }
    echo 'px;';
    echo 'opacity: ' . $gfsettings['sectiontitleboxopacity'] . ';';
    echo '}';
} else {
    echo '.course-content ul.trailicons li.currentselected .icon_content {';
    echo 'color: ';
    if ($gfsettings['currentselectedimagecontainertextcolour'][0] != '#') {
        echo '#';
    }
    echo $gfsettings['currentselectedimagecontainertextcolour'] . ';';
    echo '}';
}

echo '.course-content ul.trailicons li .trailicon_link .tooltip-inner {';
echo 'background-color: ';
if ($gfsettings['sectiontitlesummarybackgroundcolour'][0] != '#') {
    echo '#';
}
echo $gfsettings['sectiontitlesummarybackgroundcolour'] . ';';
echo 'color: ';
if ($gfsettings['sectiontitlesummarytextcolour'][0] != '#') {
    echo '#';
}
echo $gfsettings['sectiontitlesummarytextcolour'] . ';';
echo '}';

$tooltiparrowposition = $courseformat->get_set_show_section_title_summary_position();
echo '.course-content ul.trailicons li .trailicon_link .tooltip.' . $tooltiparrowposition . ' .tooltip-arrow {';
echo 'border-' . $tooltiparrowposition . '-color: ';
if ($gfsettings['sectiontitlesummarybackgroundcolour'][0] != '#') {
    echo '#';
}
echo $gfsettings['sectiontitlesummarybackgroundcolour'] . ';';
echo '}';

echo '.course-content ul.trailicons li .trailicon_link .image_holder .tooltip {';
echo 'opacity: ' . $gfsettings['sectiontitlesummarybackgroundopacity'] . ';';
echo '}';

echo '.course-content ul.trailicons img.new_activity {';
echo 'margin-top: ' . $imageproperties['margin-top'] . 'px;';
echo 'margin-left: ' . $imageproperties['margin-left'] . 'px;';
echo '}';

echo ' @media (max-width: 480px) {';
echo ' #trailiconcontainer  ul.trailicons { position: relative;
    width: 250px;
    height: auto;
    background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/trilha'.$gfsettings['showbackground'].'_topo_m.png");
    background-repeat: repeat-y; }';
echo '#trailiconcontainer  ul.par { position: relative;
    width: 250px;
    height: auto;
    background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/trilha'.$gfsettings['showbackground'].'_meio_m.png");
    background-repeat: repeat-y; }';
echo '#trailiconcontainer  ul.impar { position: relative;
    width: 250px;
    height: auto;
    background-image:  url("' . $CFG->wwwroot . '/course/format/trail/pix/trilha'.$gfsettings['showbackground'].'_meio_m.png");
    background-repeat: repeat-y; }';
echo '}';
echo '/* ]]> */';
echo '</style>';

if ($sectionid) {
    /* The section id has been specified so use the value of $displaysection as that
      will be set to the actual section number. */
    $sectionparam = $displaysection;
} else {
    $sectionparam = optional_param('section', -1, PARAM_INT);
}
if ($sectionparam != -1) {
    if (($sectionparam == 0) && $courseformat->is_section0_attop() && ($gfsettings['setsection0ownpagenotrailonesection'] == 1)) {
        // Don't allow an old section 0 link to work.
        $sectionparam = -1;
    } else if ($gfsettings['coursedisplay'] == COURSE_DISPLAY_SINGLEPAGE) {
        // Don't allow an old single page link to work, but set the current section.
        $renderer->set_initialsection($sectionparam);
        $sectionparam = -1;
    } else {
        $renderer->set_initialsection($sectionparam);
        $displaysection = $sectionparam;
    }
}

if ($sectionparam != -1) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module.
$PAGE->requires->js('/course/format/trail/format.js');
