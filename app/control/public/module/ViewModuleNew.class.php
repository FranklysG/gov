<?php
/**
 * ViewModuleNews
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewModuleNew extends TPage
{
    public function __construct()
    {
        parent::__construct();
       
        
        // replace the main section variables to section body
        $body = new THtmlRenderer('app/pages/module/view_module_new.html');
        $body->enableSection('main', array());
        
        
        // add the template to the page
        parent::add($body);
    }
}
