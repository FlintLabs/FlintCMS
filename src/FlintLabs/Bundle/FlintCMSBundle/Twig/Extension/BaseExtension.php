<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FlintCMSBundle\Twig\Extension;
use FlintLabs\Bundle\FlintCMSBundle\Twig\TokenParser\FlintRegionTokenParser,
FlintLabs\Bundle\FlintCMSBundle\Service\TemplateMappingServiceInterface,
FlintLabs\Bundle\FlintCMSBundle\Service\FormatConverterServiceInterface,
FlintLabs\Bundle\FlintCMSBundle\Twig\NavigationHelperInterface,
Symfony\Component\HttpKernel\Log\LoggerInterface;
/**
 * Base Extensions for twig rendering
 *
 * View http://twig.sensiolabs.org/doc/extensions.html for more information about extensions
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class BaseExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $name = 'pure';

    /**
     * @var \FlintLabs\Bundle\FlintCMSBundle\Twig\Extension\TemplateMappingServiceInterface
     */
    private $templateMapping;

    /**
     * @var \FlintLabs\Bundle\FlintCMSBundle\Twig\NavigationHelperInterface
     */
    private $navigationHelper;

    /**
     * @var \FlintLabs\Bundle\FlintCMSBundle\Twig\Extension\FormatConverterServiceInterface
     */
    private $formatConverter;

    protected $log;

    /**
     * @param \FlintLabs\Bundle\FlintCMSBundle\Service\TemplateMappingServiceInterface $templateMapping
     * @param \FlintLabs\Bundle\FlintCMSBundle\Twig\NavigationHelperInterface $navigationHelper
     * @param \FlintLabs\Bundle\FlintCMSBundle\Service\FormatConverterServiceInterface $formatConverter
     */
    public function __construct(NavigationHelperInterface $navigationHelper,
        FormatConverterServiceInterface $formatConverter,
        LoggerInterface $log)
    {
        $this->navigationHelper = $navigationHelper;
        $this->formatConverter = $formatConverter;
        $this->log = $log;
    }

    /**
     * @param \FlintLabs\Bundle\FlintCMSBundle\Service\TemplateMappingServiceInterface $templateMapping
     * @return void
     */
    public function setTemplateMapping(TemplateMappingServiceInterface $templateMapping)
    {
        $this->templateMapping = $templateMapping;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name string name of the extension
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            // {% flint_region 'body' %}
            new FlintRegionTokenParser(),
        );
    }

    public function getFilters()
    {
        return array(
            'href' => new \Twig_Filter_Method($this, 'getHref'),
            'imageSrc' => new \Twig_Filter_Method($this, 'getImageSrc'),
            'var_dump' => new \Twig_Filter_Function('var_dump')
        );
    }

    public function getHref($value)
    {
        return $value;
    }

    public function getImageSrc($value, $options = array())
    {
        if (!empty($value->file)) {
            $value = (string)$value;
        }
        if (!empty($options)) {
            $value = 'image-resizer.php?src=' . urlencode($value);
            foreach ($options as $key => $val) {
                $value .= '&' . $key . '=' . $val;
            }
        } else {
            $value = 'uploads/' . $value;
        }
        return $value;
    }


    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getGlobals()
    {
        return array(
            'nav' => $this->navigationHelper,
        );
    }

    /**
     * @param $env
     * @param $region
     * @param $context
     * @return void
     * @TODO: Look up the mapping for the template key (will this be known?)
     */
    public function renderRegion($env, $region, $context)
    {
        // Check we can use this function (must have context to the node element)
        $node = !empty($context['node']) ? $context['node'] : null;
        if (empty($node)) throw new \Exception('Variable "node" missing from context. Node must be present in view data to use this function. If using "only" ensure to pass with {"node":node} when using pureregion');

        // Get the fragment
        $fragment = $node->getFragment();
        $children = $fragment->getContainedChildrenRegionFragments();
        if (empty($children[$region]) || !is_array($children[$region])) return;
        foreach ($children[$region] as $sortOrder => $childFragment) {
            $templateFile = $this->templateMapping->getTemplateForFragment($childFragment);
            if (is_array($templateFile)) {
                $loaded = false;
                foreach ($templateFile as $templateFilePossibility) {
                    try {
                        $template = $env->loadTemplate($templateFilePossibility);
                        $template->display(array_merge($context, array('region' => $region, 'fragment' => $childFragment), $childFragment->getViewData()));
                        $loaded = true;
                        break;
                    } catch (\Twig_Error_Loader $e) {
                        // Keep trying
                    }
                }
                if (!$loaded) {
                    $this->log->warn('Could not locate the template file for ' . $childFragment->getType() . '. Tried looking at: ' . implode(',', $templateFile));
                }
            } else {
                try {
                    $template = $env->loadTemplate($templateFile);
                    $template->display(array_merge($context, array('region' => $region, 'fragment' => $childFragment), $childFragment->getViewData()));
                } catch(\Twig_Error_Loader $e) {
                    $this->log->warn('Could not locate the template file for ' . $childFragment->getFragmentType() . '. Tried looking at: ' . $templateFile);
                }
            }
        }
    }

}
