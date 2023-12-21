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

namespace EA\Engine\Notifications;

use DateTime;
use DateTimeZone;
use EA\Engine\Types\Email as EmailAddress;
use EA\Engine\Types\NonEmptyText;
use EA\Engine\Types\CellPhone;
use EA\Engine\Types\Text;
use EA\Engine\Types\Url;
use EA\Engine\Types\Hash;
use EA_Controller;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

/**
 * Email Notifications Class
 *
 * This library handles all the notification email deliveries on the system.
 *
 * Important: The email configuration settings are located at: /application/config/email.php
 *
 * @deprecated
 */
class WhatsApp {
    /**
     * Framework Instance
     *
     * @var EA_Controller
     */
    protected $CI;
    /**
     * Class Constructor
     *
     * @param \CI_Controller $CI
     * @param array $config Contains the email configuration to be used.
     */
    public function __construct(\CI_Controller $CI)
    {
        $this->CI = $CI;
    }


    /**
     * Send an whatsapp with the appointment details.
     *
     * This email template also needs an email title and an email text in order to complete
     * the appointment details.
     *
     * @param array $appointment Contains the appointment data.
     * @param array $provider Contains the provider data.
     * @param array $service Contains the service data.
     * @param array $customer Contains the customer data.
     * @param array $settings Contains settings of the company. At the time the "company_name", "company_link" and
     * "company_email" values are required in the array.
     * @param \EA\Engine\Types\Text $title The email title may vary depending the receiver.
     * @param \EA\Engine\Types\Text $message The email message may vary depending the receiver.
     * @param \EA\Engine\Types\Hash $appointment_link_address This link is going to enable the receiver to make changes
     * to the appointment record.
     * @param \EA\Engine\Types\CellPhone $recipient_cellphone The recipient cellphone.
     * @param \EA\Engine\Types\Text $ics_stream Stream contents of the ICS file.
     * @param string|null $timezone Custom timezone for the notification.
     *
     *
     */
    
    public function send_appointment_details(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        Text $title,
        Text $message,
        Hash $appointment_hash,
        CellPhone $recipient_cellphone,
        Text $ics_stream,
        $timezone = NULL
    )
    {
        $timezones = $this->CI->timezones->to_array();

        switch ($settings['date_format'])
        {
            case 'DMY':
                $date_format = 'd/m/Y';
                break;
            case 'MDY':
                $date_format = 'm/d/Y';
                break;
            case 'YMD':
                $date_format = 'Y/m/d';
                break;
            default:
                throw new Exception('Invalid date_format value: ' . $settings['date_format']);
        }

        switch ($settings['time_format'])
        {
            case 'military':
                $time_format = 'H:i';
                break;
            case 'regular':
                $time_format = 'g:i a';
                break;
            default:
                throw new Exception('Invalid time_format value: ' . $settings['time_format']);
        }

        $appointment_timezone = new DateTimeZone($provider['timezone']);
        $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);
        $appointment_end = new DateTime($appointment['end_datetime'], $appointment_timezone);

        if ($timezone && $timezone !== $provider['timezone'])
        {
            $appointment_timezone = new DateTimeZone($timezone);
            $appointment_start->setTimezone($appointment_timezone);
            $appointment_end->setTimezone($appointment_timezone);
        }
        
