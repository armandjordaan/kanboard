<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Task helpers
 *
 * @package helper
 * @author  Armand Jordaan
 */
class SubTaskTimeTrackingHelper extends Base
{
    public function selectStartDate(array $values, array $errors = array(), array $attributes = array())
    {
        $placeholder = date($this->configModel->get('application_date_format', 'm/d/Y H:i'));
        $attributes = array_merge(array('tabindex="12"', 'placeholder="'.$placeholder.'"'), $attributes);

        $html = $this->helper->form->label(t('Start Date'), 'start');
        $html .= $this->helper->form->text('start', $values, $errors, $attributes, 'form-datetime');

        return $html;
    }

    public function selectEndDate(array $values, array $errors = array(), array $attributes = array())
    {
        $placeholder = date($this->configModel->get('application_date_format', 'm/d/Y H:i'));
        $attributes = array_merge(array('tabindex="13"', 'placeholder="'.$placeholder.'"'), $attributes);

        $html = $this->helper->form->label(t('End Date'), 'end');
        $html .= $this->helper->form->text('end', $values, $errors, $attributes, 'form-datetime');

        return $html;
    }    
}
