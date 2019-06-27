<?php

    namespace LoSys\Services;

    final class Instagram
    {
      private $username;
      private $key;
      private $limit = 6;

      function __construct ($limit)
      {
        $this->username = getenv('INSTAGRAM_USER');
        $this->key = 'instagram.'.preg_replace('/[^A-Za-z0-9]/', "", strtolower($this->username));
        $this->limit = $limit;
      }

      private function fetch ()
      {
        $source = file_get_contents('http://instagram.com/' . $this->username);
    	  $shards = explode('window._sharedData = ', $source);
    	  $insta_json = explode(';</script>', $shards[1]);
    	  return json_decode($insta_json[0], TRUE);
      }

      private function cached ()
      {
        return get_transient($this->key);
      }

      private function cache ($data)
      {
        set_transient($this->key, $data, HOUR_IN_SECONDS*2 );
      }

      private function process ()
      {
        if (!$this->cached()) {
          $images = [];
          $data = $this->fetch();
          $edges = $data['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];

          for ($i=0; $i < $this->limit; $i++) {
            $images[] = $edges[$i]['node'];
          }

          $this->cache($images);
          return $images;
        }
      }

      public function images ()
      {
        $output = [];
        $data = $this->cached();

        if (!$data) {
          $data = $this->process();
        }

        foreach ($data as $image) {
          $output[] = [
            'image' => $image['thumbnail_resources'][4]['src'],
  	        'url' => 'https://www.instagram.com/p/'. $image['shortcode']
          ];
        }

        return $output;
      }
    }
