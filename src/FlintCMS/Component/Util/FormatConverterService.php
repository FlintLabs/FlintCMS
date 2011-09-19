<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 29/08/11
 */

namespace FlintCMS\Component\Util;

/**
 * Handles converting data between formats
 * @author camm (camm@flintinteractive.com.au)
 */
class FormatConverterService implements FormatConverterServiceInterface
{
    /**
     * Converts an XML string into an array
     * @param $xmlString string
     * @return void
     */
    public function getArrayFromXML($xmlString)
    {
        return (array)new \SimpleXMLElement($xmlString);
    }

    /**
     * Strips non-ascii characters from string
     * @param $string
     * @return void
     */
    public function getAsciiFromStr($string)
    {
        // TODO: Implement getAsciiFromStr() method.
    }

    /**
     * Gets XML entities for a string
     * @param $stringUTF8
     * @param string $style
     * @return void
     */
    public function getXMLEntities($stringUTF8, $style = '#')
    {
        // TODO: Implement getXMLEntities() method.
    }

    /**
     * Converts a supplied string into camel case format
     * @param $string
     * @param bool $ucFirst
     * @return void
     */
    public function getCamelCase($string, $ucFirst = false)
    {
        // Deal with separation
        $string = strtolower(preg_replace('/[A-Z]+/', '_$0', $string));

        // Deal with spaces etc
        $string = preg_replace('/[\s_\-]+/', '-', $string);
        if (substr($string, 0, 1) == '-') $string = substr($string, 1);
        $string = trim($string);

        // Make parts
        $made = '';
        $parts = preg_split('/-/', (string)$string);
        $first = true;
        foreach ($parts as $part) {
            if ($first == true) {
                $made .= ($ucFirst == true) ? ucfirst($part) : $part;
                $first = false;
            } else {
                $made .= ucfirst($part);
            }
        }
        return $made;
    }

    /**
     * Get the proton naming for a string
     * @param $string
     * @return void
     */
    public function getProton($string)
    {
        // Deal first with separation
        $string = strtolower(preg_replace('/[A-Z]+/', '-$0', $string));

        // Replace spacing
        $string = preg_replace('/[\s_\-]+/', '_', $string);

        // Keep
        if (substr($string, 0, 1) == '_') $string = substr($string, 1);
        return trim($string);
    }

    /**
     * Get a supplied string as hyphen separated
     * @param $string
     * @return void
     */
    public function getHyphenSeparated($string)
    {
        // Deal with camel case
        $string = strtolower(preg_replace('/[A-Z]+/', '-$0', $string));

        // Deal with proton
        $string = preg_replace('/[\s_\-]+/', '-', $string);

        // Keep clean
        if (substr($string, 0, 1) == '-') $string = substr($string, 1);
        return trim($string);
    }
}
