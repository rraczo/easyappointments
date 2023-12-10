<?php defined('BASEPATH') or exit('No direct script access allowed');

// Add custom values by settings them to the $config array.
// Example: $config['smtp_host'] = 'smtp.gmail.com';
// @link https://codeigniter.com/user_guide/libraries/email.html

////$config['useragent'] = 'Easy!Appointments';
////$config['protocol'] = 'mail'; // or 'smtp'
////$config['mailtype'] = 'html'; // or 'text'
// $config['smtp_debug'] = '0'; // or '1'
// $config['smtp_auth'] = TRUE; //or FALSE for anonymous relay.
// $config['smtp_host'] = '';
// $config['smtp_user'] = '';
// $config['smtp_pass'] = '';
// $config['smtp_crypto'] = 'ssl'; // or 'tls'
// $config['smtp_port'] = 25;

  $config['protocol'] = 'smtp';
  $config['smtp_host'] = 'sandbox.smtp.mailtrap.io';
  $config['smtp_port'] = 2525;
  $config['smtp_user'] = 'c1690feaefbe0f';
  $config['smtp_pass'] = '0b02851618d860';
  $config['crlf'] = "\r\n";
  $config['newline'] = "\r\n";
  $config['smtp_debug'] = '0';
  $config['smtp_auth'] = TRUE;
  $config['smtp_crypto'] = 'tls';
  $config['mailtype'] = 'html';