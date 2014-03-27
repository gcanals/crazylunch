<?php
namespace crazylunch\controllers;
use \crazylunch\models\Resto;
/**
 * Description of ThemeController
 *
 * @author canals
 */
class PlatController extends RestController {
    
    
    
    public function __construct(\Slim\Slim $app) {
        parent::__construct($app);
        
        $this->resource = 'plat';
    }
  
  
    
}

