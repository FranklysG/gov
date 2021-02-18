<?php
/**
 * Comment Active Record
 * @author  <your-name-here>
 */
class Comment extends TRecord
{
    const TABLENAME = 'comment';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('post_id');
    }


}
