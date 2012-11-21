<?php

require_once(dirname(__FILE__).'/../../lib/formslib.php');

class offlinesession_edit_form extends moodleform {
    function get_form () {
        return $this->_form;
    }
    
    function definition() {
        global $offlinesession;
        global $dataid;
        global $course;
        $modinfo = get_fast_modinfo($course);
        $mform =& $this->_form;

        $now = getdate();
        $curryear = (int) $now['year'];
        for ($i = 1; $i <= 31; $days["$i"] = $i++);
        for ($i = 1; $i <= 12; $months["$i"] = $i++);
        for ($i = $curryear - 5; $i <= $curryear + 5; $years["$i"] = $i++);
        for ($i = 0; $i <= 23; $hours["$i"] = $i++);
        for ($i = 0; $i < 60; $i+= 5) $minutes["$i"] = sprintf("%02d", $i);
        
        $cmids["0"] = get_string('selectanactivity', 'offlinesession');
        foreach ($modinfo->cms as $cm) $cmids["$cm->id"] = $cm->name;

        if ($dataid) {
            global $offlinesession_data;
            $starttime_obj = getdate($offlinesession_data->starttime);
            $endtime_obj = getdate($offlinesession_data->starttime + $offlinesession_data->duration);

            $default_day   =  $starttime_obj['mday'];
            $default_month =  $starttime_obj['mon'];
            $default_year  =  $starttime_obj['year'];
            $default_starthour        = $starttime_obj['hours'];
            $default_startminute      = $starttime_obj['minutes'];
            $default_endhour     = $endtime_obj['hours'];
            $default_endminute   = $endtime_obj['minutes'];
            $default_description      = $offlinesession_data->description;
            if ($offlinesession_data->cmid) $default_cmid = $offlinesession_data->cmid;
                else $default_cmid = '0';
        } else {
            $default_day   =  $now['mday'];
            $default_month =  $now['mon'];
            $default_year  =  $curryear;
            $default_starthour      = '8';
            $default_startminute    = '00';
            $default_endhour   = '8';
            $default_endnminute = '00';
            $default_description    = '';
            $default_cmid = '0';
        }

//-------------------------------------------------------------------------------
        $mform->addElement('select', 'day', get_string('date'), $days);
        $mform->setType('day', PARAM_INT);
        $mform->addRule('day', null, 'required', null, 'client');
        $mform->setDefault('day', $default_day);

        $mform->addElement('select', 'month', '', $months);
        $mform->setType('month', PARAM_INT);
        $mform->setDefault('month', $default_month);

        $mform->addElement('select', 'year', '', $years);
        $mform->setType('year', PARAM_INT);
        $mform->setDefault('year', $default_year);

        $mform->addElement('select', 'starthour', get_string('starttime', 'offlinesession'), $hours);
        $mform->setType('starthour', PARAM_INT);
        $mform->addRule('starthour', null, 'required', null, 'client');
        $mform->setDefault('starthour', $default_starthour);

        $mform->addElement('select', 'startminute', '', $minutes);
        $mform->setType('startminute', PARAM_INT);
        $mform->setDefault('startminute', $default_startminute);

        $mform->addElement('select', 'endhour', get_string('endtime', 'offlinesession'), $hours);
        $mform->setType('endhour', PARAM_INT);
        $mform->addRule('endhour', null, 'required', null, 'client');
        $mform->setDefault('endhour', $default_endhour);

        $mform->addElement('select', 'endminute', '', $minutes);
        $mform->setType('endminute', PARAM_INT);
        $mform->setDefault('endminute', $default_endminute);

        $mform->addElement('select', 'cmid', get_string('activity'), $cmids);
        $mform->setType('cmid', PARAM_INT);
        $mform->setDefault('cmid', $default_cmid);

        $mform->addElement('textarea', 'description', get_string('description'));
        $mform->setType('description', PARAM_RAW);
        $mform->setDefault('description', $default_description);

        if ($dataid) $mform->addElement('hidden', 'dataid', $dataid);
        $mform->addElement('hidden', 'offlinesessionid', $offlinesession->id);

        $this->add_action_buttons();
    }
    
