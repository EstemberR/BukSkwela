<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\Schema;

trait HasTenantConnection
{
    /**
     * Override the hasMany relationship to ensure proper database connection
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);
        $instance->setConnection($this->getConnectionName());
        
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();
        
        return new HasMany(
            $instance->newQuery(), 
            $this, 
            $instance->getTable().'.'.$foreignKey, 
            $localKey
        );
    }
    
    /**
     * Override the belongsTo relationship to ensure proper database connection
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        $instance = $this->newRelatedInstance($related);
        $instance->setConnection($this->getConnectionName());
        
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }
        
        $foreignKey = $foreignKey ?: $relation.'_'.$instance->getKeyName();
        $ownerKey = $ownerKey ?: $instance->getKeyName();
        
        return new BelongsTo(
            $instance->newQuery(), 
            $this, 
            $foreignKey, 
            $ownerKey, 
            $relation
        );
    }
    
    /**
     * Override the belongsToMany relationship to ensure proper database connection
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null)
    {
        $instance = $this->newRelatedInstance($related);
        $instance->setConnection($this->getConnectionName());
        
        $relation = $relation ?: $this->guessBelongsToManyRelation();
        
        if (is_null($table)) {
            $table = $this->joiningTable($related, $instance);
        }
        
        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();
        
        $parentKey = $parentKey ?: $this->getKeyName();
        $relatedKey = $relatedKey ?: $instance->getKeyName();
        
        $relation = new BelongsToMany(
            $instance->newQuery(), 
            $this, 
            $table, 
            $foreignPivotKey, 
            $relatedPivotKey, 
            $parentKey, 
            $relatedKey, 
            $relation
        );
        
        $relation->getQuery()->getQuery()->from($this->getConnection()->getTablePrefix() . $table);
        
        return $relation;
    }
    
    /**
     * Ensure the connection is set on all related models
     */
    public function setConnection($name)
    {
        parent::setConnection($name);
        $this->connection = $name;
        return $this;
    }

    /**
     * Override the boot method to conditionally add tenant_id only if it exists in the table
     */
    protected static function bootHasTenantConnection()
    {
        static::creating(function ($model) {
            if (tenant()) {
                try {
                    if (Schema::connection($model->getConnectionName())->hasColumn($model->getTable(), 'tenant_id')) {
                        $model->tenant_id = tenant('id');
                    }
                } catch (\Exception $e) {
                    // Just skip setting tenant_id if there's an error checking the schema
                }
            }
        });
    }
}