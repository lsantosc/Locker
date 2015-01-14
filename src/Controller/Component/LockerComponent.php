<?php
namespace Locker\Controller\Component;

use Cake\Controller\Component\AuthComponent;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;

class LockerComponent extends AuthComponent
{

    public $role = 'public';
    public $roles;
    public $controllers;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        if($this->user('role')) $this->role = $this->user('role');
        if (!file_exists(CONFIG . "locker.php")) throw new Exception(_('locker.php not found in config directory'));

        //Load configuration directives for Locker
        $params = $this->request->params;
        Configure::load('locker');
        $this->roles = Configure::read('locker.roles');
        $this->controllers = Configure::read('locker.controllers');

        $path = "/{$params['controller']}/{$params['action']}";
        if(!empty($params['plugin'])) $path = "/{$params['plugin']}".$path;

        $base = strtolower($path);
        $exact = strtolower($path . '/' . implode('/', $params['pass']));
        $wildcard = strtolower($base . '/*');

        if($this->role != 'public' && !in_array($this->role,$this->roles)) {
            throw new Exception(__('Your user role is not present in locker configuration'));
        }

        if(!empty($this->controllers[$exact])){
            if($this->check($exact)) return $this->allow();
            if($this->user()) throw new MethodNotAllowedException(sprintf(__("You do not have permission to access this area: %s"),$exact));
            return;
        }

        if(!empty($this->controllers[$wildcard]) && !empty($params['pass'])){
            if($this->check($wildcard)) return $this->allow();
            if($this->user()) throw new MethodNotAllowedException(sprintf(__("You do not have permission to access this area: %s"),$wildcard));
            return;
        }

        if(!empty($this->controllers[$base])){
            if($this->check($base)) return $this->allow();
            if($this->user()) throw new MethodNotAllowedException(sprintf(__("You do not have permission to access this area: %s"),$base));
            return;
        }

        throw new Exception(__('Method is not present on locker.php configuration'));
    }

    protected function check($path){
        return (
            in_array($this->role,$this->controllers[$path])
            || in_array('public',$this->controllers[$path])
        );
    }
}