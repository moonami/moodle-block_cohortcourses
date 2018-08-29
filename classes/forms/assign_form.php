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

namespace block_cohortcourses\forms;

use block_cohortcourses\plugin;
use moodleform;
use MoodleQuickForm_select;
use MoodleQuickForm_button;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Class assign_form
 *
 * @package   block_cohortcourses
 * @author    Darko Miletic <dmiletic@moonami.com>
 * @copyright 2018 Moonami LLC
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_form extends moodleform {

    /**
     * @return void
     */
    protected function definition() {
        global $DB;

        $form = $this->_form;
        $options = ['aaaaaaaaaaaaaa' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'];
        for ($pos = 0, $count = 55; $pos < $count; $pos++) {
            $options["aaa$pos"] = "yeah $pos";
        }

        /** @var MoodleQuickForm_button $btn1 */
        $btn1 = $form->createElement('button', 'moveleft', '<<', ['id' => 'btnmoveleft']);
        /** @var MoodleQuickForm_button $btn2 */
        $btn2 = $form->createElement('button', 'moveright', '>>', ['id' => 'btnmoveright']);

        $itemgroup = [];
        /** @var MoodleQuickForm_select $selectedcourses */
        $selectedcourses = $form->createElement(
            'select',
            'selectedcourses',
            'Selected courses',
            $options,
            ['id' => 'block_cohortcourses_select_selectcourses']
        );
        $selectedcourses->setMultiple(true);

        /** @var MoodleQuickForm_select $availablecourses */
        $availablecourses = $form->createElement(
            'select',
            'availablecourses',
            'Available courses',
            $options,
            ['id' => 'block_cohortcourses_select_availablecourses']
        );
        $availablecourses->setMultiple(true);

        $itemgroup = [$selectedcourses, $btn1, $btn2, $availablecourses];
        $form->addGroup($itemgroup, 'assigngrp', '', ' ');
        $form->registerNoSubmitButton('moveleft');
        $form->registerNoSubmitButton('moveright');

        $this->add_action_buttons(false, get_string('itedomum', plugin::COMPONENT));
    }

}