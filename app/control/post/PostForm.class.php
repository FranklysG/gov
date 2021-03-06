<?php
/**
 * PostForm Form
 * @author  <your name here>
 */
class PostForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Post');
        $this->form->setFormTitle('Novo post');
        $this->form->setFieldSizes('100%');

        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TDBUniqueSearch('system_user_id', 'app', 'SystemUser', 'id', 'frontpage_id');
        $system_user_id->setMinLength(1);
        $category_id = new TDBUniqueSearch('category_id', 'app', 'Category', 'id', 'name');
        $category_id->setMinLength(1);
        $category_id->addValidation('Categoria', new TRequiredValidator);
        $title = new TEntry('title');
        $title->addValidation('Titulo', new TRequiredValidator);
        $resume = new TText('resume');
        $resume->setSize('100%', 100);
        $resume->addValidation('Resumo', new TRequiredValidator);
        $content = new THtmlEditor('content');
        $content->setSize('100%', 500);
        $date = new TEntry('date');
        $created_at = new TEntry('created_at');
        $updated_at = new TEntry('updated_at');

        $thumbnail = new TFile('thumbnail');
        $thumbnail->addValidation('Thumbnail', new TRequiredValidator);
        $thumbnail->setCompleteAction(new TAction(array($this, 'onComplete')));
        $thumbnail->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );

        $this->frame = new TElement('div');
        $this->frame->id = 'thumbnail_frame';
        $this->frame->style = 'width:250px;height:auto;;border:1px solid gray;padding:4px;';


        // add the fields
        $this->form->addFields( [ $id ] );
        $row = $this->form->addFields( 
                                [ new TLabel('Thumbnail'), $thumbnail ] ,
                                [],
                                [ new TLabel(''), $this->frame ],
                                [],
                                [ new TLabel('Titulo da post'), $title ] ,
                                [ new TLabel('Categoria'), $category_id ] ,
                                [ new TLabel('<br>Resumo do post'), $resume ],                                
                                [ new TLabel(''), $content ]                                 
                            );

        $row->layout = ['col-sm-3','col-sm-9','col-sm-3','col-sm-12','col-sm-6','col-sm-6','col-sm-12','col-sm-12'];


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
            
            $object = Post::find($data->id);  // create an empty object
            if(!isset($object->id))
                $object = new Post; // create an empty object
            $object->slug = Convert::toWithoutAccent($data->title);
            $object->system_user_id = TSession::getValue('userid'); // load the object with data
            $object->fromArray( (array) $data); // load the object with data
            
            $object->store(); // save the object
            
            // paste archive name and sub_folder after folder tmp/
            AppUtil::paste_another_folder($data->thumbnail, 'post/thumbnail');
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
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
                $object = new Post($key); // instantiates the Active Record
                if (isset($object->thumbnail)) {
                    $image = new TImage("tmp/post/thumbnail/{$object->thumbnail}");
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
        $thumbnail = PATH."/tmp/post/thumbnail/{$param['thumbnail']}";
        TScript::create("$('#thumbnail_frame').html('')");
        TScript::create("$('#thumbnail_frame').append(\"<img style='width:100%' src='$thumbnail'>\");");
    }
}
