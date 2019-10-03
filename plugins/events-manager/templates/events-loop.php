<?php
    $events = EM_Events::get($args);
    $months = array(
      '01' => 'Jan',
      '02' => 'Feb',
      '03' => 'Mar',
      '04' => 'Apr',
      '05' => 'May',
      '06' => 'Jun',
      '07' => 'Jul',
      '08' => 'Aug',
      '09' => 'Sep',
      '10' => 'Oct',
      '11' => 'Nov',
      '12' => 'Dec',
    );
    $args['scope'] = 'past';
    foreach ($events as $event) {
      $site_url = get_site_url();
      $url = $site_url.'/'.$event->slug;
      var_dump($url);
      include(locate_template('template-parts/event-cards.php', false, false));
    }