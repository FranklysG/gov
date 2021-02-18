<?php
/**
 * ProfileType Active Record
 * @author  <your-name-here>
 */
class ProfileType extends TRecord
{
    const TABLENAME = 'profile_type';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $profile;
    private $profile_type;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('profile_id');
        parent::addAttribute('type_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

    
    /**
     * Method set_profile
     * Sample of usage: $profile_type->profile = $object;
     * @param $object Instance of Profile
     */
    public function set_profile(Profile $object)
    {
        $this->profile = $object;
        $this->profile_id = $object->id;
    }
    
    /**
     * Method get_profile
     * Sample of usage: $profile_type->profile->attribute;
     * @returns Profile instance
     */
    public function get_profile()
    {
        // loads the associated object
        if (empty($this->profile))
            $this->profile = new Profile($this->profile_id);
    
        // returns the associated object
        return $this->profile;
    }
    
    
    /**
     * Method set_profile_type
     * Sample of usage: $profile_type->profile_type = $object;
     * @param $object Instance of ProfileType
     */
    public function set_profile_type(ProfileType $object)
    {
        $this->profile_type = $object;
        $this->profile_type_id = $object->id;
    }
    
    /**
     * Method get_profile_type
     * Sample of usage: $profile_type->profile_type->attribute;
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
    


}
