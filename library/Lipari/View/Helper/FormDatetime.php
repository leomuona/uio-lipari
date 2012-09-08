<?php
/**
* View helper to render Date element
*
* based on code from
* http://akrabat.com/zend-framework/a-zend-framwork-compount-form-element-for-dates/
*/

class Lipari_View_Helper_FormDatetime extends Zend_View_Helper_FormElement
{
    public function formDatetime ($name, $value = null, $attribs = null)
    {
        // separate value into day, month and year
        $day = '';
        $month = '';
        $year = '';
        $hours = '';
        $minutes = '';
        if (is_array($value)) {
            $day = $value['day'];
            $month = $value['month'];
            $year = $value['year'];
            $hours = $value['hours'];
            $minutes = $value['minutes'];
        } elseif (strtotime($value)) {
            list($year, $month, $day, $hours, $minutes) = explode('-',
                    date('Y-m-d-H-i', strtotime($value)));
        }

        // build select options
        $dayAttribs = isset($attribs['dayAttribs']) ? $attribs['dayAttribs'] : array();
        $monthAttribs = isset($attribs['monthAttribs']) ? $attribs['monthAttribs'] : array();
        $yearAttribs = isset($attribs['yearAttribs']) ? $attribs['yearAttribs'] : array();
        $hoursAttribs = isset($attribs['hoursAttribs']) ? $attribs['hoursAttribs'] : array();
        $minutesAttribs = isset($attribs['minutesAttribs']) ? $attribs['minutesAttribs'] : array();

        $dayMultiOptions = array('' => '');
        for ($i = 1; $i < 32; $i ++)
        {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $dayMultiOptions[$index] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        $monthMultiOptions = array('' => '');
        for ($i = 1; $i < 13; $i ++)
        {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthMultiOptions[$index] = date('F', mktime(null, null, null, $i, 01));
        }

        $startYear = date('Y');
        if (isset($attribs['startYear'])) {
            $startYear = $attribs['startYear'];
            unset($attribs['startYear']);
        }

        $stopYear = $startYear + 10;
        if (isset($attribs['stopYear'])) {
            $stopYear = $attribs['stopYear'];
            unset($attribs['stopYear']);
        }

        $yearMultiOptions = array('' => '');

        if ($stopYear < $startYear) {
            for ($i = $startYear; $i >= $stopYear; $i--) {
                $yearMultiOptions[$i] = $i;
            }
        } else {
            for ($i = $startYear; $i <= $stopYear; $i++) {
                $yearMultiOptions[$i] = $i;
            }
        }

        $hoursMultiOptions = array('' => '');
        for ($i = 0; $i < 24; $i ++)
        {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $hoursMultiOptions[$index] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        $minutesMultiOptions = array('' => '');
        for ($i = 0; $i < 60; $i ++)
        {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $minutesMultiOptions[$index] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        // return the 5 selects separated by &nbsp;
        return
            $this->view->formSelect(
                $name . '[day]',
                $day,
                $dayAttribs,
                $dayMultiOptions) . '&nbsp;' .
            $this->view->formSelect(
                $name . '[month]',
                $month,
                $monthAttribs,
                $monthMultiOptions) . '&nbsp;' .
            $this->view->formSelect(
                $name . '[year]',
                $year,
                $yearAttribs,
                $yearMultiOptions) . '&nbsp;' .
            $this->view->formSelect(
                $name . '[hours]',
                $hours,
                $hoursAttribs,
                $hoursMultiOptions) . '&nbsp;' .
            $this->view->formSelect(
                $name . '[minutes]',
                $minutes,
                $minutesAttribs,
                $minutesMultiOptions
            );
    }
}
