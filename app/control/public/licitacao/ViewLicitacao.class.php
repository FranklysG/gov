<?php
/**
 * ViewLicitacao
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Franklys Guimarães
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ViewLicitacao extends TPage
{
    private $datagrid;
    private $pageNavigation;

    public function __construct()
    {
        parent::__construct();
        
        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->setActionSide('right');

        // create the datagrid columns
        $code       = new TDataGridColumn('code',    'Code',    'center', '10%');
        $name       = new TDataGridColumn('name',    'Name',    'left',   '30%');
        $city       = new TDataGridColumn('city',    'City',    'left',   '30%');
        $state      = new TDataGridColumn('state',   'State',   'left',   '30%');
        
        // add the columns to the datagrid, with actions on column titles, passing parameters
        $this->datagrid->addColumn($code);
        $this->datagrid->addColumn($name);
        $this->datagrid->addColumn($city);
        $this->datagrid->addColumn($state);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onView'), ['name' => '{name}'] );
        $action1->setUseButton(TRUE);
        $action1->setButtonClass('btn btn-default');
        $action1->setLabel('Detalhe');
        $action1->setImage('fas:download blue');
        // $action1->setField('id');
        // $action1->setDisplayCondition( array($this, 'displayColumn') );

        // add the actions to the datagrid
        $this->datagrid->addAction($action1);

        // creates the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $panel = new TPanelGroup();
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // turn on horizontal scrolling inside panel body
        $panel->getBody()->style = "overflow-x:auto;";

        // replace the main section variables to section body
        $body = THtmlRenderer::create('app/pages/licitacao/view_licitacao.html', array(
            'datagrid' => $panel
        ));
        
        // add the template to the page
        parent::add(new ViewHeader);
        parent::add($body);
        parent::add(new ViewFooter);
    }

    /**
     * Define when the action can be displayed
     */
    public function displayColumn( $object )
    {
        if ($object->code > 1 AND $object->code < 4)
        {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Load the data into the datagrid
     */
    function onReload()
    {
        $this->datagrid->clear();
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->name   = 'B.B. King';
        $item->city   = 'Itta Bena';
        $item->state  = 'Mississippi (US)';
        
        for ($i=0; $i <= 13 ; $i++) { 

            $item->code   = $i;
            $this->datagrid->addItem($item);
        }
        
        $count = 13;
        $param = '';
        $limit = 10;

        $this->pageNavigation->setCount($count); // count of records
        $this->pageNavigation->setProperties($param); // order, page
        $this->pageNavigation->setLimit($limit); // limit
    }
    
    /**
     * method onView()
     * Executed when the user clicks at the view button
     */
    public static function onView($param)
    {
        // get the parameter and shows the message
        $key=$param['key'];
        new TMessage('info', "The name is: <b>{$key}</b>");
        // TScript::create("__adianti_load_page('index.php?class=CartProdutoList&method=onReload&offset={$offset}&limit=10&direction=asc&page={$page}&first_page=1&order=id');");
        TScript::create("__adianti_load_page('licitacao');");
    }
    
    /**
     * shows the page
     */
    public function show()
    {
        $this->onReload();
        parent::show();
    }
}
