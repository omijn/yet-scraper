<?php

  $links_file = fopen("institute_links.txt", "a") or die("Unable to create/open links file.");
  $progress_file_r = fopen("links_page_num.txt", "r") or die("Unable to read saved progress.");
  $current_page_count = intval(fgets($progress_file_r)) + 1;

  for ($pagecount = $current_page_count; $pagecount <= 142; $pagecount++) {
    //random delay between 10 seconds and 120 seconds
    $delay = mt_rand(10, 120);
    echo "\033[93mWaiting for $delay seconds.\033[39m\n";
    for ($i = 1; $i <= $delay; $i++) {
      echo "$i\r";
      usleep(1000000);
    }

    echo "\033[96mFetching links from page $pagecount...\033[39m\n";
    $url = "http://www.yet5.com/training/18/".$pagecount."/training-institutes-in-bangalore.html";
    $webpage = file_get_contents($url);

    preg_match_all(
      '/<a.*href=\"([\S]*)\".*class=\"institute_list\".*a>/',
      $webpage,
      $institute_links
    );

    foreach($institute_links[1] as &$link) {
      $link = "http://www.yet5.com".$link."\n";
      fwrite($links_file, $link);
    }

    echo "\033[92mFetched ".count($institute_links[1])." links!\033[39m\n\n";

    $progress_file_w = fopen("links_page_num.txt", "w") or die("Unable to write save progress.");
    fwrite($progress_file_w, $pagecount);
    fclose($progress_file_w);

    unset($link);  //http://php.net/manual/en/control-structures.foreach.php

  }

  fclose($links_file);
  fclose($progress_file_r);

?>
