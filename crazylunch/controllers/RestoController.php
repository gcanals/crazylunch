<?php
namespace crazylunch\controllers;
use \crazylunch\models\Resto;
/**
 * Description of ThemeController
 *
 * @author canals
 */
class RestoController extends RestController {
    
    
    
    public function __construct(\Slim\Slim $app) {
        parent::__construct($app);
        
        $this->resource = 'resto';
       
    }
  
  
    
}

