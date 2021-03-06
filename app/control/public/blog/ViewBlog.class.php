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
        TTransaction::open('app');
        $criteria = new TCriteria;
        $criteria->setProperty('limit' , 8);
        $objects = Post::getObjects($criteria);
        
        $data = [];
        foreach($objects as $object){
            $data['post'][] = [
                'post_id' => $object->id,
                'thumbnail' => $object->thumbnail,
                'author' => $object->system_user->name,
                'title' => $object->title,
                'category' => $object->category->name,
                'comments' => 'comments',
                'resume' => mb_strimwidth($object->resume,0 ,180),
                'date' => Convert::toDayMonthString($object->created_at),
                'year' => Convert::toYear($object->created_at)
            ];
        }

        $data['right_sidebar'] = new ViewBlogSidebar;
        TTransaction::close();
        // replace the main section variables to section body
        $body = THtmlRenderer::create('app/pages/blog/view_blog.html',$data);
        $body->disableHtmlConversion();
        // add the template to the page
        parent::add(new ViewHeader);
        parent::add($body);
        parent::add(new ViewFooter);
    }
}
