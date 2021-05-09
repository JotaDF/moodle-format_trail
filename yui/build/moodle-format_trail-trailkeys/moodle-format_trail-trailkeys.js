YUI.add('event-nav-keys', function(Y) {

var keys = {
        enter    : 13,
        esc      : 27,
        backspace: 8,
        tab      : 9,
        pageUp   : 33,
        pageDown : 34,
        left     : 37,
        up       : 38,
        right    : 39,
        down     : 40,
        space    : 32
    };

Y.Object.each(keys, function (keyCode, name) {
    Y.Event.define({
        type: name,

        on: function (node, sub, notifier, filter) {
            var method = (filter) ? 'delegate' : 'on';

            sub._handle = node[method]('keydown', function (e) {
                if (e.keyCode === keyCode) {
                    notifier.fire(e);
                }
            }, filter);
        },

        delegate: function () {
            this.on.apply(this, arguments);
        },

        detach: function (node, sub) {
            sub._handle.detach();
        },

        detachDelegate: function () {
            this.detach.apply(this, arguments);
        }
    });
});


}, '2011.02.02-21-07' ,{requires:['event-synthetic']});
YUI.add('moodle-format_trail-trailkeys', function (Y, NAME) {

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

M.format_trail = M.format_trail || {};
M.format_trail.trailkeys = M.format_trail.trailkeys || {};
M.format_trail.trailkeys = {
    currentTrailBox: false,
    currentTrailBoxIndex: 0,
    findfocused: function () {
        var focused = document.activeElement;
        if (!focused || focused == document.body) {
            focused = null;
        } else if (document.querySelector) {
            focused = document.querySelector(":focus");
        }
        M.format_trail.trailkeys.currentTrailBox = false;
        if (focused && focused.id) {
            if (focused.id.indexOf('trailsection-') > -1) {
                M.format_trail.trailkeys.currentTrailBox = true;
                M.format_trail.trailkeys.currentTrailBoxIndex = parseInt(focused.id.replace("trailsection-", ""), 10);
            }
        }
        return M.format_trail.trailkeys.currentTrailBox;
    },
    init: function (params) {
        Y.on('esc', function (e) {
            e.preventDefault();
            M.format_trail.icon_toggle(e);
        });
        // Initiated in CONTRIB-3240...
        Y.on('enter', function (e) {
            if (M.format_trail.trailkeys.currentTrailBox) {
                e.preventDefault();
                if (e.shiftKey) {
                    M.format_trail.icon_toggle(e);
                } else {
                    M.format_trail.icon_toggle(e);
                }
            }
        });
        Y.on('tab', function (/*e*/) {
            setTimeout(function () {
                // Cope with the fact that the default event happens after us.
                // Therefore we need to react after focus has moved.
                if (M.format_trail.trailkeys.findfocused()) {
                    M.format_trail.tab(M.format_trail.trailkeys.currentTrailBoxIndex);
                }
            }, 250);
        });
        Y.on('space', function (e) {
            if (M.format_trail.trailkeys.currentTrailBox) {
                e.preventDefault();
                M.format_trail.icon_toggle(e);
            }
        });
        Y.on('left', function (e) {
            e.preventDefault();
            if (params.rtl) {
                M.format_trail.next_section(e);
            } else {
                M.format_trail.previous_section(e);
            }
        });
        Y.on('right', function (e) {
            e.preventDefault();
            if (params.rtl) {
                M.format_trail.previous_section(e);
            } else {
                M.format_trail.next_section(e);
            }
        });
    }
};

}, '@VERSION@', {"requires": ["event-nav-keys"]});
