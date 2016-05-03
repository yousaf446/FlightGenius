<?php

class Spider {

    public function spider_call($header = array(), $referer = false, $url, $cookie = false,
                           $post = false) {
        if (!$cookie)
        {
            $cookie = "cookie.txt";
        }
        //$cookie_text = 'PHPSESSID=4k877pjmlf3fh7qi2k56h8ht81; __utmt=1; __utma=114404165.1485780952.1461914772.1461920419.1461924549.4; __utmb=114404165.8.10.1461924549; __utmc=114404165; __utmz=114404165.1461914772.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,sdch');
        if (isset($header) && !empty($header))
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 200);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.7) Gecko/20070914 Firefox/2.0.0.7");
        curl_setopt($ch, CURLOPT_COOKIEJAR, realpath($cookie));
        curl_setopt($ch, CURLOPT_COOKIEFILE, realpath($cookie));
        //curl_setopt($ch, CURLOPT_COOKIE, $cookie_text);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (isset($referer) && $referer != false)
        {
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        } else
        {
            curl_setopt($ch, CURLOPT_REFERER, $url);
        }
        //if have to post data on the server
        if (isset($post) && !empty($post) && $post)
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        } //endif
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return ($data);
    }
}