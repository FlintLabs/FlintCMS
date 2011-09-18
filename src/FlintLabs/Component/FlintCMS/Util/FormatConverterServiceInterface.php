<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 29/08/11
 */

namespace FlintLabs\Component\FlintCMS\Util;

/**
 * Handles conversion of data between formats
 * @author camm (camm@flintinteractive.com.au)
 */
interface FormatConverterServiceInterface
{
    /**
     * Converts an XML string into an array
     * @abstract
     * @param $xmlString string
     * @return void
     */
    public function getArrayFromXML($xmlString);

    /**
     * Strips non-ascii characters from string
     * @abstract
     * @param $string
     * @return void
     */
    public function getAsciiFromStr($string);

    /**
     * Gets XML entities for a string
     * @abstract
     * @param $stringUTF8
     * @param string $style
     * @return void
     */
    public function getXMLEntities($stringUTF8, $style = '#');

    /**
     * Converts a supplied string into camel case format
     * @abstract
     * @param $string
     * @param bool $ucFirst
     * @return void
     */
    public function getCamelCase($string, $ucFirst = true);

    /**
     * Get the proton naming for a string
     * @abstract
     * @param $string
     * @return void
     */
    public function getProton($string);

    /**
     * Get a supplied string as hyphen separated
     * @abstract
     * @param $string
     * @return void
     */
    public function getHyphenSeparated($string);

}
