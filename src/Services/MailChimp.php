<?php
    // TODO: Need to work on this a lot;

    namespace LoSys;

    use \DrewM\MailChimp\MailChimp as MC;

    final class MailChimp
    {
      private $mc;
      private $email;
      private $hash;
      private $list_id;

      function __construct () {
        $this->mc = new MC(getenv('MC_API'));
        $this->list_id = getenv('MC_LISTID');
        $this->ajax_setup();
      }

      private function ajax_setup () {
        add_action('wp_ajax_process:mailchimp', [$this, 'ajax_process']);
        add_action('wp_ajax_nopriv_process:mailchimp', [$this, 'ajax_process']);
      }

      public function ajax_process () {
        $this->email = $_POST['data']['EMAIL'];
        $exists = $this->checkUserExists();

        if ($exists) {
          $result = $this->update();
        }

        if (!$exists) {
          $result = $this->create();
        }

        if ($this->mc->success()) {
          $message = 'Thank you!';
        }

        wp_send_json([
        #'status' => $this->mc->success(),
        'error' => !$this->mc->success(),
        #'result' => $result,
        'message' => $message,
        #'hash' => $this->hash,
        #'exists' => $exists,
        #'lastRequest' => $this->mc->getLastRequest(),
        ]);
      }

      private function checkUserExists () {
        $this->hash = $this->mc->subscriberHash($this->email);
        $this->mc->get("lists/$this->list_id/members/$this->hash");
        return $this->mc->success();
      }

      private function update () {
        return $this->mc->patch("lists/$this->list_id/members/$this->hash");
      }

      private function create () {
        return $this->mc->post("lists/$this->list_id/members", [
          'email_address' => $this->email,
          'status'        => 'subscribed',
        ]);
      }
    }
