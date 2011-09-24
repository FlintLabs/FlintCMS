<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Bundle\FrontProfilerBundle\DataCollector;
use FlintCMS\Component\Dispatcher\DispatcherServiceInterface;
use FlintCMS\Component\Template\TemplateMappingServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use FlintCMS\Component\Util\FormatConverterServiceInterface;

/**
 * Hooks into the dispatch service to observe what node was dispatched
 * @TODO: Make this listener based!
 * @author camm (camm@flintinteractive.com.au)
 */
class DispatcherDataCollector extends \Symfony\Component\HttpKernel\DataCollector\DataCollector
{

    /**
     * @var \FlintCMS\Component\Dispatcher\DispatcherServiceInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $templateFile;

    /**
     * @var string
     */
    private $fragmentFile;

    /**
     * @var \SimpleXMLElement
     */
    private $fragmentModel;

    /**
     * @var \FlintLabs\Bundle\FlintCMSProfilerBundle\DataCollector\TemplateMappingServiceInterface
     */
    private $templateMapping;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $engine;

    /**
     * @var \FlintCMS\Component\Util\FormatConverterServiceInterface
     */
    private $formatConverter;

    /**
     * @param \FlintCMS\Component\Dispatcher\DispatcherServiceInterface $dispatcher
     */
    public function __construct(DispatcherServiceInterface $dispatcher, TemplateMappingServiceInterface $templateMapping, FormatConverterServiceInterface $formatConverter, EngineInterface $engine)
    {
        $this->dispatcher = $dispatcher;
        $this->templateMapping = $templateMapping;
        $this->formatConverter = $formatConverter;
        $this->engine = $engine;
    }


    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request   A Request instance
     * @param Response   $response  A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @api
     */
    function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $collections = array();
        if (method_exists($this->dispatcher, 'getNode')) {
            // Obtain the node that was dispatched
            $node = $this->dispatcher->getNode();
            if (!empty($node)) {
                /*
                 * NODE INFORMATION
                 */
                $nodeCollection = array();
                $nodeCollection['id'] = $node->getId();
                $nodeCollection['label'] = $node->getLabel();
                $collections['node'] = $nodeCollection;

                // Template
                $nodeFragment = $node->getFragment();
                if (!empty($nodeFragment)) {

                    /*
                     * NODE FRAGMENT INFORMATION
                     */
                    // Obtain the node attached fragment
                    $nodeAttachedFragmentCollection = array();
                    $nodeAttachedFragmentCollection['type'] = $nodeFragment->getType();
                    $nodeAttachedFragmentCollection['cmsType'] = $this->getFragmentCMSType($nodeFragment);
                    $nodeAttachedFragmentCollection['view'] = array_keys($nodeFragment->getViewData());
                    $nodeAttachedFragmentCollection['id'] = $nodeFragment->getId();
                    $nodeAttachedFragmentCollection['viewDataStructure'] = $this->getFragmentDataStructure($nodeFragment);

                    $templateLocation = $this->templateMapping->getTemplateForFragment($nodeFragment);
                    $nodeAttachedFragmentCollection['searchedPaths'] = is_array($templateLocation)
                            ? implode(', ', $templateLocation) : $templateLocation;
                    if (is_array($templateLocation) && !empty($this->engine)) {
                        foreach ($templateLocation as $templateLocationFile) {
                            if ($this->engine->exists($templateLocationFile)) {
                                $nodeAttachedFragmentCollection['used'] = $templateLocationFile;
                                break;
                            }
                        }
                    }


                    /*
                     * TEMPLATE INFORMATION
                     */
                    $fragmentDataXML = new \SimpleXMLElement($nodeFragment->getData());
                    $templateMatches = $fragmentDataXML->xpath('//template');
                    if (!empty($templateMatches)) {
                        $template = array();
                        $template['key'] = (string)current($templateMatches);

                        /*
                         * TEMPLATE FILE CONFIGURATION
                         */
                        if (!empty($this->templateFile)) {
                            try {
                                $templateFileXML = new \SimpleXMLElement(file_get_contents($this->templateFile));
                                $template['config'] = $this->templateFile;
                                $templateXML = $templateFileXML->xpath('//template[@key="' . $template['key'] . '"]');
                                if (!empty($templateXML)) {
                                    // Template Information
                                    $templateXML = current($templateXML);
                                    $template['title'] = $this->simpleQuery($templateXML, 'title');
                                    $template['description'] = $this->simpleQuery($templateXML, 'description');
                                    $template['file'] = $this->simpleQuery($templateXML, 'file');
                                    $template['thumbnail'] = $this->simpleQuery($templateXML, 'thumbnail');
                                    $regionMatches = $templateXML->xpath('regions/region');

                                    // Regions
                                    $template['regions'] = array();
                                    if (!empty($regionMatches)) {
                                        foreach ($regionMatches as $regionMatch) {
                                            $region = array();
                                            $region['key'] = $this->simpleQuery($regionMatch, '@key');
                                            $region['id'] = $this->simpleQuery($regionMatch, '@id');
                                            $region['title'] = $this->simpleQuery($regionMatch, 'title');
                                            $template['regions'][$region['id']] = $region;
                                        }
                                    }

                                }
                            } catch (Exception $e) {
                                $template['config'] = 'Unable to parse ' . $this->templateFile;
                            }
                        }
                        $collections['template'] = $template;
                    }


                    /*
                     * NODE FRAGMENT CHILDREN
                     */
                    $childrenFragments = array();
                    $regionFragments = $nodeFragment->getContainedChildrenRegionFragments();
                    foreach ($regionFragments as $region => $fragments) {
                        foreach ($fragments as $fragment) {
                            $childFragmentOverview = array(
                                'region' => $region,
                                'type' => $fragment->getType(),
                                'cmsType' => $this->getFragmentCMSType($fragment),
                                'id' => $fragment->getId(),
                                'view' => array_keys($fragment->getViewData()),
                                'viewDataStructure' => $this->getFragmentDataStructure($fragment)
                            );

                            // Get information about the template search paths
                            $templateLocation = $this->templateMapping->getTemplateForFragment($fragment);
                            $childFragmentOverview['searchedPaths'] = is_array($templateLocation)
                                    ? implode(', ', $templateLocation) : $templateLocation;
                            if (is_array($templateLocation) && !empty($this->engine)) {
                                foreach ($templateLocation as $templateLocationFile) {
                                    if ($this->engine->exists($templateLocationFile)) {
                                        $childFragmentOverview['used'] = $templateLocationFile;
                                        break;
                                    }
                                }
                            }

                            $childrenFragments[$region][] = $childFragmentOverview;
                        }
                    }
                    $nodeAttachedFragmentCollection['regionsWithFragments'] = array_keys($regionFragments);
                    $collections['children'] = $childrenFragments;

                    $collections['nodeAttachedFragment'] = $nodeAttachedFragmentCollection;
                }
            }
        }

