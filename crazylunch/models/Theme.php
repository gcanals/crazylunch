<?php
namespace crazylunch\models;
use \Illuminate\Database\Eloquent\Model;
/**
 *
 * Theme model, extends the Eloquent Model
 * table : theme
 * PK : id
 *
 * hasMany  : restos()
 *
 * @author canals
 */
class Theme extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'theme';
    protected $primaryKey = 'id';
    public $timestamps=false;
    
    public function restos() {
        return $this->hasMany('\crazylunch\models\Resto', 'id_theme');
    }
}

