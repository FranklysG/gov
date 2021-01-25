<?php
/**
 * ViewFooter
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewFooter extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        // replace the main section variables to section footer
        $footer = new THtmlRenderer('app/pages/main/view_footer.html');
        $footer->enableSection('main', array());

        // add the template to the page
        parent::add($footer);
    }
}
