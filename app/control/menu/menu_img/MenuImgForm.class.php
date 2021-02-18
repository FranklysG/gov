<?php
/**
 * MenuImgForm Form
 * @author  <your name here>
 */
class MenuImgForm extends TWindow
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::removePadding();
        parent::setSize(320, null);
        parent::setTitle('Novo icone de menu');
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_MenuImg');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $name = new TEntry('name');
        $directory = new TFile('directory');
        $directory->setCompleteAction(new TAction(array($this, 'onComplete')));
        $directory->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );

        $this->frame = new TElement('div');
        $this->frame->id = 'directory_frame';
        $this->frame->style = 'width:100px;height:auto;;border:1px solid gray;padding:4px;';


        // add the fields
        $this->form->addFields([$id]);
        $this->form->addFields( [ new TLabel('Selecione o icone (.png, .jpg, .jpeg)'), $directory ] );
        $this->form->addFields( [ new TLabel('Nome'), $name ] );
        $this->form->addFields( [new TLabel(''), $this->frame] );

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('app'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
                       
            $object = MenuImg::find($data->id);  // create an empty object
            if(!isset($object->id))
                $object = new MenuImg;  // create an empty object
            $object->system_user_id = TSession::getValue('userid'); // load the object with data
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // archive name and sub_folder
            AppUtil::paste_another_folder($data->directory, 'menu_img');

            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['MenuImgList', 'onReload']));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
        
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('app'); // open a transaction
                $object = new MenuImg($key); // instantiates the Active Record
                if (isset($object->directory)) {
                    $image = new TImage("tmp/menu_img/{$object->directory}");
                    $image->style = 'width: 100%';
                    $this->frame->add($image);
                }
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public static function onComplete($param)
    {
        // refresh photo_frame
        $directory = PATH."/tmp/menu_img/{$param['directory']}";
        TScript::create("$('#directory_frame').html('')");
        TScript::create("$('#directory_frame').append(\"<img style='width:100%' src='$directory'>\");");
    }
}
