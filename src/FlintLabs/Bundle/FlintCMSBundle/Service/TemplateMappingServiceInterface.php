<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 29/08/11
 */

namespace FlintLabs\Bundle\FlintCMSBundle\Service;
use FlintLabs\Bundle\FlintCMSBundle\Entity\Fragment;

/**
 * Handles mapping fragments to views to be rendered
 * @author camm (camm@flintinteractive.com.au)
 */
interface TemplateMappingServiceInterface {
    /**
     * Returns with the corresponding view for the fragment
     * @abstract
     * @param Fragment $fragment
     * @return string
     */
    public function getTemplateForFragment(Fragment $fragment);
}