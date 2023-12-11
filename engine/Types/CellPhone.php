<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Types;

/**
 * Class Boolean
 *
 * @deprecated
 *
 * @package EA\Engine\Types
 */
class CellPhone extends Type {
    /**
     * @param mixed $value
     * @return bool
     */
    protected function validate($value)
    {
        #$patron = '/^\+\d{1,3} \(\d{3}\) \d{3}-\d{4}$|^\+\d{1,3}\d{10}$/';
        $cleanedValue = preg_replace('/[^0-9]/', '', $value);
        return (strlen($cleanedValue) === 10);
    }
}