        $this->data['collections'] = $collections;
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    function getName()
    {
        return 'flint_cms_dispatcher';
    }


    public function getCollectionCount()
    {
        $count = 0;
        if (!empty($this->data['collections']['node'])) $count++;
        if (!empty($this->data['collections']['nodeAttachedFragment'])) $count++;
        if(!empty($this->data['collections']['children']))
            $count += count($this->data['collections']['children']);
        return $count;
    }

    public function getCollections()
    {
        return $this->data['collections'];
    }


    private function simpleQuery($xml, $query)
    {
        if (empty($xml)) return;
        $results = $xml->xpath($query);
        if (!empty($results)) {
            return (string)current($results);
        }
    }

    private function debug($object)
    {
        $dump = 'Unable to debug unless xdebug is running';
        if (function_exists('xdebug_call_class')) {
            ini_set('xdebug.var_display_max_children', 3);
            ob_start();
            xdebug_var_dump($object);
            $dump = ob_get_clean();
        }
        return $dump;
    }

    private function getFragmentModel()
    {
        if (empty($this->fragmentFile)) return;
        if (!empty($this->fragmentModel)) return $this->fragmentModel;

        try {
            $this->fragmentModel = new \SimpleXMLElement(file_get_contents($this->fragmentFile));
        } catch (\Exception $e) {
            return null;
        }

        return $this->fragmentModel;

    }

