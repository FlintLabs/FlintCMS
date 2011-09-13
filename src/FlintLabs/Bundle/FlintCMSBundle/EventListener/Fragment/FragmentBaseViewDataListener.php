<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FlintCMSBundle\EventListener\Fragment;
use FlintLabs\Bundle\FlintCMSBundle\Service\FormatConverterServiceInterface,
FlintLabs\Bundle\FlintCMSBundle\EventListener\FragmentDispatchListenerInterface,
FlintLabs\Bundle\FlintCMSBundle\EventListener\FragmentDispatchEvent;

/**
 * Populates the base view data for the fragment, handling conversion from
 * XML into accessible data from the front-end
 * @author camm (camm@flintinteractive.com.au)
 */
class FragmentBaseViewDataListener implements FragmentDispatchListenerInterface
{
    private $formatConverter;

    /**
     * @param FormatConverterServiceInterface $formatConverter
     */
    public function __construct(FormatConverterServiceInterface $formatConverter)
    {
        $this->formatConverter = $formatConverter;
    }

    /**
     * Handles the event of encountering a fragment
     * @param FragmentDispatchEvent $fragmentDispatchEvent
     * @return void
     * @TODO: This is expensive call for large data. Hopefully this is only for small bits of XML data
     */
    public function onFragmentDispatch(FragmentDispatchEvent $fragmentDispatchEvent)
    {
        // Get the XML stored in the database
        $fragment = $fragmentDispatchEvent->getFragment();
        if(!($fragment instanceof \FlintLabs\Bundle\FlintCMSBundle\Entity\ViewModelContainerInterface)) {
            throw new Exception('Encountered a fragment that does not implement the view model container interface');
        }

        // Convert the data
        $xmlToArray = $this->fixArrayKeys($this->formatConverter->getArrayFromXML($fragment->getData()));

        // Place into the view layer
        foreach($xmlToArray as $key => $value) {
            $fragment->addViewData($key, $value);
        }
    }

    public function fixArrayKeys($input) {
        if (!is_array($input)) return $input;
        $output = array();
        foreach ($input as $key => $value) {
            $output[$this->formatConverter->getCamelCase($key)] = $this->fixArrayKeys($value);
        }
        return $output;
    }
}
