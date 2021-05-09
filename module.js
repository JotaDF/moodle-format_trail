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
 * @author     Based on code originally written by Paul Krix and Julian Ridden.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint-disable camelcase */
/**
 * @namespace
 */
M.format_trail = M.format_trail || {
    // The YUI Object thang.
    ourYUI: null,
    // Boolean - states if editing is on.
    editing_on: null,
    // String - If set will contain a URL prefix for the section page redirect functionality and not show the shade box.
    section_redirect: null,
    // YUI Node object for the trail icon element.
    selected_section: null,
    // Integer - number of sections in the course.
    num_sections: null,
    // Integer - current selected section number or '-1' if unknown.
    selected_section_no: -1,
    /* Array - Index is section number (Integer) and value is either '1' for not section not shown or '2' for shown
     helps to work out the next / previous section in 'find_next_shown_section'. */
    shadebox_shown_array: null,
    // DOM reference to the #trailshadebox_content element.
    shadebox_content: null
};

/**
 * Initialise with the information supplied from the course format so we can operate.
 * @param {Object}  Y YUI instance
 * @param {Boolean} the_editing_on If editing is on.
 * @param {String}  the_section_redirect If set will contain a URL prefix for the section page redirect functionality
 *                  and not show the shade box.
 * @param {Integer} the_num_sections the number of sections in the course.
 * @param {Integer} the_initial_section the initial section to show.
 * @param {Array}   the_shadebox_shown_array States what sections are not shown (value of 1) and which are (value of 2)
 *                  index is the section no.
 */
M.format_trail.init = function(Y, the_editing_on, the_section_redirect, the_num_sections, the_initial_section,
        the_shadebox_shown_array) {
    "use strict";
    this.ourYUI = Y;
    this.editing_on = the_editing_on;
    this.section_redirect = the_section_redirect;
    this.selected_section = null;
    this.num_sections = parseInt(the_num_sections);
    this.shadebox_shown_array = the_shadebox_shown_array;
    Y.use('json-parse', function(Y) {
        M.format_trail.shadebox_shown_array = Y.JSON.parse(M.format_trail.shadebox_shown_array);
    });

    if (this.num_sections > 0) {
        if (the_initial_section > -1) {
            M.format_trail.tab(the_initial_section);
        } else {
            this.set_selected_section(this.num_sections, true, true); // Section 0 can be in the trail.
        }
    } else {
        this.selected_section_no = -1;
    }

    // Have to show the sections when editing.
    if (the_editing_on) {
        // Show the sections when editing.
        Y.all(".trail_section").removeClass('hide_section');
    } else {
        var navdrawer = M.format_trail.ourYUI.one('[data-region="drawer"] nav:first-child'); // Flat navigation.
        if (navdrawer) {
            Y.delegate('click', this.navdrawerclick, '[data-region="drawer"] nav:first-child', 'a', this);
        }
        if (this.section_redirect === null) {
            Y.delegate('click', this.icon_click, Y.config.doc, 'ul.trailicons a.trailicon_link', this);

            var shadeboxtoggleone = Y.one("#trailshadebox_overlay");
            if (shadeboxtoggleone) {
                shadeboxtoggleone.on('click', this.icon_toggle, this);
            }
            var shadeboxtoggletwo = Y.one("#trailshadebox_close");
            if (shadeboxtoggletwo) {
                shadeboxtoggletwo.on('click', this.icon_toggle, this);
                document.getElementById("trailshadebox_close").style.display = "";
            }
            var shadeboxprevious = Y.one("#trailshadebox_previous");
            if (shadeboxprevious) {
                shadeboxprevious.on('click', this.previous_section, this);
                document.getElementById("trailshadebox_previous").style.display = "";
            }
            var shadeboxnext = Y.one("#trailshadebox_next");
            if (shadeboxnext) {
                shadeboxnext.on('click', this.next_section, this);
                document.getElementById("trailshadebox_next").style.display = "";
            }
            M.format_trail.shadebox_overlay = document.getElementById('trailshadebox_overlay');
            if (M.format_trail.shadebox_overlay) {
                M.format_trail.shadebox.initialize_shadebox();
                M.format_trail.shadebox.update_shadebox();
                window.onresize = function() {
                    M.format_trail.shadebox.update_shadebox();
                };
            }
        }
    }
    this.shadebox_content = Y.one("#trailshadebox_content");
    if (this.shadebox_content) {
        this.shadebox_content.removeClass('hide_content'); // Content 'flash' prevention.
    }

    // Show the shadebox of a named anchor in the URL where it is expected to be of the form:
    // #section-X.
    if ((this.section_redirect === null) && (window.location.hash) && (!the_editing_on)) {
        var idx = parseInt(window.location.hash.substring(window.location.hash.indexOf("-") + 1));
        var min = 1;
        if (M.format_trail.shadebox_shown_array[0] == 2) { // Section 0 can be shown.
            min = 0;
        }
        if ((idx >= min) && (idx <= this.num_sections) && (M.format_trail.shadebox_shown_array[idx] == 2)) {
            M.format_trail.tab(idx);
            M.format_trail.trail_toggle();
        }
    } else if ((this.num_sections > 0) && (the_initial_section > -1)) { // Section has been specified so show it.
        M.format_trail.trail_toggle();
    }
};

