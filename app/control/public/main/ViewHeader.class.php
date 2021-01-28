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
        try{
            // replace the main section variables to section header
            TTransaction::open('app');
            $object_logo = SystemPreference::find('logo')->value;
            $objects_menu_img = MenuImg::getObjects();
            $objects_menu = Menu::getObjects();
            TTransaction::close();

            $data = [];
            $sub_menu = [];
            
            $menu_on =  ''; // decide se o menu vai ser dropdown ou não
            $sub_menu_on =  ''; // caso o menu seja de dropdown
            $arrow_down_icon =  ''; // icone do lado do label do menu
            
            $data['logo'] = $object_logo;

            foreach ($objects_menu_img as $object) {
                $data['header_menu_img'][] = [
                    'url' => $object->id,
                    'icone' => $object->directory
                ];
            }

            foreach($objects_menu as $object){
                foreach ($object->getSubMenus() as $value) {
                    $sub_menu[] = [
                        'sub_menu_name' => $value->name, 
                        'sub_menu_route' => $value->route
                    ];

                    $arrow_down_icon =  'fas fa-sort-down'; // icone
                    $menu_on =  "dropdown"; // menu dropdown
                    $sub_menu_on =  'id=navbarDropdown role=button data-toggle=dropdown
                    aria-haspopup=true aria-expanded=false'; // atributos dos links do menu
                }
                
                $data['menu'][] = [
                    'name' => $object->name, 
                    'route' => $object->route,
                    'sub_menu' => $sub_menu,
                    'menu_on' => $menu_on,
                    'sub_menu_on' => $sub_menu_on,
                    'arrow_down_icon' => $arrow_down_icon
                ]; 

                $menu_on =  '';
                $sub_menu_on =  '';  
                $$arrow_down_icon =  '';  
            }

            $header = new THtmlRenderer('app/pages/main/view_header.html');
            $header->enableSection('main', $data);
            
            parent::add($header);
        }catch (Exeption $e) {
                new TMessage('erro', $e->getMessage());
            }
        }
}
