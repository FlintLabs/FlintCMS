<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 14/09/11
 */
namespace FlintLabs\Bundle\FlintCMSBundle\Service;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
interface TemplateRegisterServiceInterface
{
    public function registerConfig($config);

    public function getConfig($template);
}
