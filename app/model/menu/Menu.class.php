<?php
/**
 * Menu Active Record
 * @author  <your-name-here>
 */
class Menu extends TRecord
{
    const TABLENAME = 'menu';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $sub_menus;
    private $profile_type;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('profile_type_id');
        parent::addAttribute('name');
        parent::addAttribute('rout');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method addSubMenu
     * Add a SubMenu to the Menu
     * @param $object Instance of SubMenu
     */
    public function addSubMenu(SubMenu $object)
    {
        $this->sub_menus[] = $object;
    }
    
    /**
     * Method getSubMenus
     * Return the Menu' SubMenu's
     * @return Collection of SubMenu
     */
    public function getSubMenus()
    {
        return $this->sub_menus;
    }
    
    /**
     * Method set_profile_type
     * Sample of usage: $menu->profile_type = $object;
     * @param $object Instance of ProfileType
     */
    public function set_profile_type(ProfileType $object)
    {
        $this->profile_type = $object;
        $this->profile_type_id = $object->id;
    }
    
    /**
     * Method get_profile_type
     * Sample of usage: $menu->profile_type->attribute;
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
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->sub_menus = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related SubMenu objects
        $repository = new TRepository('SubMenu');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('menu_id', '=', $id));
        $this->sub_menus = $repository->load($criteria);
    
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
    
        // delete the related SubMenu objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('menu_id', '=', $this->id));
        $repository = new TRepository('SubMenu');
        $repository->delete($criteria);
        // store the related SubMenu objects
        if ($this->sub_menus)
        {
            foreach ($this->sub_menus as $sub_menu)
            {
                unset($sub_menu->id);
                $sub_menu->menu_id = $this->id;
                $sub_menu->store();
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
        // delete the related SubMenu objects
        $repository = new TRepository('SubMenu');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('menu_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
