<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.3.2
 * ---------------------------------------------------------------------------- */

/**
 * Class Migration_Create_consents_table
 *
 * @property CI_DB_query_builder $db
 * @property CI_DB_forge $dbforge
 */
class Migration_Add_extra_company_settings extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $this->db->insert('settings', [
            'name' => 'company_logo',
            'value' => ''
        ]);

        $this->db->insert('settings', [
            'name' => 'company_css',
            'value' => '#book-appointment-wizard #header{background: #041c31 !important;} #book-appointment-wizard .active-step{ background: #ffffff !important; } #book-appointment-wizard .book-step{ background: #ffffff36; } #book-appointment-wizard .book-step strong { color: #000 !important; } body .ui-datepicker .ui-widget-header{ background: #041c31; } body .ui-widget.ui-widget-content { border: 1px solid #041c31; } body .ui-datepicker th { background: #041c31 } #book-appointment-wizard #available-hours .selected-hour { background-color: #041c31; border-color: #041c31; } html body .ui-datepicker td a.ui-state-active { background: #041c31 !important; } body .ui-datepicker td a, body .ui-datepicker td span { color: #041c31 !important; } #header{ background-color: #041c31 !important; } #header #header-menu .nav-item.active { background: #052642 !important; } #header #header-menu .nav-item:hover { background: #052642 !important; } #calendar-page #calendar-toolbar { background: #e3e3e3e5 !important; }'
        ]);
    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->db->delete('settings', ['name' => 'company_logo']);
        $this->db->delete('settings', ['name' => 'company_css']);
    }
}
