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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/format/trail/lib.php'); // For format_trail static constants.

if ($ADMIN->fulltree) {

    /* Default course display.
     * Course display default, can be either one of:
     * COURSE_DISPLAY_SINGLEPAGE or - All sections on one page.
     * COURSE_DISPLAY_MULTIPAGE     - One section per page.
     * as defined in moodlelib.php.
     */
    $name = 'format_trail/defaultcoursedisplay';
    $title = get_string('defaultcoursedisplay', 'format_trail');
    $description = get_string('defaultcoursedisplay_desc', 'format_trail');
    $default = COURSE_DISPLAY_SINGLEPAGE;
    $choices = array(
        COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Container alignment. */
    $name = 'format_trail/defaultimagecontaineralignment';
    $title = get_string('defaultimagecontaineralignment', 'format_trail');
    $description = get_string('defaultimagecontaineralignment_desc', 'format_trail');
    $default = format_trail::get_default_image_container_alignment();
    $choices = format_trail::get_horizontal_alignments();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Icon width. */
    $name = 'format_trail/defaultimagecontainerwidth';
    $title = get_string('defaultimagecontainerwidth', 'format_trail');
    $description = get_string('defaultimagecontainerwidth_desc', 'format_trail');
    $default = format_trail::get_default_image_container_width();
    $choices = format_trail::get_image_container_widths();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Icon ratio. */
    $name = 'format_trail/defaultimagecontainerratio';
    $title = get_string('defaultimagecontainerratio', 'format_trail');
    $description = get_string('defaultimagecontainerratio_desc', 'format_trail');
    $default = format_trail::get_default_image_container_ratio();
    $choices = format_trail::get_image_container_ratios();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Resize method - 1 = scale, 2 = crop. */
    $name = 'format_trail/defaultimageresizemethod';
    $title = get_string('defaultimageresizemethod', 'format_trail');
    $description = get_string('defaultimageresizemethod_desc', 'format_trail');
    $default = format_trail::get_default_image_resize_method();
    $choices = array(
        1 => new lang_string('scale', 'format_trail'),   // Scale.
        2 => new lang_string('crop', 'format_trail')   // Crop.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default border colour in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultbordercolour';
    $title = get_string('defaultbordercolour', 'format_trail');
    $description = get_string('defaultbordercolour_desc', 'format_trail');
    $default = format_trail::get_default_border_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    /* Border width. */
    $name = 'format_trail/defaultborderwidth';
    $title = get_string('defaultborderwidth', 'format_trail');
    $description = get_string('defaultborderwidth_desc', 'format_trail');
    $default = format_trail::get_default_border_width();
    $choices = format_trail::get_border_widths();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Border radius on / off. */
    $name = 'format_trail/defaultborderradius';
    $title = get_string('defaultborderradius', 'format_trail');
    $description = get_string('defaultborderradius_desc', 'format_trail');
    $default = format_trail::get_default_border_radius();
    $choices = array(
        1 => new lang_string('off', 'format_trail'),   // Off.
        2 => new lang_string('on', 'format_trail')   // On.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default imagecontainer background colour in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultimagecontainerbackgroundcolour';
    $title = get_string('defaultimagecontainerbackgroundcolour', 'format_trail');
    $description = get_string('defaultimagecontainerbackgroundcolour_desc', 'format_trail');
    $default = format_trail::get_default_image_container_background_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default current selected section colour in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultcurrentselectedsectioncolour';
    $title = get_string('defaultcurrentselectedsectioncolour', 'format_trail');
    $description = get_string('defaultcurrentselectedsectioncolour_desc', 'format_trail');
    $default = format_trail::get_default_current_selected_section_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default current selected image container text colour in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultcurrentselectedimagecontainertextcolour';
    $title = get_string('defaultcurrentselectedimagecontainertextcolour', 'format_trail');
    $description = get_string('defaultcurrentselectedimagecontainertextcolour_desc', 'format_trail');
    $default = format_trail::get_default_current_selected_image_container_text_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default current selected image container colour in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultcurrentselectedimagecontainercolour';
    $title = get_string('defaultcurrentselectedimagecontainercolour', 'format_trail');
    $description = get_string('defaultcurrentselectedimagecontainercolour_desc', 'format_trail');
    $default = format_trail::get_default_current_selected_image_container_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    /* Hide section title - 1 = no, 2 = yes. */
    $name = 'format_trail/defaulthidesectiontitle';
    $title = get_string('defaulthidesectiontitle', 'format_trail');
    $description = get_string('defaulthidesectiontitle_desc', 'format_trail');
    $default = format_trail::get_default_hide_section_title();
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Section title in trail box maximum length with 0 for no truncation. */
    $name = 'format_trail/defaultsectiontitletraillengthmaxoption';
    $title = get_string('defaultsectiontitletraillengthmaxoption', 'format_trail');
    $description = get_string('defaultsectiontitletraillengthmaxoption_desc', 'format_trail');
    $default = format_trail::get_default_section_title_trail_length_max_option();
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_INT));

    /* Section title box position - 1 = Inside, 2 = Outside. */
    $name = 'format_trail/defaultsectiontitleboxposition';
    $title = get_string('defaultsectiontitleboxposition', 'format_trail');
    $description = get_string('defaultsectiontitleboxposition_desc', 'format_trail');
    $default = format_trail::get_default_section_title_box_position();
    $choices = array(
        1 => new lang_string('sectiontitleboxpositioninside', 'format_trail'),
        2 => new lang_string('sectiontitleboxpositionoutside', 'format_trail')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Section title box inside position - 1 = Top, 2 = Middle, 3 = Bottom. */
    $name = 'format_trail/defaultsectiontitleboxinsideposition';
    $title = get_string('defaultsectiontitleboxinsideposition', 'format_trail');
    $description = get_string('defaultsectiontitleboxinsideposition_desc', 'format_trail');
    $default = format_trail::get_default_section_title_box_inside_position();
    $choices = array(
        1 => new lang_string('sectiontitleboxinsidepositiontop', 'format_trail'),
        2 => new lang_string('sectiontitleboxinsidepositionmiddle', 'format_trail'),
        3 => new lang_string('sectiontitleboxinsidepositionbottom', 'format_trail')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Section title box height. */
    $name = 'format_trail/defaultsectiontitleboxheight';
    $title = get_string('defaultsectiontitleboxheight', 'format_trail');
    $description = get_string('defaultsectiontitleboxheight_desc', 'format_trail');
    $default = format_trail::get_default_section_title_box_height();
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_INT));

    /* Section title box opacity. */
    $name = 'format_trail/defaultsectiontitleboxopacity';
    $title = get_string('defaultsectiontitleboxopacity', 'format_trail');
    $description = get_string('defaultsectiontitleboxopacity_desc', 'format_trail');
    $default = format_trail::get_default_section_title_box_opacity();
    $choices = format_trail::get_default_opacities();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Section title font size. */
    $name = 'format_trail/defaultsectiontitlefontsize';
    $title = get_string('defaultsectiontitlefontsize', 'format_trail');
    $description = get_string('defaultsectiontitlefontsize_desc', 'format_trail');
    $default = format_trail::get_default_section_title_font_size();
    $choices = format_trail::get_default_section_font_sizes();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Section title alignment. */
    $name = 'format_trail/defaultsectiontitlealignment';
    $title = get_string('defaultsectiontitlealignment', 'format_trail');
    $description = get_string('defaultsectiontitlealignment_desc', 'format_trail');
    $default = format_trail::get_default_section_title_alignment();
    $choices = format_trail::get_horizontal_alignments();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default section title text colour in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultsectiontitleinsidetitletextcolour';
    $title = get_string('defaultsectiontitleinsidetitletextcolour', 'format_trail');
    $description = get_string('defaultsectiontitleinsidetitletextcolour_desc', 'format_trail');
    $default = format_trail::get_default_section_title_inside_title_text_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default section title background colour in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultsectiontitleinsidetitlebackgroundcolour';
    $title = get_string('defaultsectiontitleinsidetitlebackgroundcolour', 'format_trail');
    $description = get_string('defaultsectiontitleinsidetitlebackgroundcolour_desc', 'format_trail');
    $default = format_trail::get_default_section_title_inside_title_background_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    /* Show section title summary on hover - 1 = no, 2 = yes. */
    $name = 'format_trail/defaultshowsectiontitlesummary';
    $title = get_string('defaultshowsectiontitlesummary', 'format_trail');
    $description = get_string('defaultshowsectiontitlesummary_desc', 'format_trail');
    $default = format_trail::get_default_show_section_title_summary();
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Show section title summary on hover position - 1 = top, 2 = bottom, 3 = left and 4 = right. */
    $name = 'format_trail/defaultsetshowsectiontitlesummaryposition';
    $title = get_string('defaultsetshowsectiontitlesummaryposition', 'format_trail');
    $description = get_string('defaultsetshowsectiontitlesummaryposition_desc', 'format_trail');
    $default = format_trail::get_default_set_show_section_title_summary_position();
    $choices = array(
        1 => new lang_string('top', 'format_trail'),
        2 => new lang_string('bottom', 'format_trail'),
        3 => new lang_string('left', 'format_trail'),
        4 => new lang_string('right', 'format_trail')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Section title summary maximum length with 0 for no truncation. */
    $name = 'format_trail/defaultsectiontitlesummarymaxlength';
    $title = get_string('defaultsectiontitlesummarymaxlength', 'format_trail');
    $description = get_string('defaultsectiontitlesummarymaxlength_desc', 'format_trail');
    $default = format_trail::get_default_section_title_summary_max_length();
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_INT));

    // Default section title summary text colour on hover in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultsectiontitlesummarytextcolour';
    $title = get_string('defaultsectiontitlesummarytextcolour', 'format_trail');
    $description = get_string('defaultsectiontitlesummarytextcolour_desc', 'format_trail');
    $default = format_trail::get_default_section_title_summary_text_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default section title summary background colour on hover in hexadecimal RGB with preceding '#'.
    $name = 'format_trail/defaultsectiontitlesummarybackgroundcolour';
    $title = get_string('defaultsectiontitlesummarybackgroundcolour', 'format_trail');
    $description = get_string('defaultsectiontitlesummarybackgroundcolour_desc', 'format_trail');
    $default = format_trail::get_default_section_title_summary_background_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    /* Section title title summary opacity on hover. */
    $name = 'format_trail/defaultsectiontitlesummarybackgroundopacity';
    $title = get_string('defaultsectiontitlesummarybackgroundopacity', 'format_trail');
    $description = get_string('defaultsectiontitlesummarybackgroundopacity_desc', 'format_trail');
    $default = format_trail::get_default_section_title_summary_opacity();
    $choices = format_trail::get_default_opacities();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Show new activity notification image - 1 = no, 2 = yes. */
    $name = 'format_trail/defaultnewactivity';
    $title = get_string('defaultnewactivity', 'format_trail');
    $description = get_string('defaultnewactivity_desc', 'format_trail');
    $default = 2;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Fix the section container popup to the screen. 1 = no, 2 = yes */
    $name = 'format_trail/defaultfitsectioncontainertowindow';
    $title = get_string('defaultfitsectioncontainertowindow', 'format_trail');
    $description = get_string('defaultfitsectioncontainertowindow_desc', 'format_trail');
    $default = 1;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Grey out hidden sections. */
    $name = 'format_trail/defaultgreyouthidden';
    $title = get_string('greyouthidden', 'format_trail');
    $description = get_string('greyouthidden_desc', 'format_trail');
    $default = 1;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Section 0 on own page when out of the trail and course layout is 'Show one section per page'. */
    $name = 'format_trail/defaultsection0ownpagenotrailonesection';
    $title = get_string('defaultsection0ownpagenotrailonesection', 'format_trail');
    $description = get_string('defaultsection0ownpagenotrailonesection_desc', 'format_trail');
    $default = 1;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    /* Custom mouse pointers - 1 = no, 2 = yes. */
    $name = 'format_trail/defaultcustommousepointers';
    $title = get_string('custommousepointers', 'format_trail');
    $description = get_string('custommousepointers_desc', 'format_trail');
    $default = 2;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
    /* Show style background. */
    $name = 'format_trail/defaultsetshowbackground';
    $title = get_string('defaultsetshowbackground', 'format_trail');
    $description = get_string('defaultsetshowbackground', 'format_trail');
    $default = format_trail::get_default_set_show_background();
    $choices = array(
        1 => new lang_string('tipo_pista', 'format_trail'),
        5 => new lang_string('tipo_pista2', 'format_trail'),
        2 => new lang_string('tipo_rio', 'format_trail'),
        3 => new lang_string('tipo_quebra1', 'format_trail'),
        4 => new lang_string('tipo_quebra2', 'format_trail'),
        5 => new lang_string('tipo_tesouro', 'format_trail')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
}
