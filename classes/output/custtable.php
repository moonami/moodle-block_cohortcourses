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

use block_cohortcourses\plugin;
use moodle_url;
use context;
use html_writer;
use html_table;
use core_renderer;
use moodle_page;
use html_table_row;
use coding_exception;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->libdir.'/filelib.php');

/**
 * Class custtable
 * @package   block_cohortcourses
 * @author    Darko Miletic <dmiletic@moonami.com>
 * @copyright 2018 Moonami LLC
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custtable {

    /**
     * @var string
     */
    protected $content = '';

    public function __toString() {
        return $this->content;
    }

    /**
     * custtable constructor.
     *
     * @param  object $output
     * @param  moodle_url $pageurl
     * @param  int $pagenr
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function __construct($output, moodle_url $pageurl, $pagenr) {
        $cohorts = cohort_get_all_cohorts($pagenr, plugin::PERPAGE);
        $data = [];
        foreach ($cohorts['cohorts'] as $cohort) {
            $line = [];
            $cohortcontext = context::instance_by_id($cohort->contextid);
            $cohort->description = file_rewrite_pluginfile_urls(
                $cohort->description,
                'pluginfile.php',
                $cohortcontext->id,
                'cohort',
                'description',
                $cohort->id
            );
            // Category.
            if ($cohortcontext->contextlevel == CONTEXT_COURSECAT) {
                $line[] = html_writer::link(
                    new moodle_url('/cohort/index.php', ['contextid' => $cohort->contextid]),
                    $cohortcontext->get_context_name(false)
                );
            } else {
                $line[] = $cohortcontext->get_context_name(false);
            }
            // Name.
            $line[] = $cohort->name;
            // Cohort id.
            $line[] = $cohort->idnumber;
            // Description.
            $line[] = format_text($cohort->description, $cohort->descriptionformat);
            // Count.
            $line[] = 0;
            // Component.
            if (empty($cohort->component)) {
                $line[] = get_string('nocomponent', 'cohort');
            } else {
                $line[] = get_string('pluginname', $cohort->component);
            }

            $urlparams = ['id' => $cohort->id, 'returnurl' => $pageurl->out_as_local_url(true, [plugin::PARAMNAME => $pagenr])];
            $buttons = [
                html_writer::link(
                    new moodle_url('/blocks/cohortcourses/assign.php', $urlparams),
                    $output->pix_icon('t/preferences', get_string('assign', 'core_cohort')),
                    ['title' => get_string('assign', 'core_cohort')]
                )
            ];
            // Edit.
            $line[] = implode(' ', $buttons);
            $data[] = new html_table_row($line);
        }

        $table = new html_table();
        $table->head  = [
            get_string('category'),
            get_string('name', 'cohort'),
            get_string('idnumber', 'cohort'),
            get_string('description', 'cohort'),
            get_string('memberscount', 'cohort'),
            get_string('component', 'cohort'),
            get_string('edit'),
        ];

        $table->colclasses = [
            'leftalign category',
            'leftalign name',
            'leftalign id',
            'leftalign description',
            'leftalign size',
            'centeralign source',
            'centeralign action'
        ];
        $table->id = 'cohorts';
        $table->attributes['class'] = 'admintable generaltable';
        $table->data = $data;

        $pagebar = $output->paging_bar(
            $cohorts['totalcohorts'],
            $pagenr,
            plugin::PERPAGE,
            $pageurl->out(true, [plugin::PARAMNAME => $pagenr]),
            plugin::PARAMNAME
        );

        $this->content = $pagebar.html_writer::table($table).$pagebar;
    }

}