/**
 * Called when the user clicks on the trail icon, set up in the init() method.
 * @param {object} e color
 */
M.format_trail.icon_click = function(e) {
    "use strict";
    e.preventDefault();
    var icon_index = parseInt(e.currentTarget.get('id').replace("trailsection-", ""));
    var previousno = this.selected_section_no;
    this.selected_section_no = icon_index;
    this.update_selected_background(previousno);
    this.icon_toggle(e);
};

/**
 * Called when there is flat navigation and the nav drawer has been clicked.
 * @param {object} e Click event.
 * @return {boolean} Returns true if event is to be triggered normally or nothing otherwise as preventDefault will stop the
 default action of the event - https://www.w3.org/TR/DOM-Level-2-Events/events.html#Events-flow-cancelation.
 */
M.format_trail.navdrawerclick = function(e) {
    "use strict";
    var href = e.currentTarget.get('href');
    var sectionref = href.indexOf("#section-");
    if (sectionref === 0) {
        return true;
    }
    var idx = parseInt(href.substring(sectionref + 9));
    var min = 1;
    if (this.shadebox_shown_array[0] == 2) { // Section 0 can be shown.
        min = 0;
    }
    if ((idx >= min) && (idx <= this.num_sections) && (this.shadebox_shown_array[idx] == 2)) {
        this.tab(idx);
        this.icon_toggle(e);
    }
    return;
};

/**
 * Called when the user tabs and the item is a trail icon.
 * @param {object} index Click event.
 */
M.format_trail.tab = function(index) {
    "use strict";
    this.ourYUI.log('M.format_trail.tab: ' + index);
    var previous_no = this.selected_section_no;
    this.selected_section_no = index;
    this.update_selected_background(previous_no);
    if (M.format_trail.shadebox.shadebox_open === true) {
        this.change_shown();
    }
};

/**
 * Toggles the shade box on / off.
 * Called when the user clicks on a trail icon or presses the Esc or Space keys - see 'trailkeys.js'.
 * @param {Object} e Event object.
 */
M.format_trail.icon_toggle = function(e) {
    "use strict";
    e.preventDefault();
    this.trail_toggle();
};

M.format_trail.trail_toggle = function() {
    if (this.selected_section_no != -1) { // Then a valid shown section has been selected.
        if ((this.editing_on === true) && (this.update_capability === true)) {
            // Jump to the section on the page.
            window.scroll(0, document.getElementById("section-" + this.selected_section_no).offsetTop);
        } else if (this.section_redirect !== null) {
            // Keyboard control of 'toggle' in 'One section per page' layout.
            location.assign(this.section_redirect + "&section=" + this.selected_section_no);
        } else if (M.format_trail.shadebox.shadebox_open === true) {
            this.shadebox.toggle_shadebox();
        } else {
            this.change_shown();
            this.shadebox.toggle_shadebox();
        }
    }
};

/**
 * Called when the user clicks on the left arrow on the shade box or when they press the left
 * cursor key on the keyboard - see 'trailkeys.js'.
 * Moves to the previous visible section - looping to the last if the current is the first.
 * @param {Object} e Event object.
 */
