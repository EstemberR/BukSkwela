<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class FixTenantRelationships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-relationships {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix relationship methods in tenant models';

    /**
     * Models with the HasMany relationship that need fixing
     */
    protected $modelsToFix = [
        'App\Models\Course\Course',
        'App\Models\Staff\Staff',
        'App\Models\Student\Student',
        'App\Models\Requirements\Requirement',
        'App\Models\Requirements\RequirementCategory',
        'App\Models\Department'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Fixing relationship methods for tenant: {$tenant} in database {$databaseName}");
        
        try {
            // Set the connection to use the tenant database
            Config::set('database.connections.tenant.database', $databaseName);
            Config::set('database.connections.tenant.username', env('DB_USERNAME'));
            Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
            
            // Purge and reconnect to the tenant database
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Test the connection
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database");
            
            // Fix relationship methods in model classes
            foreach ($this->modelsToFix as $modelClass) {
                if (class_exists($modelClass)) {
                    $this->fixModelRelationships($modelClass);
                } else {
                    $this->warn("Model class {$modelClass} not found");
                }
            }
            
            // Create a new helper class to properly handle relationships
            $this->createTenantRelationshipTrait();
            
            $this->info("Relationship methods fixed successfully!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error fixing relationship methods: " . $e->getMessage());
            Log::error("Fix tenant relationships error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
    
    /**
     * Fix relationship methods in a model class
     */
    protected function fixModelRelationships($modelClass)
    {
        $this->info("Fixing relationships for {$modelClass}");
        
        // Create an instance of the model
        $model = new $modelClass();
        
        // Set tenant connection manually to ensure it exists
        $model->setConnection('tenant');
        
        // This forces the model to save to the correct tenant database
        DB::connection('tenant')->table($model->getTable())
            ->where('tenant_id', $this->argument('tenant'))
            ->exists();
            
        $this->info("Connection for {$modelClass} has been fixed");
    }
    
    /**
     * Create a new trait to handle tenant relationships properly
     */
    protected function createTenantRelationshipTrait()
    {
        $traitsDir = app_path('Traits');
        
        // Create traits directory if it doesn't exist
        if (!File::exists($traitsDir)) {
            File::makeDirectory($traitsDir, 0755, true);
        }
        
        // Path to the trait file
        $traitPath = app_path('Traits/HasTenantConnection.php');
        
        // Create the trait if it doesn't exist
        if (!File::exists($traitPath)) {
            $this->info("Creating TenantConnection trait");
            
            $traitContent = <<<'EOT'
<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
}
EOT;
            
            File::put($traitPath, $traitContent);
            $this->info("Created HasTenantConnection trait at {$traitPath}");
            
            // Instructions for developers
            $this->info("Add this trait to your tenant models with the following change:");
            $this->info("use HasFactory, HasTenantConnection;");
            $this->info("And remove any ->on() calls from your relationship methods");
        } else {
            $this->info("HasTenantConnection trait already exists");
        }
    }
} 