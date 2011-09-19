<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 14/09/11
 */
namespace FlintCMS\Bundle\FrontBundle\Service;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
interface FragmentRegisterServiceInterface
{
    public function registerConfig($config);

    public function getConfig($type);
}
