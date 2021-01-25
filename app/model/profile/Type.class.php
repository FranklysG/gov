<?php
/**
 * Type Active Record
 * @author  <your-name-here>
 */
class Type extends TRecord
{
    const TABLENAME = 'type';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }


}
