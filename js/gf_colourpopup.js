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
/* eslint-disable camelcase */
M.util.init_gfcolour_popup = function(Y, id, previewconf) {
    Y.use('node', 'event-mouseenter', function() {
        /**
         * The colour popup object
         */
        var colourpopup = {
            box: null,
            input: null,
            swatch: null,
            image: null,
            preview: null,
            current: null,
            eventClick: null,
            eventFocus: null,
            eventMouseEnter: null,
            eventMouseLeave: null,
            eventMouseMove: null,
            width: 300,
            height:  100,
            factor: 5,
            /**
             * Initalises the colour popup by putting everything together and wiring the events
             */
            init: function() {
                this.input = Y.one('#' + id);
                this.swatch = Y.one('#colpicked_' + id);
                this.box = this.input.ancestor().one('.admin_colourpicker');
                this.image = Y.Node.create('<img alt="" class="colourdialogue" />');
                this.image.setAttribute('src', M.util.image_url('i/colourpicker', 'moodle'));
                this.preview = Y.Node.create('<div class="previewcolour"></div>');
                this.preview.setStyle('width', this.height / 2).setStyle('height',
                this.height / 2).setStyle('backgroundColor', this.input.get('value'));
                this.current = Y.Node.create('<div class="currentcolour"></div>');
                this.current.setStyle('width', this.height / 2).setStyle('height',
                this.height / 2 - 1).setStyle('backgroundColor', this.input.get('value'));
                this.box.setContent('').append(this.image).append(this.preview).append(this.current);

                if (typeof (previewconf) === 'object' && previewconf !== null) {
                    Y.one('#' + id + '_preview').on('click', function() {
                        if (Y.Lang.isString(previewconf.selector)) {
                            Y.all(previewconf.selector).setStyle(previewconf.style, this.input.get('value'));
                        } else {
                            for (var i in previewconf.selector) {
                                Y.all(previewconf.selector[i]).setStyle(previewconf.style, this.input.get('value'));
                            }
                        }
                    }, this);
                }
                this.swatch.on('click', this.popup, this);
                this.input.on('blur', this.setColour, this);
                this.eventClick = this.image.on('click', this.pickColour, this);
                this.eventMouseEnter = Y.on('mouseenter', this.startFollow, this.image, this);
            },
            popup: function() {
                this.box.ancestor().setStyle('display', 'block');
            },
            showColours: function() {
                this.eventFocus.detach();
                this.box.setContent('').append(this.image).append(this.preview).append(this.current);
            },
            setColour: function() {
                var colour = this.input.get('value');
                this.swatch.setStyle('backgroundColor', '#' + colour);
            },
            startFollow: function() {
                this.eventMouseEnter.detach();
                this.eventMouseLeave = Y.on('mouseleave', this.endFollow, this.image, this);
                this.eventMouseMove = this.image.on('mousemove', function(e) {
                    var colour = this.determineColour(e);
                    this.preview.setStyle('backgroundColor', '#' + colour);
                }, this);
            },
            /**
             * Stops following the mouse
             */
            endFollow: function() {
                this.eventMouseMove.detach();
                this.eventMouseLeave.detach();
                this.box.ancestor().setStyle('display', 'none');
                this.eventMouseEnter = Y.on('mouseenter', this.startFollow, this.image, this);
            },
            /**
             * Picks the colour the was clicked on
             * @param {object} e event
             */
            pickColour: function(e) {
                var colour = this.determineColour(e);
                this.input.set('value', colour);
                this.input.focus();
                this.swatch.setStyle('backgroundColor', '#' + colour);
                this.current.setStyle('backgroundColor', '#' + colour);
                this.box.ancestor().setStyle('display', 'none');
            },
            /**
             * Calculates the colour from the given co-ordinates
             * @param {object} e event
             * @return {object} string
             */
            determineColour: function(e) {
                var eventx = Math.floor(e.pageX - e.target.getX());
                var eventy = Math.floor(e.pageY - e.target.getY());

                var imagewidth = this.width;
                var imageheight = this.height;
                var factor = this.factor;
                var colour = [255, 0, 0];

                var matrices = [
                [0, 1, 0],
                [-1, 0, 0],
                [0, 0, 1],
                [0, -1, 0],
                [1, 0, 0],
                [0, 0, -1]
                ];

                var matrixcount = matrices.length;
                var limit = Math.round(imagewidth / matrixcount);
                var heightbreak = Math.round(imageheight / 2);

                for (var x = 0; x < imagewidth; x++) {
                    var divisor = Math.floor(x / limit);
                    var matrix = matrices[divisor];

                    colour[0] += matrix[0] * factor;
                    colour[1] += matrix[1] * factor;
                    colour[2] += matrix[2] * factor;

                    if (eventx == x) {
                        break;
                    }
                }

                var pixel = [colour[0], colour[1], colour[2]];
                if (eventy < heightbreak) {
                    pixel[0] += Math.floor(((255 - pixel[0]) / heightbreak) * (heightbreak - eventy));
                    pixel[1] += Math.floor(((255 - pixel[1]) / heightbreak) * (heightbreak - eventy));
                    pixel[2] += Math.floor(((255 - pixel[2]) / heightbreak) * (heightbreak - eventy));
                } else if (eventy > heightbreak) {
                    pixel[0] = Math.floor((imageheight - eventy) * (pixel[0] / heightbreak));
                    pixel[1] = Math.floor((imageheight - eventy) * (pixel[1] / heightbreak));
                    pixel[2] = Math.floor((imageheight - eventy) * (pixel[2] / heightbreak));
                }

                return this.convert_rgb_to_hex(pixel);
            },
            /**
             * Converts an RGB value to Hex
             * @param {object} rgb color
             * @return {object} string
             */
            convert_rgb_to_hex: function(rgb) {
                var hex = '';
                var hexchars = "0123456789ABCDEF";
                for (var i = 0; i < 3; i++) {
                    var number = Math.abs(rgb[i]);
                    if (number == 0 || isNaN(number)) {
                        hex += '00';
                    } else {
                        hex += hexchars.charAt((number - number % 16) / 16) + hexchars.charAt(number % 16);
                    }
                }
                return hex;
            }
        };
        /**
         * Initialise the colour popup :) Hoorah
         */
        colourpopup.init();
    });
};
