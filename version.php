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

// Plugin version.
$plugin->version = 2022070201;

// Required Moodle version.
$plugin->requires  = 2022041900.00; // Moodle 4.0 (Build: 20220419).

// Full name of the plugin.
$plugin->component = 'format_trail';

// Software maturity level.
$plugin->maturity = MATURITY_BETA;

// User-friendly version number.
$plugin->release = '4.0.0.1';
