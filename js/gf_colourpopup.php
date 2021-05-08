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
defined('MOODLE_INTERNAL') || die();
require_once("HTML/QuickForm/text.php");

/**
 * HTML class for a colorpopup type element
 *
 * @package    format_trail
 * @copyright  &copy; 2019 Jose Wilson  in respect to modifications of grid format.
 * @author     &copy; 2012 G J Barnard in respect to modifications of standard topics format.
 */
class moodlequickform_gfcolourpopup extends HTML_QuickForm_text implements templatable {

    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /**
     * @var string $helpbutton html for help button.
     */
    public $helpbutton = '';

    /**
     * @var boolean $hiddenlabel html for help button.
     */
    public $hiddenlabel = false;

    /**
     * moodlequickform_gfcolourpopup constructor.
     *
     * @param string $elementname
     * @param string $elementlabel
     * @param string $attributes
     * @param string $options
     */
    public function __construct($elementname = null, $elementlabel = null, $attributes = null, $options = null) {
        parent::__construct($elementname, $elementlabel, $attributes);
        /* Pretend we are a 'static' MoodleForm element so that we get the core_form/element-static template where
          we can render our own markup via core_renderer::mform_element() in lib/outputrenderers.php.
          used in combination with 'use' statement above and export_for_template() method below. */
        $this->setType('static');
    }

    /**
     * setHiddenLabel.
     *
     * @param string $hiddenlabel
     */
    public function sethiddenlabel($hiddenlabel) {
        $this->hiddenlabel = $hiddenlabel;
    }

    /**
     * tohtml.
     *
     * @return void
     */
    public function tohtml() {
        global $CFG, $COURSE, $USER, $PAGE, $OUTPUT;
        $id = $this->getAttribute('id');
        $PAGE->requires->js('/course/format/trail/js/gf_colourpopup.js');
        $PAGE->requires->js_init_call('M.util.init_gfcolour_popup', array($id));
        $colour = $this->getValue();
        if ((!empty($colour)) && ($colour[0] == '#')) {
            $colour = substr($colour, 1);
        }
        $content = "<input size='8' name='" . $this->getName() . "' value='" . $colour . "'id='{$id}' type='text' " .
                $this->_getAttrString($this->_attributes) . " >";
        $content .= html_writer::tag('span', '&nbsp;', array('id' => 'colpicked_' . $id, 'tabindex' => '-1',
                    'style' => 'background-color: #' . $colour .
                    '; cursor: pointer; margin: 0; padding: 0 8px; border: 1px solid black'));
        $content .= html_writer::start_tag('div', array('id' => 'colpick_' . $id,
                    'style' => "display:none; position:absolute; z-index:500;",
                    'class' => 'form-colourpicker defaultsnext'));
        $content .= html_writer::tag('div', '', array('class' => 'admin_colourpicker clearfix'));
        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Automatically generates and assigns an 'id' attribute for the element.
     *
     * Currently used to ensure that labels work on radio buttons and
     * checkboxes. Per idea of Alexander Radivanovich.
     * Overriden in moodleforms to remove qf_ prefix.
     *
     * @return void
     */
    public function generateid() {
        static $idx = 1;

        if (!$this->getAttribute('id')) {
            $this->updateAttributes(array('id' => 'id_' . substr(md5(microtime() . $idx++), 0, 6)));
        }
    }

    /**
     * set html for help button
     *
     * @param array $helpbuttonargs array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    public function sethelpbutton($helpbuttonargs, $function = 'helpbutton') {
        debugging('component sethelpbutton() is not used any more, please use $mform->sethelpbutton() instead');
    }

    /**
     * get html for help button
     *
     * @return  string html for help button
     */
    public function gethelpbutton() {
        return $this->helpbutton;
    }

    /**
     * Slightly different container template when frozen. Don't want to use a label tag
     * with a for attribute in that case for the element label but instead use a div.
     * Templates are defined in renderer constructor.
     *
     * @return string
     */
    public function getelementtemplatetype() {
        if ($this->_flagFrozen) {
            return 'static';
        } else {
            return 'default';
        }
    }

    /**
     * set html for help button
     *
     * @param \renderer_base $output $helpbuttonargs array of arguments to make a help button
     * @return string
     */
    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);
        $context['html'] = $this->tohtml();
        $context['staticlabel'] = false; // Not a static label!
        return $context;
    }

}
