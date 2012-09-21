<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('offlinesession_timeout_for_blocking', get_string('timeout_for_blocking', 'offlinesession'),
                       get_string('description_timeout_for_blocking', 'offlinesession'), 30, PARAM_INT));

}
