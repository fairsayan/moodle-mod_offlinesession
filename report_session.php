<?php

/**
 * @package    mod
 * @subpackage offlinesession
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function _offlinesession_get_sql_from_str ($userid, $courseid, $cmid) {
    $str_conditions = array();
    
    if ($userid)     array_push($str_conditions, 'od.userid = :userid');
    if ($courseid)   array_push($str_conditions, 'o.course =  :courseid');
    if ($cmid)       array_push($str_conditions, 'od.cmid =   :cmid');
    if (!empty($str_conditions)) $str_condition = 'WHERE ' . implode(' AND ', $str_conditions);
    else $str_condition = '';
    
    $sql_from_str = "FROM {offlinesession_data} od JOIN {offlinesession} o
        ON (od.offlinesessionid = o.id) $str_condition";
    
    return $sql_from_str;
}

/**
 *  Return standard offline session list in report session format.
 *  
 *  Format:
 *      - starttime (EPOCH)
 *      - endtime (EPOCH)
 *      - duration (seconds)
 *      - userid (0 => all users)
 *      - courseid (0 => all courses)
 *      - cmid (0 => all cms)
 *      - description
 *      - session type
 *      
 */
function offlinesession_get ($userid, $courseid, $cmid) {
    global $DB;
    $sessions = array();
    
    $data_conditions = array (
                    'userid' => $userid,
                    'courseid' => $courseid,
                    'cmid' => $cmid
    );
    
    $sql_from_str = _offlinesession_get_sql_from_str ($userid, $courseid, $cmid);
    $sql = "SELECT od.*, o.course AS courseid $sql_from_str";
    $result = $DB->get_records_sql ($sql, $data_conditions);
    foreach ($result as $row) {
        $data->starttime = $row->starttime;
        $data->endtime = $row->starttime + $row->duration;
        $data->duration = $row->duration;
        $data->userid = $row->userid;
        $data->courseid = $row->courseid;
        if ($row->cmid) $data->cmid = $row->cmid; else $data->cmid = NULL;
        $data->description = $row->description;
        $data->type = 'offline';
        array_push ($sessions, $data);
    }
    
    return $sessions;
}

/**
 *  Return a sigle session with aggregated values.
 *
 *  Format:
 *      - duration (seconds)
 *      - userid (0 => all users)
 *      - courseid (0 => all courses)
 *      - cmid (0 => all cms)
 *      - session type
 *
 */
function offlinesession_get_aggregated ($userid, $courseid, $cmid) {
    global $DB;
    
    $result->userid = $userid;
    $result->courseid = $courseid;
    $result->cmid = $cmid;
    $str_conditions = array();
    
    $data_conditions = array (
                    'userid' => $userid,
                    'courseid' => $courseid,
                    'cmid' => $cmid
    );
    
    $sql_from_str = _offlinesession_get_sql_from_str ($userid, $courseid, $cmid);
    $sql = "SELECT SUM(od.duration) AS duration $sql_from_str";
    $result->duration = $DB->get_field_sql ($sql, $data_conditions);
        
    return $result;
}

