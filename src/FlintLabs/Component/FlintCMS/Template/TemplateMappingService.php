<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 29/08/11
 */
namespace FlintLabs\Bundle\FlintCMSBundle\Service;
use FlintLabs\Component\FlintCMS\Entity\Fragment,
FlintLabs\Bundle\FlintCMSBundle\Service\FormatConverterServiceInterface;
/**
 * Handles mapping fragments to templates
 * @author camm (camm@flintinteractive.com.au)
 */
class TemplateMappingService implements TemplateMappingServiceInterface
{
    protected $templateFile;
    protected $templateConfig;
    protected $templateRegex;
    protected $templateRegexReplace;

    protected $templatePrefix = '::';
    protected $templateSuffix = '.html.twig';

    protected $formatConverter;

    /**
     * @param $templateFile Reference to the template lookup file
     * @param FormatConverterServiceInterface $formatConverter
     */
    public function __construct($templateFile, FormatConverterServiceInterface $formatConverter)
    {
        $this->templateFile = $templateFile;
        $this->formatConverter = $formatConverter;
    }

    /**
     * Sets the template prefix for returned templates
     * Default: '::'
     * @return void
     */
    public function setTemplatePrefix($prefix)
    {
        // Place in the trailing separator if missing
        if (substr($prefix, -1) != ':') {
            $prefix = $prefix . ':';
        }
        $this->templatePrefix = $prefix;
    }

    /**
     * Sets the template suffix for returned templates
     * Default: '.html.twig'
     * @return void
     */
    public function setTemplateSuffix($suffix)
    {
        $this->templateSuffix = $suffix;
    }

    /**
     * Sets a transform regular expression for applying to template keys
     * @return void
     */
    public function setTemplateTransformRegex($regex, $replace)
    {
        $this->templateRegex = $regex;
        $this->templateRegexReplace = $replace;
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

        }
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

        // Look for a match
        if (empty($matches)) {
            // Use the default corresponding to the type
            $typeAsHyphenated = $this->formatConverter->getHyphenSeparated($fragment->getType());
            return $this->processTemplate($typeAsHyphenated);
        } else {
            $this->initTemplateConfig();
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
     * @param $template
     * @return mixed|string
     */
    protected function processTemplate($template)
    {
        // Implement a regex match
        if (!empty($this->templateRegex) && !empty($this->templateRegexReplace)) {
            $template = preg_replace($this->templateRegex, $this->templateRegexReplace, $template);
        }

        if (!preg_match('/([:]+)/', $template)) {
            // We are assuming to be missing the template prefix
            $template = $this->templatePrefix . $template;
        }
        if (!preg_match('/:[^\.]+\./', $template)) {
            // We are assuming to be missing teh template suffix
            $template = $template . $this->templateSuffix;
        }

        return $template;
    }
}
