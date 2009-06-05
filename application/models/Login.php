<?php
/**
 * loginModel.php
 *
 * @author Thorsten Scheckenbach <t.scheckenbach@creativation.de>
 */

/**
 * loginModel
 * @author  Thorsten Scheckenbach <t.scheckenbach@creativation.de>
 */
class Model_Login extends Zend_Db_Table
{   
    /**
	 * Name of database table to use
	 * @var string
	 */
    protected $_name = 'user';

    /**
	 * Salt for auth
	 * @var string
	 */
    protected $salt = '';
    
    
    /**
     *
     * @param object $loginForm
     * @param object $request
     * @return bool
     */
    public function login($loginForm, $request)
    {
        
        if($loginForm->isValid($request->getPost()))
        {
            // get the username and password from the form
            $username = $loginForm->getValue('username');
            $password = $loginForm->getValue('userpass');

            // get the salt
            $salt = $this->getSalt($username);

            $dbAdapter = $this->getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName('user')
                        ->setIdentityColumn('username')
                        ->setCredentialColumn('userpass')
                        ->setCredentialTreatment('ENCRYPT(?,"'.$salt.'")');

            // pass to the adapter the submitted username and password
            $authAdapter->setIdentity($username)
                        ->setCredential($password);

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);

            // is the user a valid one?
            if($result->isValid())
            {
                // get all info about this user from the login table
                // ommit only the password, we don't need that
                $userInfo = $authAdapter->getResultRowObject(null, 'userpass');

                // the default storage is a session with namespace Zend_Auth
                $authStorage = $auth->getStorage();
                $authStorage->write($userInfo);
                
                return true;
            }
        }
        return false;
    }

    /**
	 * Get salt für auth
     * 
	 * @return string $salt
	 */
    protected function getSalt($username)
    {
        $result = $this->fetchRow('username = "'.$username.'"');
        $salt = substr($result->userpass,0,2);
        return $salt;
    }

    public function getUsernames($name)
    {
        $select = $this->select()
                       ->from('user',array('ID', 'name' =>'username'))
                       ->where('username LIKE "'.$name.'%"');
        $rowset = $this->fetchAll($select);
        return $rowset;
    }
}
?>
