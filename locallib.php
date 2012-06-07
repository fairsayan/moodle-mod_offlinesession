<?php

/**
 * @package    mod
 * @subpackage offlinesession
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__).'/../../lib/outputrenderers.php');

function offlinesession_get_list ($offlinesession, $editing = true) {
    global $OUTPUT;
    global $DB;
    
    $add_new_string = get_string('addofflinesession', 'offlinesession');
    $result = <<<EOD
<table id="offlinesession_list_table" cellpadding="5" rules="rows" frame="below">
    <col width="50" />
    <caption><a href="edit.php?offlinesessionid=$offlinesession->id">$add_new_string</a></caption>

EOD;
    $rows = $DB->get_records('offlinesession_data', array('offlinesessionid' => $offlinesession->id), 'starttime DESC');
    if (empty($rows)) return $OUTPUT->notification(get_string('nothingtodisplay'));
    foreach ($rows as $row) {
        $result .= offlinesession_get_list_table_title ($row, $editing);
        break; // use just the first record
    }
    foreach ($rows as $row) $result .= offlinesession_get_list_table_row ($row, $editing);
    $result .= "</table>\n";
    return $result;
}

function offlinesession_get_list_table_title ($row, $editing) {
    $result = "\t<tr>\n";
    if ($editing) $result .= "\t\t<th></th>\n"; // editing cell title: blank
    foreach ($row as $name => $data) {
        if (in_array($name, array('id', 'offlinesessionid'))) continue;
        switch ($name){
            case 'userid':
                $result .= "\t\t<th>" . get_string("user") . "</th>\n";
                break;
            case 'cmid':
                $result .= "\t\t<th>" . get_string("activity") . "</th>\n";
                break;
            case 'description':
                $result .= "\t\t<th>" . get_string("description") . "</th>\n";
                break;
            default:
                $result .= "\t\t<th>" . get_string($name, 'offlinesession') . "</th>\n";
        }
    }
    $result .= "\t</tr>\n";
    return $result;
}

function offlinesession_get_list_table_row ($row, $editing) {
    global $OUTPUT;
    global $DB;
    global $modinfo;

    $result = "\t<tr>\n";
    if ($editing) {
        $result .= "\t\t<td>";
        $result .= '<a href="edit.php?dataid=' . $row->id . '"><img class="editing_img" src="' . $OUTPUT->pix_url('t/edit') . '" /></a>';
        $result .= '<a href="delete.php?dataid=' . $row->id . '"><img class="editing_img" src="' . $OUTPUT->pix_url('t/delete'). '" /></a>';
        $result .= "</td>\n";
    }
    foreach ($row as $name => $data) {
        if (in_array($name, array('id', 'offlinesessionid'))) continue;
        switch ($name) {
            case 'userid':
                $user = $DB->get_record('user', array('id' => $data));
                $result .= "\t\t<td>" . fullname($user) . "</td>\n";
                break;
            case 'starttime':
                $date = userdate($data);
                $result .= "\t\t<td>$date</td>\n";
                break;
            case 'duration':
                $date = format_time($data);
                $result .= "\t\t<td>$date</td>\n";
                break;
            case 'cmid':
                $name = $modinfo->cms[$data]->name;
                $result .= "\t\t<td>$name</td>\n";
                break;
            default:
                $result .= "\t\t<td>$data</td>\n";
        }
    }
    $result .= "\t</tr>\n";
    return $result;
}

