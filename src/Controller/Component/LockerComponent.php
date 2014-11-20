<?php
namespace Locker\Controller\Component;
use Cake\Controller\Component\AuthComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

class LockerComponent  extends AuthComponent{

    public $group = 'admin';
    public $loginAction = ['plugin'=>false,'prefix'=>false,'controller'=>'users','action'=>'login'];

    public function __construct(ComponentRegistry $registry, array $config = []){
        parent::__construct($registry, $config);
        $this->config('loginAction',$this->loginAction);
        $this->allow();
        if(!file_exists(CONFIG."locker.php")) return;
        Configure::load('locker');
        $groups = Configure::read('Locker.groups');
        $sectors = Configure::read('Locker.sectors');

        $url = $this->_getURL();
        $wdc = $this->_getWildcarded();

        if(empty($sectors[$wdc]) && empty($sectors[$url])) return;
        if(!$this->user()){
            $this->deny();
            return;
        }

        if($this->user()){
            $group = $this->user('group');
            if(!empty($sectors[$url]) && in_array($group,$sectors[$url])) return;
            if(!empty($sectors[$wdc]) && in_array($group,$sectors[$wdc])) return;
        }
        throw new NotFoundException(__('Você não tem permissão para acessar esta área'));
        exit;
    }

    private function _getURL(){
        $params = $this->request->params;
        unset($params['pass']);
        $url = Router::url($params + $this->request->param('pass'));
        $url = str_replace($this->request->base,'',$url);
        if(empty($url)) $url = '/';
        return $url;
    }

    private function _getWildcarded(){
        $params = $this->request->params;
        unset($params['pass']);
        $route = urldecode(Router::url($params + ['*']));
        $route = strtr($route,[$this->request->base=>'']);
        return $route;
    }

}