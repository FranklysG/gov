<?php
/**
 * SubCategory Active Record
 * @author  <your-name-here>
 */
class SubCategory extends TRecord
{
    const TABLENAME = 'sub_category';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('category_id');
        parent::addAttribute('name');
        parent::addAttribute('slug');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }


}
