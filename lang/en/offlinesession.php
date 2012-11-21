<?php

/**
 * @package    mod
 * @subpackage offlinesession
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'offlinesession';
$string['modulenameplural'] = 'offlinesessions';
$string['modulename_help'] = 'Use the offlinesession module for... | The offlinesession module allows...';
$string['offlinesessionfieldset'] = 'Custom example fieldset';
$string['offlinesessionname'] = 'offlinesession name';
$string['offlinesession:manageall'] = 'View offline session for all users';
$string['offlinesessionname_help'] = 'This is the content of the help tooltip associated with the offlinesessionname field. Markdown syntax is supported.';
$string['offlinesession'] = 'offlinesession';
$string['pluginadministration'] = 'offlinesession administration';
$string['pluginname'] = 'offlinesession';

$string['starttime'] = 'Start time';
$string['endtime'] = 'End time';
$string['duration'] = 'Duration';

$string['addofflinesession'] = 'Add a new offline session';
$string['confirmofflinesessiontobedeleted'] = 'Do you really want to delete this offline session?<br />Start time: {$a->starttime}<br />Duration: {$a->duration}<br />Description: {$a->description}';
$string['unabletoaddofflinesessiondata'] = 'Unable to add offline session data';
$string['unabletoupdateofflinesessiondata'] = 'Unable to update offline session data';
$string['unabletodeleteofflinesession'] = 'Unable to delete offline session';
$string['offlinesessiondataupdated'] = 'Offline session data updated';
$string['offlinesessiondatacanupdateuntil'] = 'Offline session data updated. You can edit until {$a}';
$string['selectanactivity'] = 'Select an activity...';

$string['description_timeout_for_blocking'] = 'Time limit after that the offline session registered will be locked (if 0 won\'t be locked)';
$string['timeout_for_blocking'] = 'Timeout to lock (minutes)';

/** errors **/
$string['endtimemustbegreaterstarttime'] = 'End time must be greater then start time';
