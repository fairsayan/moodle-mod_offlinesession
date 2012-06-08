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

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // offlinesession instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('offlinesession', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $offlinesession  = $DB->get_record('offlinesession', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $offlinesession  = $DB->get_record('offlinesession', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $offlinesession->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('offlinesession', $offlinesession->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$see_all = has_capability('mod/offlinesession:manageall', $context);
$modinfo = get_fast_modinfo($course);

add_to_log($course->id, 'offlinesession', 'view', "view.php?id={$cm->id}", $offlinesession->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/offlinesession/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($offlinesession->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('offlinesession-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($offlinesession->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('offlinesession', $offlinesession, $cm->id), 'generalbox mod_introbox', 'offlinesessionintro');
}

$content = offlinesession_get_list($offlinesession, $see_all);

echo $OUTPUT->box($content, 'generalbox');


// Finish the page
echo $OUTPUT->footer();
