<?php

/**
 * @package    mod
 * @subpackage offlinesession
 * @author     Domenico Pontari <fairsayan@gmail.com>
 * @copyright  2012 Institute of Tropical Medicine - Antwerp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$module->version   = 2012060700;           // If version == 0 then module will not be installed
$module->requires  = 2011051000;           // Requires this Moodle version
$module->cron      = 0;                    // Period for cron to check this module (secs)
$module->release   = '2.2.x (Build: 2011051000)';
$module->maturity  = 'MATURITY_RC';
$module->component = 'mod_offlinesession'; // To check on upgrade, that module sits in correct place
