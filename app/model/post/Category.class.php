<?php
/**
 * Category Active Record
 * @author  <your-name-here>
 */
class Category extends TRecord
{
    const TABLENAME = 'category';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $sub_categorys;

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

    
    /**
     * Method addSubCategory
     * Add a SubCategory to the Category
     * @param $object Instance of SubCategory
     */
    public function addSubCategory(SubCategory $object)
    {
        $this->sub_categorys[] = $object;
    }
    
    /**
     * Method getSubCategorys
     * Return the Category' SubCategory's
     * @return Collection of SubCategory
     */
    public function getSubCategorys()
    {
        return $this->sub_categorys;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->sub_categorys = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related SubCategory objects
        $repository = new TRepository('SubCategory');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('category_id', '=', $id));
        $this->sub_categorys = $repository->load($criteria);
    
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
    
        // delete the related SubCategory objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('category_id', '=', $this->id));
        $repository = new TRepository('SubCategory');
        $repository->delete($criteria);
        // store the related SubCategory objects
        if ($this->sub_categorys)
        {
            foreach ($this->sub_categorys as $sub_category)
            {
                unset($sub_category->id);
                $sub_category->category_id = $this->id;
                $sub_category->store();
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
        // delete the related SubCategory objects
        $repository = new TRepository('SubCategory');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('category_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
