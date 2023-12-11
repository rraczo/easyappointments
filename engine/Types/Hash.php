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
class Hash extends Type {
    /**
     * @param mixed $value
     * @return bool
     */
    protected function validate($value)
    {
        // Verificar si el valor es una cadena
        if (!is_string($value)) {
            return false;
        }
        // Verificar si la longitud es exactamente 12 caracteres
        if (strlen($value) !== 12) {
            return false;
        }
        if (!ctype_alnum($value)) {
            return false;
        }
        return true;
    }
}
