<?php

namespace quamsta\ApiCacher;

use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Injector\Injector;

class FeedHelper {

    public static function getJson($url){
        $safeUrl = FeedHelper::safe_filename($url);
        $cache = Injector::inst()->get(CacheInterface::class . '.apiCacher');

        if (!$cache->has($safeUrl)) {
            $json = FeedHelper::fetchJson($url);

            $cache->set($safeUrl, $json, 300);
        }else{
            //always fetch json for now
            $json = $cache->get($safeUrl);
             // $json = FeedHelper::fetchJson($url);
        }
        if($json){
            $jsonDecoded = json_decode($json, TRUE);
            return $jsonDecoded;
        }
    }

    public static function safe_filename($filename){
        return preg_replace('/[^0-9a-z\.\_\-]/i','', strtolower($filename));
    }

    public static function fetchJson($url){
        if(function_exists("curl_init")){

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $content = curl_exec($ch);

            curl_close($ch);
            return $content;
        } else {
           return file_get_contents($url);
        }
    }
}
