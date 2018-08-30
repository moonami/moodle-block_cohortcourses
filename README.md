# Cohort courses block

## Requirements

* [Moodle 3.1+][moodle-31]

## Purporse

The purporse of this block is to provide functionality of assigning 
set of courses to individual cohorts.

The assignments are stored in the custom table.
Once configured block will display list of courses assigned to the specific cohort in which currrently 
logged in user is not enrolled into.

## Installation

There is nothing specific to the installation process.

## Configuration

To start configuration add the block to some moodle page, for example site home. Block can be added to ANY page.
Once added block will display configuration link to the administrator. 

## Comments

* If course assigned to one or more cohorts is deleted the link is removed from the table as well.
* If cohort containing links to one or more courses is removed the links will be removed as well.
* Plugin contains [GDPR][moodle-gdpr] support and is fully compliant with Moodle 3.5+ policy

## Copyright

&copy; 2018 [Moonami LLC.][moonami-site]  Code for this plugin is licensed under the [GPLv3 license][GPLv3].

Any Moonami trademarks and logos included in these plugins are property of Moonami and should not be reused,
redistributed, modified, repurposed, or otherwise altered or used outside of this plugin.

[moodle-31]: https://docs.moodle.org/dev/Moodle_3.1_release_notes "Moodle 3.1 Release Notes"
[GPLv3]: http://www.gnu.org/licenses/gpl-3.0.html "GNU General Public License"
[moodle-gdpr]: https://docs.moodle.org/35/en/GDPR "GDPR MoodleDocs"
[moonami-site]: https://www.moonami.com/ "Moonami LLC"