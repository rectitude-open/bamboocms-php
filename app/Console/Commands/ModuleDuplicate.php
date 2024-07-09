<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleDuplicate extends Command
{
    private string $fromModuleName;

    private string $fromRoleName;

    private string $fromModelName;

    private string $toModuleName;

    private string $toRoleName;

    private string $toModelName;

    protected $signature = 'module:duplicate {from} {to}';

    protected $description = 'Duplicate module files and directories';

    public function handle(): void
    {
        $from = $this->argument('from');
        $to = $this->argument('to');

        $fromArray = explode('/', $from);
        $toArray = explode('/', $to);

        if (count($fromArray) !== 3 || count($toArray) !== 3) {
            $this->error('Invalid argument format.');
            $this->error('Please use the following format:');
            $this->error('php artisan module:duplicate {fromModuleName}/{fromRoleName}/{fromModelName} {toModuleName}/{toRoleName}/{toModelName}');
            $this->error('Please use PascalCase for module name, role name and model name.');

            return;
        }

        $this->fromModuleName = $fromArray[0] ?? null;
        $this->fromRoleName = $fromArray[1] ?? null;
        $this->fromModelName = $fromArray[2] ?? null;

        $this->toModuleName = $toArray[0] ?? null;
        $this->toRoleName = $toArray[1] ?? null;
        $this->toModelName = $toArray[2] ?? null;

        $this->process();
    }

    private function process(): void
    {
        $fromModuleName = $this->fromModuleName;
        $fromRoleName = $this->fromRoleName;
        $fromModelName = $this->fromModelName;
        $toModuleName = $this->toModuleName;
        $toRoleName = $this->toRoleName;
        $toModelName = $this->toModelName;

        $paths = [
            [
                "modules/{$fromModuleName}/Application/{$fromRoleName}/AppServices/{$fromModelName}AppService.php",
                "modules/{$toModuleName}/Application/{$toRoleName}/AppServices/{$toModelName}AppService.php",
            ],
            [
                "modules/{$fromModuleName}/Application/{$fromRoleName}/Controllers/{$fromModelName}Controller.php",
                "modules/{$toModuleName}/Application/{$toRoleName}/Controllers/{$toModelName}Controller.php",
            ],
            [
                "modules/{$fromModuleName}/Application/{$fromRoleName}/Resources/{$fromModelName}Resource.php",
                "modules/{$toModuleName}/Application/{$toRoleName}/Resources/{$toModelName}Resource.php",
            ],
            [
                "modules/{$fromModuleName}/Domain/Models/{$fromModelName}.php",
                "modules/{$toModuleName}/Domain/Models/{$toModelName}.php",
            ],
            [
                "modules/{$fromModuleName}/Infrastructure/Factories/{$fromModelName}Factory.php",
                "modules/{$toModuleName}/Infrastructure/Factories/{$toModelName}Factory.php",
            ],
            [
                "modules/{$fromModuleName}/Infrastructure/Repositories/{$fromModelName}Repository.php",
                "modules/{$toModuleName}/Infrastructure/Repositories/{$toModelName}Repository.php",
            ],
            [
                "modules/{$fromModuleName}/Infrastructure/Scopes/{$fromModelName}SearchScope.php",
                "modules/{$toModuleName}/Infrastructure/Scopes/{$toModelName}SearchScope.php",
            ],
            [
                "modules/{$fromModuleName}/Tests/{$fromRoleName}/Feature/{$fromModelName}Test.php",
                "modules/{$toModuleName}/Tests/{$toRoleName}/Feature/{$toModelName}Test.php",
            ],
            [
                "modules/{$fromModuleName}/Tests/{$fromRoleName}/Feature/{$fromModelName}Dataset.php",
                "modules/{$toModuleName}/Tests/{$toRoleName}/Feature/{$toModelName}Dataset.php",
            ],
            [
                "modules/{$fromModuleName}/Application/{$fromRoleName}/Requests/{$fromModelName}/",
                "modules/{$toModuleName}/Application/{$toRoleName}/Requests/{$toModelName}/",
            ],
        ];

        foreach ($paths as $path) {
            $fromPath = $path[0] ? base_path($path[0]) : null;
            $toPath = $path[1] ? base_path($path[1]) : null;

            if ($fromPath) {
                if (File::isDirectory($fromPath)) {
                    $this->processDirectoryCopy($fromPath, $toPath);
                } else {
                    $this->processFileCopy($fromPath, $toPath);
                }
                $this->replaceContent($fromPath, $toPath);
            }
        }

        $this->info('Module duplicated successfully');
    }

    private function replaceContent(string $fromPath, string $toPath): void
    {
        if (File::isDirectory($fromPath)) {
            $files = File::allFiles($toPath);
            foreach ($files as $file) {
                $this->replaceFileContent($file);
            }
        } else {
            $this->replaceFileContent(new \SplFileInfo($toPath));
        }
    }

    private function replaceFileContent(\SplFileInfo $file): void
    {
        if (! $file->isFile()) {
            return;
        }

        $filePath = $file->getPathname();
        $content = File::get($filePath);
        $content = Str::swap(
            [
                "namespace Modules\\{$this->fromModuleName}\\Application\\{$this->fromRoleName}" => "namespace Modules\\{$this->toModuleName}\\Application\\{$this->toRoleName}",
                "namespace Modules\\{$this->fromModuleName}\\Domain" => "namespace Modules\\{$this->toModuleName}\\Domain",
                "namespace Modules\\{$this->fromModuleName}\\Infrastructure" => "namespace Modules\\{$this->toModuleName}\\Infrastructure",
                "use Modules\\{$this->fromModuleName}\\" => "use Modules\\{$this->toModuleName}\\",
                "{$this->fromModuleName}.{$this->fromRoleName}.{$this->fromModelName}.store" => "{$this->toModuleName}.{$this->toRoleName}.{$this->toModelName}.store",
                Str::plural($this->fromModelName) => Str::plural($this->toModelName),
                $this->fromModelName => $this->toModelName,
                Str::plural(Str::lcfirst($this->fromModelName)) => Str::plural(Str::lcfirst($this->toModelName)),
                Str::lcfirst($this->fromModelName) => Str::lcfirst($this->toModelName),
                Str::plural(Str::kebab($this->fromModelName)) => Str::plural(Str::kebab($this->toModelName)),
                Str::kebab($this->fromModelName) => Str::kebab($this->toModelName),
                Str::plural(Str::snake($this->fromModelName)) => Str::plural(Str::snake($this->toModelName)),
                Str::snake($this->fromModelName) => Str::snake($this->toModelName),
            ],
            $content,
        );

        File::put($filePath, $content);
    }

    private function processDirectoryCopy(string $fromDirectory, string $toDirectory)
    {
        if (file_exists($fromDirectory)) {
            File::copyDirectory($fromDirectory, $toDirectory);
        }
    }

    private function processFileCopy(string $fromFile, string $toFile)
    {
        if (File::exists($fromFile)) {
            $this->makeFileDirectory($toFile);
            File::copy($fromFile, $toFile);
        }
    }

    private function makeFileDirectory(string $file)
    {
        $directory = dirname($file);
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }
}
