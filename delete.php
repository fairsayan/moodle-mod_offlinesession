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
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/edit_form.php');

$dataid = required_param('dataid', PARAM_INT);
$confirmed = optional_param('confirmed', false, PARAM_BOOL); // confirmed deletion

$offlinesession_data  = $DB->get_record('offlinesession_data', array('id' => $dataid), '*', MUST_EXIST);
$offlinesession = $DB->get_record('offlinesession', array('id' => $offlinesession_data->offlinesessionid), '*', MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $offlinesession->course), '*', MUST_EXIST);
$cm             = get_coursemodule_from_instance('offlinesession', $offlinesession->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$see_all = has_capability('mod/offlinesession:manageall', $context);

/// Print the page header

$PAGE->set_url('/mod/offlinesession/view.php', array('id' => $offlinesession_data->id));
$PAGE->set_title(format_string($offlinesession->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if (!$see_all && $USER->id != $offlinesession_data->userid)
    echo notice(get_string('accessdenied', 'admin'), $CFG->wwwroot.'/mod/offlinesession/view.php?id='.$cm->id, $course);

if ($confirmed) 
    add_to_log($course->id, 'offlinesession', 'delete', "delete.php?dataid=$dataid", $offlinesession->name, $cm->id);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('offlinesession-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($confirmed) {
    if (!$DB->delete_records('offlinesession_data', array('id'=> $dataid)))
        notice (get_string("unabletodeleteofflinesession", 'offlinesession'));
    redirect($CFG->wwwroot.'/mod/offlinesession/view.php?id='.$cm->id, get_string("offlinesessiondataupdated", 'offlinesession'));
}


$msg_data->starttime = userdate($offlinesession_data->starttime);
$msg_data->duration = format_time($offlinesession_data->duration);
$msg_data->description = $offlinesession_data->description;
echo $OUTPUT->confirm (
                get_string("confirmofflinesessiontobedeleted", 'offlinesession', $msg_data),
                "delete.php?confirmed=true&dataid=$dataid",
                $CFG->wwwroot.'/mod/teacherdiary/view.php?id='.$cm->id
);

// Finish the page
echo $OUTPUT->footer();
