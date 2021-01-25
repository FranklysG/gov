<?php
/**
 * ViewContact
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewContact extends TPage
{
    public function __construct()
    {
        parent::__construct();
       
        
        // replace the main section variables to section body
        $body = new THtmlRenderer('app/pages/body/view_contact.html');
        $body->enableSection('main', array());
        
        
        // add the template to the page
        parent::add(new ViewHeader);
        parent::add($body);
        parent::add(new ViewFooter);
    }
}
