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
/* Imports */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/gdlib.php');

$logverbose = optional_param('logverbose', 0, PARAM_INT);  // Set to 1 to have verbose logging.
$crop = optional_param('crop', 0, PARAM_INT);  // Set to 1 to have cropped images.

/* Script settings */
define('TRAIL_ITEM_IMAGE_WIDTH', 210);
define('TRAIL_ITEM_IMAGE_HEIGHT', 140);

/**
 * Get trail ids courses.
 *
 * @return array
 */
function trail_get_courseids() {
    global $DB;

    if (!$courseids = $DB->get_records('format_trail_icon', null, '', 'courseid')) {
        $courseids = false;
    }
    return $courseids;
}
/**
 * Get ids courses.
 *
 * @return array
 */
function course_get_courseids() {
    global $DB;

    if (!$courseids = $DB->get_records('course', null, '', 'id')) {
        $courseids = false;
    }
    return $courseids;
}
/**
 * Get icons.
 *
 * @param int $courseid
 * @return string
 */
function trail_get_icons($courseid) {
    global $DB;

    if (!$courseid) {
        return false;
    }

    if (!$sectionicons = $DB->get_records('format_trail_icon', array('courseid' => $courseid), '', 'sectionid, image')) {
        $sectionicons = false;
    }
    return $sectionicons;
}
/**
 * Get files.
 *
 * @return string
 */
function trail_files() {
    global $DB;

    if (!$sectionicons = $DB->get_records('files', null, '', 'pathnamehash, contextid, component, filearea, filepath, filename')) {
        $sectionicons = false;
    }
    return $sectionicons;
}

$courseids = course_get_courseids();
if ($logverbose) {
    echo('<p>Course ids: ' . $courseids . '.</p>');
}

if ($courseids) {

    $fs = get_file_storage();

    if ($logverbose) {
        $sectionfiles = trail_files();
        if ($sectionfiles) {
            echo('<p>Files table before: ' . $sectionfiles . '.</p>');
        }
    }

    foreach ($courseids as $course) {
        $courseid = $course->id;
        if ($courseid == 1) {
            // Site course.
            continue; // Normally I dislike goto's.
        }
        $sectionicons = trail_get_icons($courseid);

        if ($sectionicons) {
            $context = context_course::instance($courseid);
            $contextid = $context->id;

            if ($contextid) {
                if ($logverbose) {
                    echo('<p>Section icons: ' . $sectionicons . '.</p>');
                }

                if ($sectionicons) {
                    if ($logverbose) {
                        echo('<p>Converting legacy images ' . $sectionicons . ".</p>");
                    }
                    foreach ($sectionicons as $sectionicon) {

                        if (isset($sectionicon->image)) {
                            echo('<p>Converting legacy image ' . $sectionicon->image . ".</p>");

                            if ($tempfile = $fs->get_file($contextid, 'course', 'legacy', 0, '/icons/', $sectionicon->image)) {

                                echo('<p> Stored file:' . $tempfile . '</p>');
                                // Resize the image and save it...
                                $created = time();
                                $storedfilerecord = array(
                                    'contextid' => $contextid,
                                    'component' => 'course',
                                    'filearea' => 'section',
                                    'itemid' => $sectionicon->sectionid,
                                    'filepath' => '/',
                                    'filename' => $sectionicon->image,
                                    'timecreated' => $created,
                                    'timemodified' => $created);

                                try {
                                    $convertsuccess = true;
                                    $mime = $tempfile->get_mimetype();

                                    $storedfilerecord['mimetype'] = $mime;

                                    $tmproot = make_temp_directory('trailformaticon');
                                    $tmpfilepath = $tmproot . '/' . $tempfile->get_contenthash();
                                    $tempfile->copy_content_to($tmpfilepath);

                                    $data = generate_image($tmpfilepath, TRAIL_ITEM_IMAGE_WIDTH, TRAIL_ITEM_IMAGE_HEIGHT, $crop);
                                    if (!empty($data)) {
                                        $fs->create_file_from_string($storedfilerecord, $data);
                                    } else {
                                        $convertsuccess = false;
                                    }
                                    unlink($tmpfilepath);

                                    if ($convertsuccess == false) {
                                        print('<p>Image ' . $sectionicon->image . ' failed to convert.</p>');
                                    } else {
                                        print('<p>Image ' . $sectionicon->image . ' converted.</p>');

                                        // Clean up and remove the old thumbnail too.
                                        $tempfile->delete();
                                        unset($tempfile);
                                        if ($tempfile = $fs->get_file($contextid, 'course',
                                                'legacy', 0, '/icons/', 'tn_' . $sectionicon->image)) {
                                            // Remove thumbnail.
                                            $tempfile->delete();
                                            unset($tempfile);
                                        }
                                    }
                                } catch (Exception $e) {
                                    if (isset($tempfile)) {
                                        $tempfile->delete();
                                        unset($tempfile);
                                    }
                                    print('Trail Format Convert Image Exception:...');
                                    debugging($e->getMessage());
                                }
                            } else {
                                echo('<p>Image ' . $sectionicon->image . ' could not be found in the legacy files.</p>');
                            }
                        } else {
                            echo('<p>Section icon not set for course id: ' . $courseid . ', section id: '
                                    . $sectionicon->sectionid . '.</p>');
                        }
                    }
                } else {
                    echo('<p>No section icons found for course id: ' . $courseid . '.</p>');
                }
            } else {
                echo('<p>Cannot get context id for course id: ' . $courseid . '.</p>');
            }
        } else {
            echo('<p>Course id: ' . $courseid . ', is not a Trail format course or cannot get the sections for it.</p>');
        }
    }
    if ($logverbose) {
        $sectionfiles = trail_files();
        if ($sectionfiles) {
            echo('<p>Files table after: ' . $sectionfiles . '.</p>');
        }
    }
} else {
    echo('<p>Cannot get list of course ids from format_trail_icon table.</p>');
}
