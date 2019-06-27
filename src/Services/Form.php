<?php

    namespace LoSys\Services;

    final class Form
    {
      private $to;

      function __construct ()
      {
        $this->to = getenv('EMAIL_FORM');
        $this->ajax_setup();
      }

      private function ajax_setup ()
      {
        add_action('wp_ajax_process:form', [$this, 'ajax_process']);
        add_action('wp_ajax_nopriv_process:form', [$this, 'ajax_process']);
      }

      public function ajax_process ()
      {
        $data = $_POST['data'];
        wp_send_json($this->process($data));
      }

      private function process ($data)
      {
        $subject = 'From ' . get_bloginfo('name');

        $headers = "From: Contact Form <" . strip_tags($data['email']) . ">\r\n";
        $headers .= "Reply-To: <". strip_tags($data['email']) . ">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = '';

        foreach ($data as $key => $value) {
          $message .= str_replace('_', ' ', $key).': '.$value.'</br>';
        }

        if (wp_mail($this->to, $subject, $message, $headers)) {
          return ['error' => false, 'message' => 'Message sent - thank you!'];
        }

        return ['error' => true, 'message' => 'The form was unable to process. Please fix any errors and try again.','errors' => $errors];
      }
    }
