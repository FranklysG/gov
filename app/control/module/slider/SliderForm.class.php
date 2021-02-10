<?php
/**
 * SliderForm Form
 * @author  <your name here>
 */
class SliderForm extends TWindow
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
        parent::setSize(400, null);
        parent::setTitle('Novo banner');
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Slider');
        $this->form->setFieldSizes('100%');
        

        // create the form fields
        $id = new THidden('id');
        $system_user_id = new THidden('system_user_id', 'app', 'SystemUser', 'id', 'frontpage_id');
        $name = new TEntry('name');
        $active = new TCombo('active');
        $active->addItems([
            true => 'ativado',
            false => 'desativado'
        ]);
        $created_at = new TDate('created_at');
        $updated_at = new TDate('updated_at');

        $directory = new TFile('directory');
        $directory->setCompleteAction(new TAction(array($this, 'onComplete')));
        $directory->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );

        $this->frame = new TElement('div');
        $this->frame->id = 'directory_frame';
        $this->frame->style = 'width:100%;height:auto;;border:1px solid gray;padding:4px;';


        // add the fields
        $row = $this->form->addFields( [ $id]);
        $row = $this->form->addFields( [ new TLabel('Nome'), $name ],
                                        [ new TLabel('Mostar'), $active ],
                                        [ new TLabel('<br>Selecione a imagem <i style="font-size:10px;"> (2048x478)</i>'), $directory ],
                                        [ new TLabel(''), $this->frame ],
                                     );

        $row->layout = ['col-sm-8','col-sm-4','col-sm-12','col-sm-12'];
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
      
            $object = Slider::find($data->id);  // create an empty object
            if(!isset($object->id))
                $object = new Slider; // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->system_user_id = TSession::getValue('userid'); // load the object with data
            $object->store(); // save the object
            
            // paste archive name and sub_folder
            AppUtil::paste_another_folder($data->directory, 'slider');
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), new TAction(['SliderList', 'onReload']));
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
                $object = new Slider($key); // instantiates the Active Record
                if (isset($object->directory)) {
                    $image = new TImage("tmp/slider/{$object->directory}");
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
        $directory = PATH."/tmp/slider/{$param['directory']}";
        TScript::create("$('#directory_frame').html('')");
        TScript::create("$('#directory_frame').append(\"<img style='width:100%' src='$directory'>\");");
    }
}
