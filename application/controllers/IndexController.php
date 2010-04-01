<?php



class IndexController extends Zend_Controller_Action
{
    /**
     *
     * @var string
     */
    protected $errorMessage = "";

    /**
     *
     * @var string
     */
    protected $model;

    /**
     *
     * @var int
     */
    protected $actualWeek = 0;


    public function init()
    {
            $this->model = new loginModel();
            $this->actualWeek = (int) date("W",time());
    }
	/**
 	* Display Login
 	*/
	public function indexAction()
	{
	// Wir sind eh angemeldet -> ab zum Kalender
        if(Zend_Auth::getInstance()->hasIdentity())
        {
            $auth = Zend_Auth::getInstance();
            $authStorage = $auth->getStorage();
            $userInfo = $authStorage->read();
            switch($userInfo->usertype){
                    case "20": $this->_redirect('admincal/showcal/week/'.$this->actualWeek);
                            break;
                    default: $this->_redirect('cal/showcal/week/'.$this->actualWeek);
            }
        }

        $request = $this->getRequest();
        $loginForm = $this->getLoginForm();

        if($request->isPost())
        {
            $validLogin = $this->model->login($loginForm, $request);

            if($validLogin == true){
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $userInfo = $authStorage->read();

                switch($userInfo->usertype){
                    case "20": $this->_redirect('admincal/showcal/week/'.$this->actualWeek);
                            break;
                    default: $this->_redirect('cal/showcal/week/'.$this->actualWeek);
                }
            }else{
                $this->errorMessage = "Wrong username or password provided. Please try again.";
            }
        }

//        $tscform = new Zend_Dojo_Form();
//        $tscform->addElement('ComboBox', 'usernameAutocomplete', array(
//        'label'         => 'Username:',
//        'storeId'       => 'testStore',
//        'storeType' => 'dojox.data.QueryReadStore',
//        'storeParams' => array(
//        'url'           => '/index/autocomplete',
//        'requestMethod' => 'get'),
//        'dojoType'      => 'dijit.form.ComboBox',
//        'autoComplete'   => 'false',
//        'hasDownArrow'   => 'true'
//        ));

        $this->view->errorMessage = $this->errorMessage;
        $this->view->loginForm = $loginForm;
       // $this->view->DojoForm = $tscform;
	}

    /**
     * Logout
     */
    public function logoutAction()
    {
        // clear everything - session is cleared also!
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/index/index');
    }

    /**
     * Create the LoginForm in the View
     * @return object $loginForm
     */
    protected function getLoginForm()
    {
        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username:')
                 ->setFilters(array('StringTrim','StringToLower'))
                 ->setRequired(true);

        $password = new Zend_Form_Element_Password('userpass');
        $password->setLabel('Password:')
                 ->setRequired(true);

        $submit = new Zend_Form_Element_Submit('login');
        $submit->setLabel('Login')
               ->setRequired(false)
               ->setIgnore(true);

        $loginForm = new Zend_Form();
        $loginForm->setAction('/index/index')
                ->setMethod('post')
                ->addElement($username)
                ->addElement($password)
                ->addElement($submit);

        return $loginForm;
    }


    public function autocompleteAction(){

        // Then get some request parameters; here we use the GET params
        $getName = $this->getRequest()->getParam('name');

        // Fetch results from the model; again, merely illustrative
        $results = $this->model->getUsernames($getName);

        $dojoData = new Zend_Dojo_Data('ID',$results);

        $this->view->data = $dojoData;
    }

}

?>