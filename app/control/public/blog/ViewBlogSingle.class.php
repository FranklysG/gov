<?php
/**
 * ViewBlogSingle
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewBlogSingle extends TPage
{
    public function __construct($param = null)
    {
        parent::__construct();
        TTransaction::open('app');
        $criteria = new TCriteria;
        if(isset($param['id'])){
            $key = $param['id'];
            $criteria->add(new TFilter('id','=',$key));
        }
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
                'content' => $object->content,
                'date' => Convert::toDayMonthString($object->created_at),
                'year' => Convert::toYear($object->created_at)
            ];
        }

        $data['right_sidebar'] = new ViewBlogSidebar;
        TTransaction::close();
        
        // replace the main section variables to section body
        $body = THtmlRenderer::create('app/pages/blog/view_blog_single.html', $data);
        $body->disableHtmlConversion();
        // add the template to the page
        parent::add(new ViewHeader);
        parent::add($body);
        parent::add(new ViewFooter);
    }

}
