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
 * Cohort courses block.
 *
 * @package   block_cohortcourses
 * @author    Darko Miletic <dmiletic@moonami.com>
 * @copyright 2018 Moonami LLC
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_cohortcourses\output;

use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_system;
use dml_exception;
use coding_exception;
use moodle_url;
use block_cohortcourses\plugin;

defined('MOODLE_INTERNAL') || die();

/**
 * Class courses
 * @package   block_cohortcourses
 * @author    Darko Miletic <dmiletic@moonami.com>
 * @copyright 2018 Moonami LLC
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses implements renderable, templatable {

    /**
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    private function available_courses() {
        global $USER, $DB;

        $params = [
            'visible'  => true,
            'format'   => 'site',
            'ctxlevel' => CONTEXT_COURSE,
            'userid'   => $USER->id,
            'userid2'  => $USER->id,
        ];
        $sql = "SELECT c.id, c.fullname, c.shortname, c.idnumber
                  FROM {course}               c
                  JOIN {block_cohortcourses} bc ON bc.courseid = c.id
                  JOIN {cohort_members}      cm ON bc.cohortid = cm.cohortid
                 WHERE c.visible = :visible
                       AND
                       c.format <> :format
                       AND NOT EXISTS (
                         SELECT ctx.instanceid
                           FROM {role_assignments}  ra
                           JOIN {context}          ctx ON ctx.contextlevel = :ctxlevel AND ctx.id = ra.contextid
                          WHERE ra.userid = :userid AND ctx.instanceid = c.id
                       )
                       AND
                       cm.userid = :userid2
               ";
        $res = $DB->get_records_sql($sql, $params);
        $baseurl = new moodle_url('/course/view.php');
        $result = [];
        foreach ($res as $item) {
            $item->curl = $baseurl->out(false, ['id' => $item->id]);
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @param  renderer_base $output
     * @return array|stdClass|void
     */
    public function export_for_template(renderer_base $output) {
        $courses = $this->available_courses();
        $configurl = '';
        $canconfig = has_capability(plugin::CAPCONFIG, context_system::instance());
        if ($canconfig) {
            $url = new moodle_url('/blocks/cohortcourses/index.php');
            $configurl = $url->out(false);
        }
        $result = [
            'showconfig' => $canconfig,
            'configurl'  => $configurl,
            'uniqid'     => random_string(4),
            'hascourses' => !empty($courses),
            'courses'    => $courses
        ];
        return $result;
    }

}
