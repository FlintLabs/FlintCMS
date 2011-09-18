<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 10/09/11
 */
namespace FlintLabs\Bundle\FlintCMSBundle\Service;
use FlintLabs\Bundle\FlintCMSBundle\Entity\Fragment,
FlintLabs\Bundle\FlintCMSBundle\Service\FormatConverterServiceInterface,
Symfony\Component\Templating\EngineInterface;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class LocationBasedTemplateMappingService implements TemplateMappingServiceInterface
{
    protected $templatePrefixes;
    protected $fragmentPrefixes;

    protected $templateFile;
    protected $templateConfig;
    protected $formatConverter;

    protected $suffix = '.html.twig';
    protected $regex;
    protected $replace;

    public function __construct($templateFile, FormatConverterServiceInterface $formatConverter)
    {
        $this->templateFile = $templateFile;
        $this->formatConverter = $formatConverter;
        
        // Setup a prioritisation path mechanism
        $this->templatePrefixes = array();
        $this->fragmentPrefixes = array();
        for ($x = -10; $x <= 0; $x++) {
            $this->templatePrefixes[$x] = array();
            $this->fragmentPrefixes[$x] = array();
        }

        // Set the default priorities
        $this->templatePrefixes[-10][] = ':Templates:';
        $this->fragmentPrefixes[-10][] = ':Fragments:';
        $this->templatePrefixes[-10][] = '::';
        $this->fragmentPrefixes[-10][] = '::';

        // Attempt to find them in the base package
        $this->fragmentPrefixes[0][] = 'FlintLabsFlintCMSBundle:Fragments:';
        $this->templatePrefixes[0][] = 'FlintLabsFlintCMSBundle:Templates:';
    }

    /**
     * @throws Exception
     * @return void
     */
    protected function initTemplateConfig()
    {
        if (empty($this->templateConfig)) {
            try {
                $this->templateConfig = new \SimpleXMLElement(file_get_contents($this->templateFile));
            } catch (Exception $e) {
                throw new Exception('Could not parse the template file cofiguration source');
            }

            // Set the template path priorities based on information configured in the template file
            $prefixMatches = $this->templateConfig->xpath('//fallback-prefixes/prefix');
            if (!empty($prefixMatches)) {
                foreach ($prefixMatches as $prefixMatch) {
                    // Obtain the prefix
                    $prefix = $prefixMatch->xpath('@value');
                    if (!empty($prefix)) {
                        $prefix = (string)current($prefix);
                        
                        // Obtain the type
                        $type = $prefixMatch->xpath('@type');
                        if (!empty($type)) $type = (string)current($type);
                        else $type = null;

                        // Obtain a priority
                        $priority = $prefixMatch->xpath('@prefix');
                        if (!empty($priority)) $priority = (string)current($priority);
                        else $priority = -5;

                        // Assign as required
                        switch(strtolower($type)) {
                            case 'template':
                            case 'templates':
                                $this->addTemplatePrefix($prefix, $priority);
                                break;
                            case 'fragment':
                            case 'fragments':
                                $this->addFragmentPrefix($prefix, $priority);
                                break;
                            default:
                                $this->addTemplatePrefix($prefix, $priority);
                                $this->addFragmentPrefix($prefix, $priority);
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws Exception
     * @param $prefix
     * @param int $priority
     * @return void
     */
    public function addTemplatePrefix($prefix, $priority = -5) {
        if (!preg_match('/:$/', $prefix) || preg_match('/[:]{2}/', $prefix)) {
            throw new Exception('Please ensure template paths are in correct format, containing two ":" and finishing with a trailing :');
        }
        $priority = intval($priority);
        if ($priority < -10) $priority = -10;
        else if ($priority > 0) $priority = 0;
        $this->templatePrefixes[$priority][] = $prefix;
    }

    /**
     * @throws Exception
     * @param $path
     * @param $priority
     * @return void
     */
    public function addFragmentPrefix($prefix, $priority = -5) {
        if (!preg_match('/:$/', $prefix) || preg_match('/[:]{2}/', $prefix)) {
            throw new Exception('Please ensure template paths are in correct format, containing two ":" and finishing with a trailing :');
        }
        $priority = intval($priority);
        if ($priority < -10) $priority = -10;
        else if ($priority > 0) $priority = 0;
        $this->fragmentPrefixes[$priority][] = $prefix;
    }

    /**
     * Returns with the corresponding view for the fragment
     * @param Fragment $fragment
     * @return string
     */
    public function getTemplateForFragment(Fragment $fragment)
    {
        // Attempt to get a template match from the fragment data
        try {
            $templateDef = new \SimpleXMLElement($fragment->getData());
            $matches = $templateDef->xpath('/data/template');
        } catch (Exception $e) {
            throw new Exception('Could not parse the fragment data as XML');
        }

        // Parse the template config
        $this->initTemplateConfig();

        // Look for a match
        if (empty($matches)) {
            // Use the default corresponding to the type
            return $this->processFragment($fragment->getType());
        } else {
            $templateKey = (string)current($matches);

            // Lookup the template configuration
            $templateMatches = $this->templateConfig->xpath("//template[@key='" . $templateKey . "']/file");
            if (empty($templateMatches)) {
                throw new Exception('Could not locate the template by the key: ' . $templateKey);
            }
            return $this->processTemplate((string)current($templateMatches));
        }
    }

    /**
     * Look for the possible locations of this fragment, return with a match
     * @param $type
     * @return string
     */
    protected function processFragment($type)
    {
        $typeAsHyphenated = $this->formatConverter->getHyphenSeparated($type);
        $file = $typeAsHyphenated . $this->suffix;
        // Locate the possible locations for this file
        $templates = array();
        for ($x = -10; $x <= 0; $x++) {
            foreach ($this->fragmentPrefixes[$x] as $prefix) {
                $name = $prefix . $file;
                $templates[] = $name;
            }
        }
        return $templates;
    }

    /**
     * Look for the possible locations of this template, return with a match
     * @param $templateFile
     * @return string
     */
    protected function processTemplate($templateFile)
    {
        // Check if we need a suffix
        if (!preg_match('/\./', $templateFile)) $templateFile .= $this->suffix;

        // If we have presence of ':' we are assuming an exact location
        if (preg_match('/:/', $templateFile)) return $templateFile;

        // Locate the possible locations for this template
        $templates = array();
        for ($x = -10; $x <= 0; $x++) {
            foreach ($this->templatePrefixes[$x] as $prefix) {
                $name = $prefix . $templateFile;
                // Does this template exist?
                $templates[] = $name;
            }
        }
        return $templates;
    }

    /**
     * @param $regex
     * @param $replace
     * @return void
     */
    public function setRegex($regex, $replace)
    {
        $this->regex = $regex;
        $this->replace = $replace;
    }

    /**
     * @param $suffix
     * @return void
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }
}
