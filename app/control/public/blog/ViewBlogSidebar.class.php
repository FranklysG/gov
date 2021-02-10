<?php
/**
 * ViewBlogSidbar
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewBlogSidebar extends TPage
{
    public function __construct()
    {
        parent::__construct();
        TTransaction::open('app');
        $posts = Post::getObjects();
        $categorys = Category::getObjects();
        $data = [];
        $sub_category = [];
        foreach($categorys as $object){
            foreach ($object->getSubCategorys() as $sub) {
               $sub_category['sub_category'][] = [
                    'sub_name' => $sub->name,
                    'sub_slug' => $sub->slug,
               ];
            }
            
            $data['category'][] = [
                'name' => $object->name,
                'slug' => $object->slug,
                'sub_category' => [$sub_category]
            ];

            $sub_category = [];
        }

        foreach ($posts as $object) {
            $data['lasted_post'][] = [
                'post_id' => $object->id,
                'thumbnail' => $object->thumbnail,
                'author' => $object->system_user->name,
                'title' => $object->title,
                'resume' => mb_strimwidth($object->resume,0 ,180),
                'date' => Convert::toDayMonthString($object->created_at),
                'year' => Convert::toYear($object->created_at)
            ];
        }

        TTransaction::close();
        
        // replace the main section variables to section body
        $sidebar = THtmlRenderer::create('app/pages/blog/view_blog_sidebar.html', $data);
       
        // add the template to the page
        parent::add($sidebar);
    }
}