    function validation ($data) {
        $errors = array();
        $duration = ($data['endhour'] - $data['starthour']) * 3600 + ($data['endminute'] - $data['startminute'])  * 60;
        if ($duration <= 0) $errors['endminute'] = get_string('endtimemustbegreaterstarttime', 'offlinesession');

        return empty($errors)?true:$errors;
    }
}

class MoodleOfflineSessionEditForm_Renderer extends MoodleQuickForm_Renderer {
    function renderElement(&$element, $required, $error){
        // Make sure the element has an id.
        $element->_generateId();

            //adding stuff to place holders in template
        switch ($element->getName()) {
          case 'day':
          case 'starthour':
          case 'endhour':
              $html = $this->_elementTemplates['inlinefirst'];
            break;
          case 'month':
              $html = $this->_elementTemplates['inline'];
            break;
          case 'startminute':
          case 'endminute':
          case 'year':
              $html = $this->_elementTemplates['inlinelast'];
            break;
          default:
            if (($this->_inGroup) and !empty($this->_groupElementTemplate)) {
                // so it gets substitutions for *each* element
                $html = $this->_groupElementTemplate;
            } elseif (method_exists($element, 'getElementTemplateType')){
                $html = $this->_elementTemplates[$element->getElementTemplateType()];
            }else{
                $html = $this->_elementTemplates['default'];
            }
        }

        if ($this->_showAdvanced){
            $advclass = ' advanced';
        } else {
            $advclass = ' advanced hide';
        }
        if (isset($this->_advancedElements[$element->getName()])){
            $html =str_replace(' {advanced}', $advclass, $html);
        } else {
            $html =str_replace(' {advanced}', '', $html);
        }
        if (isset($this->_advancedElements[$element->getName()])||$element->getName() == 'mform_showadvanced'){
            $html =str_replace('{advancedimg}', $this->_advancedHTML, $html);
        } else {
            $html =str_replace('{advancedimg}', '', $html);
        }
        $html =str_replace('{id}', 'fitem_' . $element->getAttribute('id'), $html);
        $html =str_replace('{type}', 'f'.$element->getType(), $html);
        $html =str_replace('{name}', $element->getName(), $html);
        if (method_exists($element, 'getHelpButton')){
            $html = str_replace('{help}', $element->getHelpButton(), $html);
        }else{
            $html = str_replace('{help}', '', $html);

        }
        if (($this->_inGroup) and !empty($this->_groupElementTemplate)) {
            $this->_groupElementTemplate = $html;
        }
        elseif (!isset($this->_templates[$element->getName()])) {
            $this->_templates[$element->getName()] = $html;
        }

        parent::renderElement($element, $required, $error);
    }
    
    function __construct () {
      parent::__construct();
      $this->_elementTemplates['inline'] = "\n\t\t".'<div id="{id}" class="felement finline {type}<!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error">{error}</span><br /><!-- END error -->{element}</div>';
      $this->_elementTemplates['inlinelast'] = "\n\t\t".'<div id="{id}" class="felement finlinelast {type}<!-- BEGIN error --> error<!-- END error -->">{element}<!-- BEGIN error --><span class="error">{error}</span><!-- END error --></div>';
      $this->_elementTemplates['inlinefirst'] = "\n\t\t".'<div id="{id}" class="pulldownIE7"></div><div class="fitem finlinefirst {advanced}<!-- BEGIN required --> required<!-- END required -->"><div class="fitemtitle"><label>{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg} {help}</label></div><div class="felement {type}<!-- BEGIN error --> error<!-- END error -->"><!-- BEGIN error --><span class="error">{error}</span><br /><!-- END error -->{element}</div></div>';
      parent::HTML_QuickForm_Renderer_Tableless();
    }
}
