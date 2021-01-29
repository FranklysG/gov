<?php
/**
 * MenuForm Master/Detail
 * @author  <your name here>
 */
class MenuForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setTargetContainer('adianti_right_panel');

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Menu');
        $this->form->setFormTitle('Menu');
        $this->form->setFieldSizes('100%');
        
        // create the form fields
        $id = new THidden('id');
        $name = new TEntry('name');
        $route = new TCombo('route');
        $route->addItems([
            'home' => 'home',
            'licitacao' => 'licitacao',
            'noticias' => 'noticias',
        ]);
        $created_at = new THidden('created_at');
        $updated_at = new THidden('updated_at');

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        $detail_name = new TEntry('detail_name');
        $detail_route = new TCombo('detail_route');
        $detail_route->addItems([
            'home' => 'home',
            'licitacao' => 'licitacao',
            'noticias' => 'noticias',
        ]);

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // add the fields
        $this->form->addFields([ $id]);
        $row = $this->form->addFields( [ new TLabel('Nome'), $name ],[],
                                [ new TLabel('Paginas Disponoveis'), $route ] );

        $row->layout = ['col-sm-12','col-sm-12','col-sm-12'];
        
        // detail fields
        $this->form->addContent( ['<br><h5>Sub Menus</h5><hr>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        
        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Register', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');

        $row = $this->form->addFields( [new TLabel('Name'), $detail_name],
                                [new TLabel('Route'), $detail_route],
                                [new TLabel(''), $add] );

        $row->layout = ['col-sm-4','col-sm-4','col-sm-4'];
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('SubMenu_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = " width:100%;margin-bottom: 10px";
        
        // items
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('name', 'Name', 'left', 10) );
        $this->detail_list->addColumn( new TDataGridColumn('route', 'Route', 'left', 10) );

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addHeaderActionLink( _t('Close'), new TAction(array($this, 'onClose')), 'fa:times red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Add detail item
     * @param $param URL parameters
     */
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            /** validation sample
            if (empty($data->fieldX))
            {
                throw new Exception('The field fieldX is required');
            }
            **/
            
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['name'] = $data->detail_name;
            $grid_data['route'] = $data->detail_route;
            
            // insert row dynamically
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('SubMenu_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_name = '';
            $data->detail_route = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Menu', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Edit detail item
     * @param $param URL parameters
     */
    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_name = $param['name'];
        $data->detail_route = $param['route'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Menu', $data, false, false );
    }
    
    /**
     * Delete detail item
     * @param $param URL parameters
     */
    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_name = '';
        $data->detail_route = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Menu', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('SubMenu_list', $param['uniqid']);
    }
    
    /**
     * Load Master/Detail data from database to form
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('app');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Menu($key);
                $items  = SubMenu::where('menu_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('app');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Menu;
            $master->fromArray( (array) $data);
            $master->store();
            
            SubMenu::where('menu_id', '=', $master->id)->delete();
            
            if( isset($param['SubMenu_list_name']) )
            {
                foreach( $param['SubMenu_list_name'] as $key => $item_id )
                {
                    $detail = new SubMenu;
                    $detail->name  = $param['SubMenu_list_name'][$key];
                    $detail->route  = $param['SubMenu_list_route'][$key];
                    $detail->menu_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Menu', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}
