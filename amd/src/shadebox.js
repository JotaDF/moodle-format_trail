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
 * Trail Format shadebox — AMD replacement for the legacy YUI shadebox.
 *
 * Opens a fullscreen overlay displaying the clicked section's activities.
 *
 * @module     format_trail/shadebox
 * @package    format_trail
 * @copyright  2026 Jean Lúcio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log'], function($, log) {
    'use strict';

    /** @type {number} Currently visible section index, -1 = none. */
    var currentSection = -1;

    /** @type {number} Total number of sections in the course. */
    var numSections = 0;

    /**
     * Array indexed by section number.
     * Value 2 = section is shown in the trail; 1 = section is hidden/not in trail.
     *
     * @type {Array}
     */
    var shownArray = [];

    /** @type {boolean} Whether the shadebox overlay is currently open. */
    var isOpen = false;

    /** @type {jQuery} The #trailshadebox wrapper element. */
    var $shadebox;

    /** @type {jQuery} The #trailshadebox_overlay element (dark background). */
    var $overlay;

    /** @type {jQuery} The #trailshadebox_content element. */
    var $content;

    /**
     * Returns the next section index that should be shown, starting from `start`
     * and moving in the direction specified by `forward`.
     *
     * @param {number}  start   Starting section index.
     * @param {boolean} forward True = next, false = previous.
     * @return {number} Section index to show (same as start if no other found).
     */
    var findAdjacentSection = function(start, forward) {
        var current = start;
        var next = start;
        var found = false;

        while (!found) {
            if (forward) {
                current++;
                if (current > numSections) {
                    current = 0;
                }
            } else {
                current--;
                if (current < 0) {
                    current = numSections;
                }
            }

            if (current === start) {
                found = true; // Full loop, no other section found.
            } else if (shownArray[current] === 2) {
                next = current;
                found = true;
            }
        }

        return next;
    };

    /**
     * Hides all trail sections inside the shadebox and shows only section `idx`.
     * Also highlights the corresponding card in the icon container.
     *
     * @param {number} idx Section number to display.
     */
    var displaySection = function(idx) {
        $content.find('.trail_section').addClass('hide_section');

        var $sec = $('#section-' + idx);
        if ($sec.length) {
            $sec.removeClass('hide_section');
            currentSection = idx;
        }

        $('ul.trailicons li').removeClass('currentselected');
        var $link = $('#trailsection-' + idx);
        if ($link.length) {
            $link.closest('li').addClass('currentselected');
        }
    };

    /**
     * Opens the shadebox overlay showing the given section.
     *
     * @param {number} idx Section number to open.
     */
    var open = function(idx) {
        displaySection(idx);
        $shadebox.show();
        isOpen = true;
    };

    /**
     * Closes the shadebox overlay.
     */
    var close = function() {
        $shadebox.hide();
        isOpen = false;
    };

    return {
        /**
         * Initialise the shadebox behaviour.
         *
         * Called by the PHP renderer via $PAGE->requires->js_call_amd().
         *
         * @param {string} shownArrayJson JSON-encoded array; index = section no, value = 1|2.
         * @param {number} num            Total number of sections.
         * @param {number} initialSection Section to open immediately (-1 = none).
         */
        init: function(shownArrayJson, num, initialSection) {
            log.debug('format_trail/shadebox: init');

            numSections = num;
            shownArray = JSON.parse(shownArrayJson);

            $shadebox = $('#trailshadebox');
            $overlay = $('#trailshadebox_overlay');
            $content = $('#trailshadebox_content');

            if (!$shadebox.length) {
                log.warn('format_trail/shadebox: #trailshadebox not found in DOM');
                return;
            }

            // Move shadebox to <body> so the overlay covers the whole viewport.
            $('body').append($shadebox);

            $shadebox.hide();
            $overlay.show();
            $('#trailshadebox_close').show();

            if ($('#trailshadebox_previous').length) {
                $('#trailshadebox_previous').show();
            }
            if ($('#trailshadebox_next').length) {
                $('#trailshadebox_next').show();
            }

            $content.removeClass('hide_content');

            // Card click — open shadebox for the clicked section.
            $(document).on('click', 'ul.trailicons a.trailicon_link', function(e) {
                e.preventDefault();
                var idx = parseInt($(this).attr('id').replace('trailsection-', ''), 10);
                if (!isNaN(idx) && shownArray[idx] === 2) {
                    open(idx);
                }
            });

            // Click outside shadebox content — close.
            // The overlay is pointer-events:none (purely visual), so clicks
            // on the dark backdrop reach the document and we close here.
            $(document).on('click', function(e) {
                if (!isOpen) {
                    return;
                }
                var $target = $(e.target);
                // Do not close if the click is inside the shadebox content or controls.
                if ($target.closest('#trailshadebox_content, #trailshadebox_previous, #trailshadebox_next, #trailshadebox_close').length) {
                    return;
                }
                // Do not close if the click is on a trail card (it will open another section).
                if ($target.closest('ul.trailicons').length) {
                    return;
                }
                close();
            });

            // Close button click.
            $('#trailshadebox_close').on('click', function(e) {
                e.preventDefault();
                close();
            });

            // Previous arrow.
            $(document).on('click', '#trailshadebox_previous', function(e) {
                e.preventDefault();
                if (isOpen) {
                    displaySection(findAdjacentSection(currentSection, false));
                }
            });

            // Next arrow.
            $(document).on('click', '#trailshadebox_next', function(e) {
                e.preventDefault();
                if (isOpen) {
                    displaySection(findAdjacentSection(currentSection, true));
                }
            });

            // Keyboard navigation.
            $(document).on('keydown', function(e) {
                if (!isOpen) {
                    return;
                }
                if (e.key === 'Escape') {
                    close();
                } else if (e.key === 'ArrowLeft') {
                    displaySection(findAdjacentSection(currentSection, false));
                } else if (e.key === 'ArrowRight') {
                    displaySection(findAdjacentSection(currentSection, true));
                }
            });

            // Hash in URL — open the referenced section on page load.
            if (window.location.hash) {
                var hashIdx = parseInt(window.location.hash.replace('#section-', ''), 10);
                if (!isNaN(hashIdx) && shownArray[hashIdx] === 2) {
                    open(hashIdx);
                    return;
                }
            }

            // Explicit initial section passed from PHP.
            if (initialSection > -1 && shownArray[initialSection] === 2) {
                open(initialSection);
            }
        }
    };
});