M.format_trail.previous_section = function() {
    "use strict";
    this.change_selected_section(false);
};

/**
 * Called when the user clicks on the right arrow on the shade box or when they press the right
 * cursor key on the keyboard - see 'trailkeys.js'.
 * Moves to the next visible section - looping to the first if the current is the last.
 * @param {Object} e Event object.
 */
M.format_trail.next_section = function() {
    this.change_selected_section(true);
};

/**
 * Changes the current section in response to user input either arrows or keys.
 * @param {Boolean} increase_section If 'true' to to the next section, if 'false' go to the previous.
 */
M.format_trail.change_selected_section = function(increase_section) {
    "use strict";
    if (this.selected_section_no != -1) { // Then a valid shown section has been selected.
        this.set_selected_section(this.selected_section_no, increase_section, false);
        if (M.format_trail.shadebox.shadebox_open === true) {
            this.change_shown();
        }
    }
};

/**
 * Changes the shown section within the shade box to the new one defined in 'selected_section_no'.
 */
M.format_trail.change_shown = function() {
    "use strict";
    // Make the selected section visible, scroll to it and hide all other sections.
    if (this.selected_section !== null) {
        this.selected_section.addClass('hide_section');
    }
    this.selected_section = this.ourYUI.one("#section-" + this.selected_section_no);

    // Focus on the first element in the shade box.
    var firstactivity = document.getElementById("section-" + this.selected_section_no).getElementsByTagName('a')[0];
    if (firstactivity) {
        firstactivity.focus();
    }

    this.selected_section.removeClass('hide_section');
};

/**
 * Works out what the 'next' section should be given the starting point and direction.  If called from
 * init() then will ignore that there is no current section upon which to 'un-select' before we select
 * the new one.  The result is placed in 'selected_section_no'.
 * @param {Integer} starting_point The starting point upon which to start the search.
 * @param {Boolean} increase_section If 'true' to to the next section, if 'false' go to the previous.
 * @param {Boolean} initialise If 'true' we are initialising and therefore no current section.
 */
M.format_trail.set_selected_section = function(starting_point, increase_section, initialise) {
    "use strict";
    if ((this.selected_section_no != -1) || (initialise === true)) {
        var previous_no = this.selected_section_no;
        this.selected_section_no = this.find_next_shown_section(starting_point, increase_section);
        this.update_selected_background(previous_no);
    }
};

/**
 * Updates the selected icon background.
 * @param {Integer} previous_no The number of the previous section.
 */
M.format_trail.update_selected_background = function(previous_no) {
    "use strict";
    if (this.selected_section_no != -1) {
        var selected_section = this.ourYUI.one("#trailsection-" + this.selected_section_no);
        selected_section.get('parentNode').addClass('currentselected');
    }
    if ((previous_no != -1) && (previous_no != this.selected_section_no)) { // Do not un-select if we are the current section.
        var previous_section = this.ourYUI.one("#trailsection-" + previous_no);
        previous_section.get('parentNode').removeClass('currentselected');
    }
};

/**
 * Returns the next shown section from the given starting point and direction.
 * @param {Integer} starting_point The starting point upon which to start the search.
 * @param {Boolean} increase_section If 'true' to to the next section, if 'false' go to the previous.
 * @returns {Integer} The next section number or '-1' if not found.
 */
M.format_trail.find_next_shown_section = function(starting_point, increase_section) {
    "use strict";
    var found = false;
    var current = starting_point;
    var next = starting_point;

    while (found === false) {
        if (increase_section === true) {
            current++;
            if (current > this.num_sections) {
                current = 0;
            }
        } else {
            current--;
            if (current < 0) {
                current = this.num_sections;
            }
        }

        // Guard against repeated looping code...
        if (current == starting_point) {
            found = true; // Exit loop and 'next' will be the starting point.
        } else if (this.shadebox_shown_array[current] == 2) { // This section can be shown.
            next = current;
            found = true; // Exit loop and 'next' will be 'current'.
        }
    }

    return next;
};

// Below is shade box code.
M.format_trail.shadebox = M.format_trail.shadebox || {
    // Boolean stating if the shade box is open or not.
    shadebox_open: null,
    // DOM reference to the shade box overlay element.
    shadebox_overlay: null,
    // DOM reference to the 'trailshadebox' element.
    trail_shadebox: null
};

