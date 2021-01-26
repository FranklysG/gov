<?php
/**
 * PublicView
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PublicView extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        // replace the main section variables to section header
        TTransaction::open('app');
        $objects = Menu::getObjects();
        TTransaction::close();
    
        $replace = [];
        foreach($objects as $object){
            $replace['menu'][] = [
                'name' => $object->name,
                'rout' => $object->rout
            ];
        }

        $menu = new THtmlRenderer('./app/pages/module/view_module_menu.html');

        $menu->enableSection('main', $replace);

        // add the template to the page
        parent::add($menu);
    }
}
