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
        $servicePath = app_path("Services/{$name}Service.php");

        if (!file_exists($servicePath)) {
            $serviceTemplate = "
<?php

namespace App\Services;

use App\Models\\$name;

class {$name}Service
{
    public function getAll()
    {
        return $name::all();
    }

    public function show($name \$model)
    {
        return \$model;
    }

    public function store(array \$data)
    {
        return $name::create(\$data);
    }

    public function update(array \$data, $name \$model)
    {
        \$model->update(\$data);
        return \$model;
    }

    public function delete($name \$model)
    {
        \$model->delete();
    }

    public function copy($name \$model)
    {
        \$newModel = \$model->replicate();
        \$newModel->save();
        return \$newModel;
    }

    public function checkConnection($name \$model)
    {
        // Logic for checking the connection, if any
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
            $controllerTemplate = "
<?php

namespace App\Http\Controllers;

use App\Http\Requests\\{$name}Request;
use App\Models\\$name;
use App\Services\\{$name}Service;
use Illuminate\Http\JsonResponse;

class {$name}Controller extends Controller
{
    public function __construct(private {$name}Service \$service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(\$this->service->getAll());
    }

    public function show($name \$model): JsonResponse
    {
        return response()->json([
            'item' => \$this->service->show(\$model),
        ]);
    }

    public function store({$name}Request \$request): JsonResponse
    {
        \$model = \$this->service->store(\$request->validated());

        return response()->json([
            'item' => \$model,
        ]);
    }

    public function update({$name}Request \$request, $name \$model): JsonResponse
    {
        \$this->service->update(\$request->validated(), \$model);

        return response()->json([
            'item' => \$model,
        ]);
    }

    public function destroy($name \$model): JsonResponse
    {
        \$this->service->delete(\$model);
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
