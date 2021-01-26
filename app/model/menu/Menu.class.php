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
    
    
    private $system_user;
    private $sub_menus;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('name');
        parent::addAttribute('route');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_system_user
     * Sample of usage: $menu->system_user = $object;
     * @param $object Instance of SystemUser
     */
    public function set_system_user(SystemUser $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }
    
    /**
     * Method get_system_user
     * Sample of usage: $menu->system_user->attribute;
     * @returns SystemUser instance
     */
    public function get_system_user()
    {
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUser($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
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
