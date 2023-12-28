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
class Migration_Add_whatsapp_setting extends CI_Migration {
    /**
     * Upgrade method.
     */
    public function up()
    {
        $this->db->insert('settings', [
            'name' => 'whatsapp_is_active',
            'value' => '0'
        ]);

        $this->db->insert('settings', [
            'name' => 'whatsapp_template_confirmation',
            'value' => 'cita_confirmation'
        ]);
        
        $this->db->insert('settings', [
            'name' => 'whatsapp_template_cancelation',
            'value' => 'cita_cancelation'
        ]);
        
        $this->db->insert('settings', [
            'name' => 'whatsapp_url_messages',
            'value' => 'https://graph.facebook.com/v17.0/%s/messages'
        ]);

    }

    /**
     * Downgrade method.
     */
    public function down()
    {
        $this->db->delete('settings', ['name' => 'whatsapp_is_active']);
        $this->db->delete('settings', ['name' => 'whatsapp_template_confirmation']);
        $this->db->delete('settings', ['name' => 'whatsapp_template_cancelation']);
        $this->db->delete('settings', ['name' => 'whatsapp_url_messages']);
    }
}
