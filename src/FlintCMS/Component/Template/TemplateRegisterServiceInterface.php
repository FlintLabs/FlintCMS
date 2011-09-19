<?php
/*
 * Copyright Cameron Manderson (c) 2011 All rights reserved.
 * Date: 14/09/11
 */
namespace FlintCMS\Component\Template;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
interface TemplateRegisterServiceInterface
{
    public function registerConfig($config);

    public function getConfig($template);
}
