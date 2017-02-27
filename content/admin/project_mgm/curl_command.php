<?php


function curl_command($post_url, $post_count, $fields)
{
   // echo $post_url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
//curl_setopt($ch, CURLOPT_HEADER, true);
    if ($fields != '')
    {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    }

    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//if (isset($_POST['lv_type']) && $_POST['lv_type'] == 'Login')
//    curl_setopt($ch, CURLOPT_COOKIEJAR, $tmp_fname);
    //curl_setopt($ch, CURLOPT_COOKIEFILE, 'memberauth');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    //curl_setopt($ch, CURLOPT_REFERER, "http://lasvegasrealtor.com");
    //$lv_html = curl_exec($ch);
    ob_start();
    curl_exec($ch);
    $lv_html = ob_get_clean();
    curl_close($ch);
	unset($ch);
    $lv_html = trim($lv_html);
    return $lv_html;
}




?>