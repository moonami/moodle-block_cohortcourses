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
use MoodleQuickForm_submit;
use coding_exception;
use dml_exception;

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
     * assign_form constructor.
     * @param string $action
     * @param mixed $customdata
     * @param string $method
     * @param string $target
     * @param array $attributes
     * @param bool $editable
     * @param array|null $ajaxformdata
     */
    public function __construct($action = null, $customdata = null, $method = 'post', $target = '',
                                $attributes = null, $editable = true, array $ajaxformdata = null) {
        $attrs = ['id' => 'block_cohortcourses_assign_form'];
        if (!empty($attributes)) {
            $attrs += $attributes;
        }
        parent::__construct($action, $customdata, $method, $target, $attrs, $editable, $ajaxformdata);
    }

    /**
     * @param  int $cohortid
     * @return array
     */
    protected function getcohortcourses($cohortid) {
        global $DB;

        $sql = "SELECT bc.courseid, c.fullname
                  FROM {block_cohortcourses} bc
                  JOIN {cohort}              ch ON ch.id = bc.cohortid
                  JOIN {course}               c ON c.id = bc.courseid
                 WHERE bc.cohortid = :cohortid
               ";

        return $DB->get_records_sql_menu($sql, ['cohortid' => $cohortid]);
    }

    /**
     * @param  int $cohortid
     * @return array
     */
    protected function getavailablecourses($cohortid) {
        global $DB;

        $sql = "SELECT c.id, c.fullname
                  FROM {course} c
                 WHERE NOT EXISTS (
                  SELECT bc.courseid
                    FROM {block_cohortcourses} bc
                    JOIN {cohort}              ch ON ch.id = bc.cohortid
                    JOIN {course}               c ON c.id = bc.courseid
                   WHERE bc.cohortid = :cohortid
                         AND
                         bc.courseid = c.id
                 )
                 AND
                 c.format <> :format
               ";

        return $DB->get_records_sql_menu($sql, ['cohortid' => $cohortid, 'format' => 'site']);
    }

    /**
     * @param  int $cohortid
     * @param  array $courses
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function addtocohort($cohortid, array $courses) {
        global $DB;
        $objects = [];
        foreach ($courses as $courseid) {
            $objects[] = (object)['courseid' => $courseid, 'cohortid' => $cohortid];
        }
        $DB->insert_records(plugin::TABLE, $objects);
    }

    /**
     * @param int $cohortid
     * @param  array $courses
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function removefromcohort($cohortid, array $courses) {
        global $DB;
        list($insql, $params) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
        $params['cohortid'] = $cohortid;
        $DB->delete_records_select(plugin::TABLE, "cohortid = :cohortid AND courseid $insql", $params);
    }

    /**
     * @return void
     */
    protected function definition() {
        global $DB;

        $form = $this->_form;
        if (empty($this->_customdata['cohort'])) {
            print_error('invalidarguments', 'core_error');
        }
        $cohort = $this->_customdata['cohort'];

        /** @var MoodleQuickForm_submit $btn1 */
        $btn1 = $form->createElement('submit', 'moveleft', '<<', ['id' => 'btnmoveleft']);
        /** @var MoodleQuickForm_submit $btn2 */
        $btn2 = $form->createElement('submit', 'moveright', '>>', ['id' => 'btnmoveright']);

        $itemgroup = [];
        /** @var MoodleQuickForm_select $selectedcourses */
        $selectedcourses = $form->createElement(
            'select',
            'selectedcourses',
            get_string('selectedcourses', plugin::COMPONENT, $cohort->name).'<br>',
            $this->getcohortcourses($cohort->id),
            ['id' => 'block_cohortcourses_select_selectcourses']
        );
        $selectedcourses->setMultiple(true);

        /** @var MoodleQuickForm_select $availablecourses */
        $availablecourses = $form->createElement(
            'select',
            'availablecourses',
            get_string('availablecourses', plugin::COMPONENT).'<br>',
            $this->getavailablecourses($cohort->id),
            ['id' => 'block_cohortcourses_select_availablecourses']
        );
        $availablecourses->setMultiple(true);

        $itemgroup = [$selectedcourses, $btn1, $btn2, $availablecourses];
        $form->addGroup($itemgroup, 'assigngrp', '', ' ');

        // Select labels are hidden by default when being part of a group.
        $selectedcourses->setHiddenLabel(false);
        $availablecourses->setHiddenLabel(false);
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    public function definition_after_data() {
        $cohort = $this->_customdata['cohort'];
        if (($data = $this->get_data()) !== null) {
            if (!empty($data->assigngrp['moveleft']) and !empty($data->assigngrp['availablecourses'])) {
                $this->addtocohort($cohort->id, $data->assigngrp['availablecourses']);
            }
            if (!empty($data->assigngrp['moveright']) and !empty($data->assigngrp['selectedcourses'])) {
                $this->removefromcohort($cohort->id, $data->assigngrp['selectedcourses']);
            }
        }
    }

}