    private function getFragmentCMSType($fragment)
    {
        $fragmentModel = $this->getFragmentModel();
        $fragmentType = $fragment->getType();
        $type = array();
        // Get information about the fragment
        if (!empty($fragmentModel)) {
            $typeDefinitionMatch = $fragmentModel->xpath('//type-definition[@key="' . ($fragmentType) . '"]');
            if (!empty($typeDefinitionMatch)) {
                $typeDefinitionMatch = current($typeDefinitionMatch);
                $type = array();
                $type['title'] = $this->simpleQuery($typeDefinitionMatch, 'title');
                $type['description'] = $this->simpleQuery($typeDefinitionMatch, 'description');
            }
        }
        return $type;
    }

    private function getFragmentDataStructure($fragment)
    {
        $fragmentModel = $this->getFragmentModel();
        $fragmentType = $fragment->getType();

        $fragmentDataStructure = array();

        if (!empty($fragmentModel)) {
            // Get information about the view elements
            $typeMatch = $fragmentModel->xpath('//type[@key="' . ($fragmentType) . '"]');
            if (!empty($typeMatch)) $typeMatch = current($typeMatch);
        }

        $fragmentDataStructure = array();
        $viewData = $fragment->getViewData();
        if (!empty($viewData)) {
            foreach ($viewData as $viewDataKey => $viewDataVal) {
                $struct = array();
                $struct['key'] = $viewDataKey;
                $struct['debug'] = $this->debug($viewDataVal);

                // Search for information about the view key in the fragment model (see if it is CMS managed)
                if (!empty($fragmentModel)) {
                    // Is CMS Managed?
                    $attributeMatch = $fragmentModel->xpath('//attribute-definition[@key="' . $this->formatConverter->getHyphenSeparated($viewDataKey) . '"]');
                    if (!empty($attributeMatch)) {
                        // Look up any configuration for this parameter
                        $attributeMatch = current($attributeMatch);
                        $struct['title'] = $this->simpleQuery($attributeMatch, 'title');
                        $struct['description'] = $this->simpleQuery($attributeMatch, 'description');
                        $struct['type'] = $this->simpleQuery($attributeMatch, '@type');
                        $struct['assetTypeReference'] = $this->simpleQuery($attributeMatch, '@asset-type-reference');
                        $struct['configuration'] = array();
                        $configurationMatches = $attributeMatch->xpath('configuration/set-property');
                        if (!empty($configurationMatches)) {
                            foreach ($configurationMatches as $configurationMatch) {
                                $configurationProperty = array();
                                $configurationProperty['key'] = $this->simpleQuery($configurationMatch, '@key');
                                $configurationProperty['value'] = $this->simpleQuery($configurationMatch, '@value');
                                $struct['configuration'][] = $configurationProperty;
                            }
                        }

                        // Look for any specific params for this type (mandatory)
                        if (!empty($typeMatch)) {
                            $attributeMatch = $typeMatch->xpath('attribute-group/attribute[key="' . $this->formatConverter->getHyphenSeparated($viewDataKey) . '"]');
                            if (!empty($attributeMatch)) {
                                $attributeMatch = current($attributeMatch);
                                $struct['mandatory'] = $this->simpleQuery($attributeMatch, '@mandatory');
                            }
                        }
                    }
                }

                $fragmentDataStructure[$viewDataKey] = $struct;
            }
        }

        return $fragmentDataStructure;
    }

    /**
     * @param string $fragmentFile
     */
    public function setFragmentFile($fragmentFile)
    {
        $this->fragmentFile = $fragmentFile;
    }

    /**
     * @return string
     */
    public function getFragmentFile()
    {
        return $this->fragmentFile;
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }
}
