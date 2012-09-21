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

$dataid = optional_param('dataid', 0, PARAM_INT); // offlinesessionid
$offlinesessionid = optional_param('offlinesessionid', 0, PARAM_INT); // id

if ($dataid) {
    $offlinesession_data  = $DB->get_record('offlinesession_data', array('id' => $dataid), '*', MUST_EXIST);
    $offlinesessionid = $offlinesession_data->offlinesessionid;
} elseif (!$offlinesessionid) {
    error('You must specify a data ID or an offline session ID');
}

$offlinesession = $DB->get_record('offlinesession', array('id' => $offlinesessionid), '*', MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $offlinesession->course), '*', MUST_EXIST);
$cm             = get_coursemodule_from_instance('offlinesession', $offlinesession->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$see_all = has_capability('mod/offlinesession:manageall', $context);


/// Print the page header

$PAGE->set_url('/mod/offlinesession/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($offlinesession->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if (!$see_all && $dataid && $USER->id != $offlinesession_data->userid)
    echo notice(get_string('accessdenied', 'admin'), $CFG->wwwroot.'/mod/offlinesession/view.php?id='.$cm->id, $course);

add_to_log($course->id, 'offlinesession', 'edit data', "edit.php?dataid=$dataid&offlinesessionid=$offlinesessionid", $offlinesession->name, $cm->id);


// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('offlinesession-'.$somevar);

// Output starts here
echo $OUTPUT->header();


$editform = new offlinesession_edit_form('edit.php', null, 'post', '', 'class="offlinesessioneditform"');
$displayform = true;
if ($editform->is_cancelled()) redirect($CFG->wwwroot.'/mod/offlinesession/view.php?id='.$cm->id);
elseif ($data = $editform->get_data()) {
    global $USER;
    
    if ($dataid) $offlinesession_data->id = $dataid;
    $offlinesession_data->starttime = mktime ($data->starthour, $data->startminute, 0, $data->month, $data->day, $data->year);
    $offlinesession_data->duration = $data->durationhour * 3600 + $data->durationminute *60;
    $offlinesession_data->description = $data->description;
    $offlinesession_data->offlinesessionid = $data->offlinesessionid;
    $offlinesession_data->userid = $USER->id;
    if ($data->cmid) $offlinesession_data->cmid = $data->cmid;
    if (!$dataid) {
        $offlinesession_data->timecreated = time();
        $offlinesession_data->id = $DB->insert_record('offlinesession_data', $offlinesession_data);
        if (!$offlinesession_data->id) notice(get_string("unabletoaddofflinesessiondata", 'offlinesession'));
    } elseif (!$DB->update_record ('offlinesession_data', $offlinesession_data))
        notice(get_string("unabletoupdateofflinesessiondata", 'offlinesessiondata'));

    
    if ($CFG->offlinesession_timeout_for_blocking)
        $message = get_string("offlinesessiondatacanupdateuntil", 'offlinesession', date('D, d M Y H:i:s')); 
        else $message = get_string("offlinesessiondataupdated", 'offlinesession'); 
    redirect($CFG->wwwroot.'/mod/offlinesession/view.php?id='.$cm->id, $message);
    $displayform = false;
}

$renderer = new MoodleOfflineSessionEditForm_Renderer();

$editform->get_form()->accept($renderer);
if ($displayform) echo $renderer->toHtml();



// Finish the page
echo $OUTPUT->footer();
