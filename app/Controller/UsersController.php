<?php
class UsersController extends AppController {

	var $name = 'Users';

	function  beforeFilter() {
		$this->Auth->allow('add');

		parent::beforeFilter();

	}

	function logout(){
		$this->redirect($this->Auth->logout());
	}

	function index() {
		$this->redirect(array('action' => 'view'));
	}

	function login(){
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
			}
		}
	}

	function view() {
		$id = $this->Auth->user('id');
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			//if($this->data['User']['password'] == $this->Auth->password($this->data['User']['password2'])) {
			if($this->data['User']['password'] == $this->data['User']['password2']) {
				$this->User->create();

				if ($this->User->save($this->data)) {
					$this->Session->setFlash(__('The user has been saved', true));
					$this->Auth->login($this->data);
					$this->redirect(array('controller' => 'dashboards','action' => 'index'));
				} else {
					$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
				}
			}else{
				$this->Session->setFlash('The provided passwords did not match. Please try again.');

			}
		}
		@$this->data['User']['password'] = '';
		@$this->data['User']['password2'] = '';

	}

	/*function edit() {
	 $id = $this->Auth->user('id');
		if (!$id && empty($this->data)) {
		$this->Session->setFlash(__('Invalid user', true));
		$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
		if ($this->User->save($this->data)) {
		$this->Session->setFlash(__('The user has been saved', true));
		$this->redirect(array('action' => 'index'));
		} else {
		$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
		}
		}
		if (empty($this->data)) {
		$this->data = $this->User->read(null, $id);
		}
		}*/

	function delete() {
		$id = $this->Auth->user('id');
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
