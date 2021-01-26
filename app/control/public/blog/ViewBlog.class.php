<?php
/**
 * ViewBlog
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewBlog extends TPage
{
    public function __construct()
    {
        parent::__construct();
       
        
        // replace the main section variables to section body
        $body = THtmlRenderer::create('app/pages/blog/view_blog.html');
                
        // add the template to the page
        parent::add(new ViewHeader);
        parent::add($body);
        parent::add(new ViewFooter);
    }
}
