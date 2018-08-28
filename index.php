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
 * @package    block_cohortcourses
 * @author     Darko Miletic <dmiletic@moonami.com>
 * @copyright  2018 Moonami LLC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_cohortcourses\forms\assign;

require(__DIR__.'/../../config.php');

require_login();
$PAGE->set_context(context_system::instance());
require_capability('block/cohortcourses:configure', $PAGE->context);

$PAGE->set_url('/blocks/cohortcourses/index.php');
$PAGE->set_title(get_string('configtitle', 'block_cohortcourses'));
$PAGE->set_heading(get_string('configtitle', 'block_cohortcourses'));

/** @var core_renderer $OUTPUT */
$OUTPUT;

$assign = new assign();

echo $OUTPUT->header();

$assign->display();

echo $OUTPUT->footer();