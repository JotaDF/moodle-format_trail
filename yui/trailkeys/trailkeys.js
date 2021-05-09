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
 * @author     &copy; 2013 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable camelcase */
M.format_trail = M.format_trail || {};
M.format_trail.trailkeys = M.format_trail.trailkeys || {};
M.format_trail.trailkeys = {
    currentTrailBox: false,
    currentTrailBoxIndex: 0,
    findfocused: function() {
        var focused = document.activeElement;
        if (!focused || focused == document.body) {
            focused = null;
        } else if (document.querySelector) {
            focused = document.querySelector(":focus");
        }
        M.format_trail.trailkeys.currentTrailBox = false;
        if (focused && focused.id) {
            Y.log('Focus id: ' + focused.id);
            if (focused.id.indexOf('trailsection-') > -1) {
                Y.log('Trail id: ' + focused.id);
                M.format_trail.trailkeys.currentTrailBox = true;
                M.format_trail.trailkeys.currentTrailBoxIndex = parseInt(focused.id.replace("trailsection-", ""), 10);
            }
        }
        return M.format_trail.trailkeys.currentTrailBox;
    },
    init: function(params) {
        Y.on('esc', function(e) {
            e.preventDefault();
            Y.log("Esc pressed");
            Y.log("Selected section no: " + M.format_trail.selected_section_no);
            M.format_trail.icon_toggle(e);
        });
        // Initiated in CONTRIB-3240...
        Y.on('enter', function(e) {
            if (M.format_trail.trailkeys.currentTrailBox) {
                e.preventDefault();
                if (e.shiftKey) {
                    Y.log("Shift Enter pressed");
                    Y.log("Selected section no: " + M.format_trail.selected_section_no);
                    M.format_trail.icon_toggle(e);
                } else {
                    Y.log("Enter pressed");
                    Y.log("Selected section no: " + M.format_trail.selected_section_no);
                    M.format_trail.icon_toggle(e);
                }
            }
        });
        Y.on('tab', function() {
            setTimeout(function() {
                // Cope with the fact that the default event happens after us.
                // Therefore we need to react after focus has moved.
                if (M.format_trail.trailkeys.findfocused()) {
                    M.format_trail.tab(M.format_trail.trailkeys.currentTrailBoxIndex);
                }
            }, 250);
        });
        Y.on('space', function(e) {
            if (M.format_trail.trailkeys.currentTrailBox) {
                e.preventDefault();
                Y.log("Space pressed");
                Y.log("Selected section no: " + M.format_trail.selected_section_no);
                M.format_trail.icon_toggle(e);
            }
        });
        Y.on('left', function(e) {
            e.preventDefault();
            Y.log("Left pressed");
            if (params.rtl) {
                M.format_trail.next_section(e);
            } else {
                M.format_trail.previous_section(e);
            }
        });
        Y.on('right', function(e) {
            e.preventDefault();
            Y.log("Right pressed");
            if (params.rtl) {
                M.format_trail.previous_section(e);
            } else {
                M.format_trail.next_section(e);
            }
        });
    }
};