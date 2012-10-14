<?php
class DashboardsController extends AppController {

	var $name = 'Dashboards';


	function index() {
            $db = $this->Dashboard->find('first', array(
                'conditions' => array(
                    'user_id' => $this->Auth->user('id')
                )
            ));
            if(empty($db)){
                $this->request->data['Dashboard']['name'] = 'New Dashboard';
                $this->add();
            }else{
                $this->redirect(array('action' => 'view', $db['Dashboard']['id']));
            }
	}

	function view($id = null) {
		if (!$id) {
                    $this->Session->setFlash(__('Invalid dashboard', true));
                    $this->redirect(array('action' => 'index'));
		}
        $dashboard = $this->Dashboard->read(null, $id);
        if(!empty($dashboard) && $dashboard['Dashboard']['user_id'] === $this->Auth->user('id')){
        	// Get more parameters
        	$replacements = array( 'search'=>array() , 'replace'=>array() );
        	$argsnum = func_num_args();
        	if($argsnum >= 2){
        		$cur = 1;
        		$arguments = func_get_args();
        		while($cur+1 < $argsnum){
        			$replacements['search'] = '&('.$arguments[$cur].')';
        			++$cur;
        			$replacements['replace'] = $arguments[$cur];
        			++$cur;
        		}
        	}
        	$this->set('replacements',$replacements);
            $this->set('dashboard_id', $id);
            $this->set('dashboard', $dashboard);
        }else{
        	$this->Session->setFlash('Invalid dashboard'.print_r($dashboard,true)." for user ".$this->Auth->user('id'));
            $this->redirect(array('action' => 'index'));
        }
	}

	function add() {
            if(!empty($this->data)){
            	if($this->Auth->user('id') == 0) {
            		$this->Session->setFlash(__('Can not create dash for no user. UserId: '.$this->Auth->user('id'), true));
            		return;
            	}
            	CakeLog::write('debug', 'Dashboard for user '.$this->Auth->user('id'));
            	$this->request->data['Dashboard']['user_id'] = $this->Auth->user('id');
                $this->Dashboard->create();
                
            	//CakeLog::write('debug', 'Dashboard: '.print_r($this->Dashboard,true));
                if ($this->Dashboard->save($this->data)) {
                    $this->Session->setFlash(__('The dashboard has been saved', true));
                    $this->redirect(array('action' => 'view', $this->Dashboard->getLastInsertId()));
                } else {
                    $this->Session->setFlash(__('The dashboard could not be saved. Please, try again.', true));
                }
            }
		
	}

        function edit($id = null) {
                if($id && $this->Dashboard->belongsToUser($id, $this->Auth->user('id'))){
                    if (!empty($this->data)) {
                        if ($this->Dashboard->save($this->data)) {
                            $this->Session->setFlash(__('The dashboard has been saved', true));
                            $this->redirect($this->referer());
                        } else {
                                $this->Session->setFlash(__('The dashboard could not be saved. Please, try again.', true));
                        }
                    }
                    if (empty($this->data)) {
                        $this->data = $this->Dashboard->read(null, $id);
                    }
                }else{
                    $this->Session->setFlash(__('Invalid dashboard', true));
                    $this->redirect(array('action' => 'index'));
                }
	}


	function delete($id = null) {
		if($id && $this->Dashboard->belongsToUser($id, $this->Auth->user('id'))){
                    if ($this->Dashboard->delete($id)) {
                            $this->Session->setFlash(__('Dashboard deleted', true));
                            $this->redirect(array('action'=>'index'));
                    }
                    $this->Session->setFlash(__('Dashboard was not deleted', true));
                    $this->redirect(array('action' => 'index'));
                }else{
                    $this->Session->setFlash(__('Invalid dashboard', true));
                    $this->redirect(array('action' => 'index'));
                }
	}
}
