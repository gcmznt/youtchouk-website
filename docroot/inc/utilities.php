<?php

    $utils['latestVideos'] = function ($num = 10) {
        $videoList = array();
        $videoListQuery = mysql_query("SELECT * FROM video WHERE publish = 1 ORDER BY insert_date DESC LIMIT 0, $num");
        while ($currentVideo = mysql_fetch_assoc($videoListQuery)) {
            $videoList[] = $currentVideo;
        }
        return $videoList;
    };

    $utils['mostViewedVideos'] = function ($num = 10) {
        $videoList = array();
        $videoListQuery = mysql_query("SELECT * FROM video WHERE publish = 1 ORDER BY views DESC LIMIT 0, $num");
        while ($currentVideo = mysql_fetch_assoc($videoListQuery)) {
            $videoList[] = $currentVideo;
        }
        return $videoList;
    };

    $utils['getVideo'] = function ($code) {
        $code = mysql_real_escape_string($code);
        $videoDetail = mysql_query("SELECT * FROM video WHERE publish = 1 AND code = '$code'");
        if (mysql_num_rows($videoDetail) == 0) {
            return false;
        } else {
            return mysql_fetch_assoc($videoDetail);
        }
    };

    $utils['viewVideo'] = function ($code) {
        $code = mysql_real_escape_string($code);
        $updateViews = mysql_query("UPDATE video SET views = views + 1 WHERE code = '$code'");
        return mysql_affected_rows();
    };

    $utils['filterVideos'] = function ($key, $value, $num = 10) {
        $key = mysql_real_escape_string($key);
        $value = mysql_real_escape_string($value);
        $videoList = array();
        $videoListQuery = mysql_query("SELECT * FROM video WHERE publish = 1 AND $key = '$value' ORDER BY insert_date DESC LIMIT 0, $num");
        while ($currentVideo = mysql_fetch_assoc($videoListQuery)) {
            $currentVideo['description'] = nl2br($currentVideo['description']);
            $videoList[] = $currentVideo;
        }
        return $videoList;
    };

    $utils['searchVideos'] = function ($value, $num = 10) {
        $value = mysql_real_escape_string($value);
        $videoList = array();
        $videoListQuery = mysql_query("SELECT * FROM video WHERE publish = 1 AND name LIKE '%$value%' ORDER BY insert_date DESC LIMIT 0, $num");
        while ($currentVideo = mysql_fetch_assoc($videoListQuery)) {
            $currentVideo['description'] = nl2br($currentVideo['description']);
            $videoList[] = $currentVideo;
        }
        return $videoList;
    };

    $utils['validateEmail'] = function ($email) {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            }
            else if ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            }
            else if ($local[0] == '.' || $local[$localLen-1] == '.') {
                // local part starts or ends with '.'
                $isValid = false;
            }
            else if (preg_match('/\\.\\./', $local)) {
                // local part has two consecutive dots
                $isValid = false;
            }
            else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                // character not valid in domain part
                $isValid = false;
            }
            else if (preg_match('/\\.\\./', $domain)) {
                // domain part has two consecutive dots
                $isValid = false;
            }
            else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&` {}|~.-])+$/', str_replace("\\\\","",$local))) {
                // character not valid in local part unless 
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
                    $isValid = false;
                }
            }
            // if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
            //     // domain not found in DNS
            //     $isValid = false;
            // }
        }
        return $isValid;
    };


