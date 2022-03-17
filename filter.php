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
 * Version details
 *
 * @package    filter
 * @subpackage units
 * @copyright  tim.stclair@gmail.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/completionlib.php');

class filter_units extends moodle_text_filter {

    public function filter($text, array $options = array()) {
        global $COURSE;

        if (!is_string($text) or empty($text)) {
            // non string data can not be filtered anyway
            return $text;
        }

        // TODO make these configurable
        $identifiers = [
            '1' => 'Core',
            '2' => 'Elective',
            '3' => 'Optional',
            '4' => 'Introduction',
        ];

        // we need courses as a IteratorAggregate so we can use lookup methods on the course instances
        $category = core_course_category::get($COURSE->category);
        $pool = $category->get_courses(array('recursive' => false, 'summary' => false));
        $current = $COURSE->id;

        // do the replacements
        foreach ($identifiers as $value => $label) {
            if (stripos($text, "[[units::{$label}]]") !== false) {
                $html = filter_units::get_units($pool, $current, $value);
                $text = str_ireplace("[[units::{$label}]]", $html, $text);
            }
        }

        return $text;
    }

    static function get_units($pool, $current_course, $attrib_value) {
    global $CFG, $PAGE, $USER, $OUTPUT;

        // filter the list of courses to just those that match the unittype value
        $courses = course_filter_courses_by_customfield($pool, 'unittype', $attrib_value);

        $html = [];
        foreach ($courses[0] as $course) {

            if ($course->id === $current_course) continue; // skip self

            // locate course image, if any
            $image = '';
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                if ($isimage) {
                $image = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                }
            }

            $is_completed = false;
            $info = new completion_info($course);
            if (completion_info::is_enabled_for_site() && $info->is_enabled()) {
                if ($info->is_course_complete($USER->id)) {
                    $is_completed = true;
                }
            }


            // draw a figure / figcaption for the course
            $html[] = \html_writer::start_tag('figure', ['class' => 'coursebox']);
            $link = new \moodle_url('/course/view.php', array('id' => $course->id));
            // proxy course link through an opener url that sets this page as the coursehome in the session
            $url = new \moodle_url('/filter/units/open.php', array('from' => $PAGE->url->out(), 'to' => $link->out(true)));
            if (!empty($image)) $html[] = \html_writer::link($url, \html_writer::empty_tag('img', array('src' => $image)));
            $html[] = \html_writer::start_tag('figcaption');
            $html[] = \html_writer::tag('span', \html_writer::link($url, $course->shortname), ['class' => 'course-id']);
            $html[] = \html_writer::tag('span', \html_writer::link($url, $course->fullname), ['class' => 'course-title']);
            $html[] = \html_writer::end_tag('figcaption');
            if ($is_completed) {
                $html[] = \html_writer::tag('span', $OUTPUT->pix_icon('t/check',get_string('complete')), ['class' => 'completed']);
            }
            $html[] = \html_writer::end_tag('figure');

        }

        // return the html
        return implode('', $html);

    }
}