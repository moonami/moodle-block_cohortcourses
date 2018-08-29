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

use block_cohortcourses\output\courses;

defined('MOODLE_INTERNAL') || die();

/**
 * Class block_cohortcourses
 *
 * @package   block_cohortcourses
 * @author    Darko Miletic <dmiletic@moonami.com>
 * @copyright 2018 Moonami LLC
 */
class block_cohortcourses extends block_base {

    const COMPONENT = 'block_cohortcourses';

    /**
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', self::COMPONENT);
    }

    /**
     * @return array
     */
    public function applicable_formats() {
        return ['all' => true];
    }

    /**
     * @return stdClass|stdObject
     * @throws coding_exception
     */
    public function get_content() {
        if (isloggedin() and empty($this->content)) {
            $renderable = new courses();
            $renderer = $this->page->get_renderer(self::COMPONENT);

            $this->content = new stdClass();
            $this->content->text = $renderer->render($renderable);
            $this->content->footer = '';
        }

        return $this->content;
    }

}
