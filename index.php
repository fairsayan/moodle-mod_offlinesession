<?php

/**
 * @package    mod
 * @subpackage offlinesession
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // course

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

add_to_log($course->id, 'offlinesession', 'view all', 'index.php?id='.$course->id, '');

$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);

$PAGE->set_url('/mod/offlinesession/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

if (! $offlinesessions = get_all_instances_in_course('offlinesession', $course)) {
    notice(get_string('noofflinesessions', 'offlinesession'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

if ($course->format == 'weeks') {
    $table->head  = array(get_string('week'), get_string('name'));
    $table->align = array('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array(get_string('topic'), get_string('name'));
    $table->align = array('center', 'left', 'left', 'left');
} else {
    $table->head  = array(get_string('name'));
    $table->align = array('left', 'left', 'left');
}

foreach ($offlinesessions as $offlinesession) {
    if (!$offlinesession->visible) {
        $link = html_writer::link(
            new moodle_url('/mod/offlinesession.php', array('id' => $offlinesession->coursemodule)),
            format_string($offlinesession->name, true),
            array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/offlinesession.php', array('id' => $offlinesession->coursemodule)),
            format_string($offlinesession->name, true));
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($offlinesession->section, $link);
    } else {
        $table->data[] = array($link);
    }
}

echo $OUTPUT->heading(get_string('modulenameplural', 'offlinesession'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();
