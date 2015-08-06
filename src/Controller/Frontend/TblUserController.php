<?php

namespace App\Controller\Frontend;

use Cake\Core\Configure;
use lib\SendMail;

/**
 * TblUser Controller
 *
 * @property \App\Model\Table\TblUserTable $TblUser
 */
class TblUserController extends AppController
{

    public $sendmail;

    public function initialize()
    {
        parent::initialize();
        $this->sendmail = new SendMail();
        $this->loadModel('TblUser');
        $this->Auth->allow(['login', 'add', 'loginHome', 'resetPassword', 'forgetPassword', 'activeUser']);
    }

    /**
     * login user at page home
     */
    public function loginHome()
    {
        if ($this->request->is('ajax')) {
            $user = $this->Auth->identify();
            $response = array();
            if ($user && $user['is_active'] == 1) {
                $this->Auth->setUser($user);
                $response['code'] = 1;
            } else if ($user && $user['is_active'] != 1) {
                $response['code'] = 2;
                $response['message'] = Configure::read('message.error_active');
            } else {
                $response['code'] = 2;
                $response['message'] = Configure::read('message.error_login');
            }
            echo json_encode($response);
            die();
        }
    }

    /**
     * login at page login
     * @return type
     */
    public function login()
    {        
        if ($this->request->is("post")) {
            $user = $this->Auth->identify();
            if ($user && $user['is_active'] == 1) {
                $this->Auth->setUser($user);
                return $this->redirect(['prefix'=>'manage','controller' => 'Home', 'action' => 'index']);
            } else if ($user && $user['is_active'] != 1) {
                $this->Flash->error(__(Configure::read('message.error_active')));
            } else {
                $this->Flash->error(__(Configure::read('message.error_login')));
            }
        }
    }

    /**
     * active user 
     */
    public function activeUser()
    {
        $token = $this->request->token;
        $key = Configure::read('key.encrypt');
        $data = $this->TblUser->find()->where(["sha1(id$key)" => $token])->first();
        if (!$data) {
            $this->redirect('/');
        }
        $data->is_active = 1;
        $this->TblUser->updateEntity($this->TblUser, $data);
        $this->redirect('/login');
    }

    /**
     * logout user
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * for get password
     */
    public function forgetPassword()
    {

        if ($this->request->is("post")) {
            $emailuser = $this->request->data('email');
            $user = $this->TblUser->getAccount($this->TblUser, $emailuser);
            if ($user) {
                $this->sendmail->send($user['id'], $emailuser, 'resetpassword', Configure::read('mail.header_reset'), Configure::read('mail.body_reset'));
                $this->set('email', $emailuser);
                $this->render('after_forget_password');
            } else {
                $this->Flash->error(__(Configure::read('message.account_not_exist')));
            }
        }
    }

    /**
     * reset password
     */
    public function resetPassword()
    {
        $token = $this->request->token;
        $key = Configure::read('key.encrypt');
        $data = $this->TblUser->find()->where(["sha1(id$key)" => $token])->first();
        if (!$data) {
            $this->redirect('/');
        }
        $errors = array();
        if ($this->request->is("post")) {
            $validator = $this->TblUser->validatorResetPassword();
            $errors = $validator->errors($this->request->data);
            if (empty($errors)) {
                $data->password = $this->request->data('password');
                $this->TblUser->updateEntity($this->TblUser, $data);
                $this->redirect('/login');
            }
        }
        $this->set('errors', $errors);
    }

    /**
     * Register Usẻ
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tblUser = $this->TblUser->newEntity();
        if ($this->request->is('post')) {
            $tblUser = $this->TblUser->patchEntity($tblUser, $this->request->data);
            if ($this->TblUser->save($tblUser)) {
                $this->sendmail->send($tblUser->id, $tblUser->email, 'active', Configure::read('mail.header_active'), Configure::read('mail.body_active'));
                $this->Flash->success(__(Configure::read('message.register_success')), ['key' => 'register']);
                return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
            } else {
                $this->Flash->error(__(Configure::read('message.register_fail')));
            }
        }
        $this->set(compact('tblUser'));
        $this->set('_serialize', ['tblUser']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Tbl User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tblUser = $this->TblUser->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tblUser = $this->TblUser->patchEntity($tblUser, $this->request->data);
            if ($this->TblUser->save($tblUser)) {
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The tbl user could not be saved. Please, try again.'));
            }
        }
        $lastWallets = $this->TblUser->LastWallets->find('list', ['limit' => 200]);
        $this->set(compact('tblUser', 'lastWallets'));
        $this->set('_serialize', ['tblUser']);
    }

}