        $provider_full_name =  $provider['first_name'] . ' ' . $provider['last_name'];
        $appointment_date = $appointment_start->format($date_format . ' ' . $time_format);
        #$appointment_link = $appointment_link_address->get();
        $company_link = $settings['company_link'];
        $appointment_hash = $appointment_hash->get();
        #log_message('debug', 'Link para enviar: ' . $appointment_hash);
        // URL de la API de Facebook
        $whatsapp_graph_url = sprintf($settings['whatsapp_url_messages'], $settings['whatsapp_phone_number_id']);
        // Token de acceso de WhatsApp
        $access_token = $settings['whatsapp_access_token'];
        // Número de teléfono de destino
        $to = $recipient_cellphone->get();
        $template_name = $settings['whatsapp_template_confirmation'];
        // Configurar el contenido del mensaje
        $message_data = [
            'messaging_product' => 'whatsapp',
            'to' => '52' . $to,
            'type' => 'template',
            'template' => [
                'name' => $template_name,
                'language' => ['code' => 'es_MX'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $appointment_date]
                        ]
                    ],
                    [
                        'type' => 'button',
                        'index' => '0',
                        'sub_type' => 'url',
                        'parameters' => [
                            ['type' => 'text', 'text' => $company_link]
                        ]
                    ]
                ]
            ]
        ];
        // Configurar las cabeceras de la solicitud
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: Bearer $access_token\r\n" .
                            "Content-Type: application/json\r\n",
                'content' => json_encode($message_data),
                'ignore_errors' => true,
            ],
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($whatsapp_graph_url, false, $context);
        $http_status = http_response_code();
        $json_response = json_decode($response, true);
        if (isset($json_response['error']))
        {
            log_message('error', 'Api whatsapp: ' . $json_response['error']['message']);
            throw new RuntimeException('Api Facebook could not been connected. (Line ' . __LINE__ . '): ');
        }
    }
    /**
     * Send an whatsapp notification to both provider and customer on appointment removal.
     *
     * Whenever an appointment is cancelled or removed, both the provider and customer
     * need to be informed. This method sends the same email twice.
     *
     * <strong>IMPORTANT!</strong> This method's arguments should be taken
     * from database before the appointment record is deleted.
     *
     * @param array $appointment The record data of the removed appointment.
     * @param array $provider The record data of the appointment provider.
     * @param array $service The record data of the appointment service.
     * @param array $customer The record data of the appointment customer.
     * @param array $settings Some settings that are required for this function. As of now this array must contain
     * the following values: "company_link", "company_name", "company_email".
     * @param \EA\Engine\Types\CellPhone $recipient_cellphone The recipient cellphone.
     * @param \EA\Engine\Types\Text $reason The reason why the appointment is deleted.
     * @param string|null $timezone Custom timezone.
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send_delete_appointment(
        array $appointment,
        array $provider,
        array $service,
        array $customer,
        array $settings,
        CellPhone $recipient_cellphone,
        Text $reason,
        $timezone = NULL
    )
    {
        $timezones = $this->CI->timezones->to_array();

        switch ($settings['date_format'])
        {
            case 'DMY':
                $date_format = 'd/m/Y';
                break;
            case 'MDY':
                $date_format = 'm/d/Y';
                break;
            case 'YMD':
                $date_format = 'Y/m/d';
                break;
            default:
                throw new Exception('Invalid date_format value: ' . $settings['date_format']);
        }

        switch ($settings['time_format'])
        {
            case 'military':
                $time_format = 'H:i';
                break;
            case 'regular':
                $time_format = 'g:i a';
                break;
            default:
                throw new Exception('Invalid time_format value: ' . $settings['time_format']);
        }

        $appointment_timezone = new DateTimeZone($provider['timezone']);
        $appointment_start = new DateTime($appointment['start_datetime'], $appointment_timezone);

        if ($timezone && $timezone !== $provider['timezone'])
        {
            $appointment_timezone = new DateTimeZone($timezone);
            $appointment_start->setTimezone($appointment_timezone);
        }

        $to = $recipient_cellphone->get();
        
        // URL de la API de Facebook
        $whatsapp_graph_url = sprintf($settings['whatsapp_url_messages'], $settings['whatsapp_phone_number_id']);
        // Token de acceso de WhatsApp
        $access_token = $settings['whatsapp_access_token'];
        //template que se usará para la cancelaci+on
        $template_name = $settings['whatsapp_template_cancelation'];
        // Configurar el contenido del mensaje
        $message_data = [
            'messaging_product' => 'whatsapp',
            'to' => '52' . $to,
            'type' => 'template',
            'template' => [
                'name' => $template_name,
                'language' => ['code' => 'es_MX'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $reason->get()]
                        ]
                    ],
                ]
            ]
        ];
        // Configurar las cabeceras de la solicitud
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: Bearer $access_token\r\n" .
                            "Content-Type: application/json\r\n",
                'content' => json_encode($message_data),
                'ignore_errors' => true,
            ],
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($whatsapp_graph_url, false, $context);
        $http_status = http_response_code();
        $json_response = json_decode($response, true);
        if (isset($json_response['error']))
        {
            log_message('error', 'Api whatsapp: ' . $json_response['error']['message']);
            throw new RuntimeException('Api Facebook could not been connected. (Line ' . __LINE__ . '): ');
        }

        
    }
}
