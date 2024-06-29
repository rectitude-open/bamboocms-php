<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleRemove extends Command
{
    private string $moduleName;

    private string $roleName;

    private string $modelName;

    protected $signature = 'module:remove {module}';

    protected $description = 'Remove module files and directories';

    public function handle(): void
    {
        $module = $this->argument('module');

        $moduleArray = explode('/', $module);

        if (count($moduleArray) !== 3) {
            $this->error('Invalid argument format.');
            $this->error('Please use the following format:');
            $this->error('php artisan module:remove {moduleName}/{roleName}/{modelName}');
            $this->error('Please use PascalCase for module name, role name, and model name.');

            return;
        }

        $this->moduleName = $moduleArray[0] ?? null;
        $this->roleName = $moduleArray[1] ?? null;
        $this->modelName = $moduleArray[2] ?? null;

        $this->process();
    }

    private function process(): void
    {
        $moduleName = $this->moduleName;
        $roleName = $this->roleName;
        $modelName = $this->modelName;

        $paths = [
            "modules/{$moduleName}/Application/{$roleName}/AppServices/{$modelName}AppService.php",
            "modules/{$moduleName}/Application/{$roleName}/Controllers/{$modelName}Controller.php",
            "modules/{$moduleName}/Application/{$roleName}/Resources/{$modelName}Resource.php",
            "modules/{$moduleName}/Domain/Models/{$modelName}.php",
            "modules/{$moduleName}/Infrastructure/Factories/{$modelName}Factory.php",
            "modules/{$moduleName}/Infrastructure/Repositories/{$modelName}Repository.php",
            "modules/{$moduleName}/Tests/Feature/{$modelName}Test.php",
            "modules/{$moduleName}/Tests/Feature/{$modelName}Dataset.php",
            "modules/{$moduleName}/Application/{$roleName}/Requests/{$modelName}/",
        ];

        foreach ($paths as $path) {
            $fullPath = base_path($path);

            if (File::isDirectory($fullPath)) {
                $this->deleteDirectory($fullPath);
            } else {
                $this->deleteFile($fullPath);
            }
        }

        $this->info('Module removed successfully');
    }

    private function deleteDirectory(string $directory): void
    {
        if (file_exists($directory)) {
            File::deleteDirectory($directory);
        }
    }

    private function deleteFile(string $file): void
    {
        if (File::exists($file)) {
            File::delete($file);
        }
    }
}
