<?php
/**
 * ViewModuleSlid
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewModuleSlid extends TPage
{
    public function __construct()
    {
        parent::__construct();
        // replace the main section variables to section header
        TTransaction::open('app');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('active', '=', 1));
        $objects = Slider::getObjects($criteria);
        TTransaction::close();

        $data = [];
        foreach ($objects as $object) {
            $data['carousel'][] = [
                'banner' => $object->directory
            ];
        }

        // replace the main section variables to section body
        $body = new THtmlRenderer('app/pages/module/view_module_slid.html');
        $body->enableSection('main', $data);
        // add the template to the page
        parent::add($body);
    }
}
