<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModule extends Command
{
    protected $signature = 'make:module {name}';

    protected $description = 'Create a model, migration, request, service, and controller for a new module';

    public function handle(): void
    {
        $name = $this->argument('name');
        $this->info("Creating module: $name");

        $this->call('make:model', [
            'name' => $name,
            '--migration' => true,
            '--controller' => true,
        ]);

        $this->call('make:request', [
            'name' => "{$name}Request"
        ]);

        $this->createService($name);
        $this->updateController($name);

        $this->info('Module created successfully!');
    }

    protected function createService($name): void
    {
        $variableName = lcfirst($name);
        $servicePath = app_path("Services/{$name}Service.php");

        if (!file_exists($servicePath)) {
            $serviceTemplate = "<?php

namespace App\Services;

use App\Models\\$name;

class {$name}Service
{
    public function show($name \$$variableName): $name
    {
        return \$$variableName;
    }

    public function store(array \$data): $name
    {
        return $name::create(\$data);
    }

    public function update(array \$data, $name \$$variableName): void
    {
        \${$variableName}->update(\$data);
    }
}
";
            File::put($servicePath, $serviceTemplate);
            $this->info("Service created: {$servicePath}");
        } else {
            $this->info("Service already exists: {$servicePath}");
        }
    }

    protected function updateController(string $name): void
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");

        if (file_exists($controllerPath)) {
            $variableName = lcfirst($name);
            $controllerTemplate = "<?php

namespace App\Http\Controllers;

use App\Http\Requests\\{$name}Request;
use App\Models\\$name;
use App\Services\\{$name}Service;
use Illuminate\Http\JsonResponse;

class {$name}Controller extends Controller
{
    public function __construct(private {$name}Service \${$name}service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(\$this->{$name}service->getAll());
    }

    public function show($name \${$variableName}): JsonResponse
    {
        return response()->json([
            'item' => \$this->{$name}service->show(\${$variableName}),
        ]);
    }

    public function store({$name}Request \$request): JsonResponse
    {
        \${$variableName} = \$this->{$name}service->store(\$request->validated());

        return response()->json([
            'item' => \${$variableName},
        ]);
    }

    public function update({$name}Request \$request, $name \${$variableName}): JsonResponse
    {
        \$this->{$name}service->update(\$request->validated(), \${$variableName});

        return response()->json([
            'item' => \${$variableName},
        ]);
    }

    public function destroy($name \${$variableName}): JsonResponse
    {
        \${$variableName}->delete();
        return response()->json([], 204);
    }
}";
            File::put($controllerPath, $controllerTemplate);
            $this->info("Controller updated: {$controllerPath}");
        } else {
            $this->error("Controller does not exist: {$controllerPath}");
        }
    }
}
