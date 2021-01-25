<?php
/**
 * Post Active Record
 * @author  <your-name-here>
 */
class Post extends TRecord
{
    const TABLENAME = 'post';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $profile_type;
    private $category;
    private $comments;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('profile_type_id');
        parent::addAttribute('category_id');
        parent::addAttribute('title');
        parent::addAttribute('author');
        parent::addAttribute('content');
        parent::addAttribute('date');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_profile_type
     * Sample of usage: $post->profile_type = $object;
     * @param $object Instance of ProfileType
     */
    public function set_profile_type(ProfileType $object)
    {
        $this->profile_type = $object;
        $this->profile_type_id = $object->id;
    }
    
    /**
     * Method get_profile_type
     * Sample of usage: $post->profile_type->attribute;
     * @returns ProfileType instance
     */
    public function get_profile_type()
    {
        // loads the associated object
        if (empty($this->profile_type))
            $this->profile_type = new ProfileType($this->profile_type_id);
    
        // returns the associated object
        return $this->profile_type;
    }
    
    
    /**
     * Method set_category
     * Sample of usage: $post->category = $object;
     * @param $object Instance of Category
     */
    public function set_category(Category $object)
    {
        $this->category = $object;
        $this->category_id = $object->id;
    }
    
    /**
     * Method get_category
     * Sample of usage: $post->category->attribute;
     * @returns Category instance
     */
    public function get_category()
    {
        // loads the associated object
        if (empty($this->category))
            $this->category = new Category($this->category_id);
    
        // returns the associated object
        return $this->category;
    }
    
    
    /**
     * Method addComment
     * Add a Comment to the Post
     * @param $object Instance of Comment
     */
    public function addComment(Comment $object)
    {
        $this->comments[] = $object;
    }
    
    /**
     * Method getComments
     * Return the Post' Comment's
     * @return Collection of Comment
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->comments = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related Comment objects
        $repository = new TRepository('Comment');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('post_id', '=', $id));
        $this->comments = $repository->load($criteria);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        // delete the related Comment objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('post_id', '=', $this->id));
        $repository = new TRepository('Comment');
        $repository->delete($criteria);
        // store the related Comment objects
        if ($this->comments)
        {
            foreach ($this->comments as $comment)
            {
                unset($comment->id);
                $comment->post_id = $this->id;
                $comment->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related Comment objects
        $repository = new TRepository('Comment');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('post_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
