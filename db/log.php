<?php

/**
 * @package    mod
 * @subpackage offlinesession
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module'=>'offlinesession', 'action'=>'add', 'mtable'=>'offlinesession', 'field'=>'name'),
    array('module'=>'offlinesession', 'action'=>'update', 'mtable'=>'offlinesession', 'field'=>'name'),
    array('module'=>'offlinesession', 'action'=>'view', 'mtable'=>'offlinesession', 'field'=>'name'),
    array('module'=>'offlinesession', 'action'=>'view all', 'mtable'=>'offlinesession', 'field'=>'name')
);
