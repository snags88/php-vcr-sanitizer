<?php

namespace allejo\VCR;

use VCR\Request;

class RelaxedRequestMatcher
{
    private static $options = [];

    public static function configureOptions(array $options)
    {
        self::$options = array_merge_recursive(array(
            'ignoreUrlParameters' => array(),
            'ignoreHeaders' => array(),
        ), $options);
    }

    public static function getConfigurationOptions()
    {
        return self::$options;
    }

    public static function matchQueryString(Request $first, Request $second)
    {
        $firstUrl = parse_url($first->getUrl());
        $secondUrl = parse_url($second->getUrl());

        $domainEqual = ($firstUrl['scheme'] == $secondUrl['scheme']) && ($firstUrl['host'] && $secondUrl['host']);

        if (!$domainEqual) {
            return false;
        }

        $firstQuery = [];
        $secondQuery = [];

        parse_str($firstUrl['query'], $firstQuery);
        parse_str($secondUrl['query'], $secondQuery);

        foreach (self::$options['ignoreUrlParameters'] as $parameter) {
            unset($firstQuery[$parameter]);
            unset($secondQuery[$parameter]);
        }

        return $firstQuery === $secondQuery;
    }

    public static function matchHeaders(Request $first, Request $second)
    {
        $firstHeaders = $first->getHeaders();
        $secondHeaders = $second->getHeaders();

        foreach (self::$options['ignoreHeaders'] as $parameter) {
            unset($firstHeaders[$parameter]);
            unset($secondHeaders[$parameter]);
        }

        return $firstHeaders === $secondHeaders;
    }
}