<?php
/**
 * SubMenu Active Record
 * @author  <your-name-here>
 */
class SubMenu extends TRecord
{
    const TABLENAME = 'sub_menu';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('menu_id');
        parent::addAttribute('name');
        parent::addAttribute('route');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }


}
