<?php
/**
 * ViewHeader
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewHeader extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        // replace the main section variables to section header
        TTransaction::open('app');
        $preference = SystemPreference::getAllPreferences();
        $objects = Menu::getObjects();
        TTransaction::close();
    
        $replace = array();
        $replace['header_logo'] = ['logo' => $preference['logo']];
        foreach($objects as $object){
            $replace['header_menu'][] = [
                'name' => $object->name, 
                'route' => $object->route
            ];      
        }

        $header = THtmlRenderer::create('app/pages/main/view_header.html', $replace);
    
        // add the template to the page
        parent::add($header);
    }
}
