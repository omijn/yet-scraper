<?php

  include("db_conn.php");

  $links_file = fopen("institute_links.txt", "r") or die("Unable to open links file.");
  $completed_links_file = fopen("completed_links.txt", "a") or die("Unable to open completed links file.");

  while(!feof($links_file)) {
    //random delay between 20 seconds and 180 seconds
    $delay = mt_rand(20, 180);
    // $delay = 5;
    echo "\033[93mWaiting for $delay seconds.\033[39m\n";
    for ($i = 1; $i <= $delay; $i++) {
      echo "$i\r";
      usleep(1000000);
    }

    echo "\033[96mFetching data from webpage...\033[39m\n";
    $url = fgets($links_file);
    $trimmed_url = trim($url);
    $institute_raw_details_html = file_get_contents($trimmed_url);

    //scrape institute name
    preg_match("/<meta property=\"og:title\" content=\"(.*)\".*>/", $institute_raw_details_html, $name);

    //scrape address details
    preg_match("/<meta property=\"og:street-address\" content=\"(.*)\".*>/", $institute_raw_details_html, $address);
    preg_match("/<meta property=\"og:locality\" content=\"(.*)\".*>/", $institute_raw_details_html, $locality);
    preg_match("/<meta property=\"og:city\" content=\"(.*)\".*>/", $institute_raw_details_html, $city);
    preg_match("/<meta property=\"og:postal-code\" content=\"(.*)\".*>/", $institute_raw_details_html, $postal_code);
    preg_match("/<meta property=\"og:landmark\" content=\"(.*)\".*>/", $institute_raw_details_html, $landmark);

    //scrape phone numbers
    preg_match("/Phone\D*([\d\+\-\(\)\s\/]{5,}).*<\/p>/", $institute_raw_details_html, $phone);
    preg_match("/Mobile\D*([\d\+\-\(\)\s\/]{5,}).*<\/p>/", $institute_raw_details_html, $mobile);

    //scrape website
    preg_match("/Website.*href=\"([^\"]*)\"/", $institute_raw_details_html, $website);

    //scrape courses
    preg_match_all("/leftlist.*skill_heading.*>([\w\ \-\&\+\.]+)</", $institute_raw_details_html, $courses);

    //scrape detailed courses
    preg_match_all("/leftlist_skill.*?textcon.*?>([\w\ \-\&\+\.]+)</m", $institute_raw_details_html, $detailed_courses);

    //remove location details from institute name
    $name[1] = preg_replace("/(?:.(?! in\b))+$/m", "", $name[1]);

    $name[1] = mysqli_real_escape_string($connection, $name[1]);
    $address[1] = mysqli_real_escape_string($connection, $address[1]);
    $locality[1] = mysqli_real_escape_string($connection, $locality[1]);
    $city[1] = mysqli_real_escape_string($connection, $city[1]);
    $postal_code[1] = mysqli_real_escape_string($connection, $postal_code[1]);
    $landmark[1] = mysqli_real_escape_string($connection, $landmark[1]);
    $phone[1] = mysqli_real_escape_string($connection, $phone[1]);
    $mobile[1] = mysqli_real_escape_string($connection, $mobile[1]);
    $website[1] = mysqli_real_escape_string($connection, $website[1]);
    $courses[1] = mysqli_real_escape_string($connection, implode(',', $courses[1]));
    $detailed_courses[1] = mysqli_real_escape_string($connection, implode(',', $detailed_courses[1]));

    $sqlquery = "INSERT INTO yet5(Name, Address, Locality, City, `Postal Code`, Landmark, Phone, Mobile, Website, Courses, `Detailed Courses`) VALUES('".$name[1]."', '".$address[1]."', '".$locality[1]."', '".$city[1]."', $postal_code[1], '".$landmark[1]."', '".$phone[1]."', '".$mobile[1]."',
    '".$website[1]."', '".$courses[1]."', '".$detailed_courses[1]."')";
    // echo $sqlquery."\n\n";

    mysqli_query($connection, $sqlquery) or die(mysqli_error($connection));

    echo "\033[92mData stored!\033[39m\n\n";
    fwrite($completed_links_file, $url);

    file_put_contents("institute_links.txt", str_replace($url, '', file_get_contents("institute_links.txt")));

    echo("\033[36mName:\033[0m ");print_r($name[1]);echo(", ");
    // echo("\033[36mAddress:\033[0m ");
    print_r($address[1]);echo(", ");
    // echo("\033[36mLocality:\033[0m ");
    print_r($locality[1]);echo(", ");
    // echo("\033[36mCity:\033[0m ");
    print_r($city[1]);echo(" - ");
    // echo("\033[36mPostal Code:\033[0m ");
    print_r($postal_code[1]);echo("");
    echo(" (");print_r($landmark[1]);echo(")\n");
    echo("\033[36mPhone number:\033[0m ");print_r($phone[1]);echo("\t");
    echo("\033[36mMobile number:\033[0m ");print_r($mobile[1]);echo("\t");
    echo("\033[36mWebsite:\033[0m ");print_r($website[1]);echo("\n\n");
    // echo("\033[36mCourses:\033[0m ");print_r($courses[1]);echo("\n\n");
    // echo("\033[36mDetailed Courses:\033[0m ");print_r($detailed_courses[1]);echo("\n\n");
  }

  fclose($links_file);
?>