// Initialises the shade box.
M.format_trail.shadebox.initialize_shadebox = function() {
    "use strict";
    this.shadebox_open = false;

    this.shadebox_overlay = document.getElementById('trailshadebox_overlay');
    this.shadebox_overlay.style.display = "";
    this.trail_shadebox = document.getElementById('trailshadebox');
    document.body.appendChild(this.trail_shadebox); // Adds the shade box to the document.

    this.hide_shadebox();

    var trailshadebox_content = M.format_trail.ourYUI.one('#trailshadebox_content');
    if (trailshadebox_content.hasClass('absolute')) {
        var top = 50;
        var pageelement = M.format_trail.ourYUI.one('#page-content'); // All themes.
        if (!pageelement) {
            pageelement = M.format_trail.ourYUI.one('#region-main'); // Fallback.
        }
        if (pageelement) {
            var pageelementDOM = pageelement.getDOMNode();
            top = pageelementDOM.offsetTop + pageelementDOM.clientTop;
            if (top === 0) {
                // Use the parent.  This can happen with the Boost theme where region-main is floated.
                top = pageelementDOM.offsetParent.offsetTop + pageelementDOM.offsetParent.clientTop;
            }
            top = top + 15;
        }

        var navdrawer = M.format_trail.ourYUI.one('#nav-drawer'); // Boost theme.
        if (navdrawer) {
            var navdrawerDOM = navdrawer.getDOMNode();
            var navdrawerStyle = window.getComputedStyle(navdrawerDOM);
            var zindex = parseInt(navdrawerStyle.getPropertyValue("z-index"));
            if (zindex) {
                zindex = zindex + 1;
                this.shadebox_overlay.style.zIndex = '' + zindex;
                zindex = zindex + 1;
                trailshadebox_content.setStyle('zIndex', '' + zindex);
            }
        }

        trailshadebox_content.setStyle('top', '' + top + 'px');
    }
};

// Toggles the shade box open / closed.
M.format_trail.shadebox.toggle_shadebox = function() {
    "use strict";
    if (this.shadebox_open) {
        this.hide_shadebox();
        this.shadebox_open = false;
    } else {
        window.scrollTo(0, 0);
        this.show_shadebox();
        this.shadebox_open = true;
    }
};

// Shows the shade box.
M.format_trail.shadebox.show_shadebox = function() {
    "use strict";
    this.update_shadebox();
    this.trail_shadebox.style.display = "";
};

// Hides the shade box.
M.format_trail.shadebox.hide_shadebox = function() {
    "use strict";
    this.trail_shadebox.style.display = "none";
};

// Adjusts the size of the shade box every time it's shown as the browser window could have changed.
M.format_trail.shadebox.update_shadebox = function() {
    "use strict";
    // Make the overlay full screen (width happens automatically, so just update the height).
    var pagesize = this.get_page_height();
    this.shadebox_overlay.style.height = pagesize + "px";
};

/**
 * Gets the page height.
 * Code from quirksmode.org.
 * Author unknown.
 * @return {object} pageHeight
 */
M.format_trail.shadebox.get_page_height = function() {
    "use strict";
    var yScroll;
    if (window.innerHeight && window.scrollMaxY) {
        yScroll = window.innerHeight + window.scrollMaxY;
    } else if (document.body.scrollHeight > document.body.offsetHeight) { // All but Explorer Mac.
        yScroll = document.body.scrollHeight;
    } else { // Explorer Mac ... also works in Explorer 6 strict and safari.
        yScroll = document.body.offsetHeight;
    }

    var windowHeight;
    // All except Explorer.
    if (self.innerHeight) { // jshint ignore:line
        windowHeight = self.innerHeight; // jshint ignore:line
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 strict mode.
        windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // Other Explorers.
        windowHeight = document.body.clientHeight;
    }

    // For small pages with total height less than height of the viewport.
    var pageHeight;
    if (yScroll < windowHeight) {
        pageHeight = windowHeight;
    } else {
        pageHeight = yScroll;
    }
    return pageHeight;
};
