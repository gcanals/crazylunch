<?php
namespace crazylunch\models;
use \Illuminate\Database\Eloquent\Model;
/**
 * Plat model, extends the Eloquent Model
 * table : plats
 * PK : id
 *
 * belongsTo : resto()
 *
 * @author canals
 */
class Plat extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'plats';
    protected $primaryKey = 'id';
    public $timestamps=false;
    
    public function resto() {
        return $this->belongsTo('\crazylunch\models\Resto', 'id_resto');
    }
}

