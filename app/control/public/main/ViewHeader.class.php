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
        
        $header = new THtmlRenderer('app/pages/main/view_header.html');
        $menu = new THtmlRenderer('app/pages/module/view_module_menu.html');
        $objects = Menu::getObjects();
        
        $items = [];
        foreach($objects as $object){
            $items['menu'][] = [
                'rout' => $object->rout,
                'name' => $object->name
            ];
        }
       
        
        $menu->enableSection('main', $items);
        $header->enableSection('main', ['view_module_menu' => $menu]);
        TTransaction::close();

        // add the template to the page
        parent::add($header);
    }
}
