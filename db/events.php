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
 * Observer registration
 *
 * @package   block_cohortcourses
 * @copyright 2018 Moonami, LLC
 * @author    Darko Miletic <dmiletic@moonami.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname'   => '\core\event\cohort_deleted',
        'callback'    => 'block_cohortcourses_cohort_deleted',
        'includefile' => '/blocks/cohortcourses/listeners.php'
    ],
    [
        'eventname'   => '\core\event\course_deleted',
        'callback'    => 'block_cohortcourses_course_deleted',
        'includefile' => '/blocks/cohortcourses/listeners.php'
    ],
];
