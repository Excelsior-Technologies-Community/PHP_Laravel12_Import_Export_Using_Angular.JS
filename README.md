# Step 1 : Install Laravel 12 and Create New Project 
Command (Terminal / CMD)
```php
composer create-project laravel/laravel PHP_Laravel12_Import_Export_Using_Angular.JS
```
# Step 2 : Set Database function for .env file
 ```php
 DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=your database name
 DB_USERNAME=root
 DB_PASSWORD=
```
### Now Create Simple IMPORT AND EXPORT function this method followed all step:-

# Step 3 :Create Migration File for database table 
Command
```php
php artisan make:migration create_crud_table --create=crud
```
Migration File
database/migrations/xxxx_xx_xx_create_crud_table.php
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crud', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crud');
    }
};
```
Run Migration
```php
php artisan migrate
```

# Step 4 :Create Model
Command
```php
php artisan make:model Crud
```
Model File
app/Models/Crud.php
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crud extends Model
{
    // 🔹 Table name (VERY IMPORTANT)
    protected $table = 'crud';

    // 🔹 Mass assignment allow
    protected $fillable = [
        'title',
        'description'
    ];
}
```
# Step 5 :Create Controller For IMPORT AND EXPORT METHOD
Command
```php
php artisan make:controller CrudController
```
Controller File
app/Http/Controllers/CrudController.php
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Crud;

class CrudController extends Controller
{
    // =============================
    // PAGES (BLADE)
    // =============================
    public function indexPage()
    {
        return view('angular.index');
    }

    public function createPage()
    {
        return view('angular.create');
    }

    public function editPage()
    {
        return view('angular.edit');
    }

    // =============================
    // DATA APIs
    // =============================
    public function list()
    {
        return Crud::orderBy('id', 'asc')->get();
    }

    public function store(Request $request)
    {
        Crud::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return ['status' => true];
    }

    public function show($id)
    {
        return Crud::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        Crud::findOrFail($id)->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return ['status' => true];
    }

    public function destroy($id)
    {
        Crud::findOrFail($id)->delete();
        return ['status' => true];
    }
// =============================
    // EXPORT CSV
    // =============================
    public function export()
    {
        $filename = "crud_export_" . date('Y-m-d_H-i-s') . ".csv";
        $records = Crud::orderBy('id', 'asc')->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Description']);

            foreach ($records as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->title,
                    $row->description,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =============================
    // IMPORT CSV
    // =============================
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $rowNumber = 0;
        $inserted = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {

            if ($rowNumber === 0) {
                $rowNumber++;
                continue;
            }

            if (!isset($row[1]) || !isset($row[2])) {
                continue;
            }

            Crud::create([
                'title' => trim($row[1]),
                'description' => trim($row[2]),
            ]);

            $inserted++;
        }

        fclose($handle);

        return response()->json([
            'status' => true,
            'inserted' => $inserted
        ]);

}
```
# Step 6 :Create Web route for routes/web.php fileinclude IMPORT AND EXPORT FUNCTION:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudController;

// =============================
// PAGES
// =============================
Route::get('/crud', [CrudController::class, 'indexPage']);
Route::get('/crud/create', [CrudController::class, 'createPage']);
Route::get('/crud/edit', [CrudController::class, 'editPage']);

// =============================
// CRUD API
// =============================
Route::get('/crud-list', [CrudController::class, 'list']);
Route::post('/crud-store', [CrudController::class, 'store']);
Route::get('/crud-show/{id}', [CrudController::class, 'show']);
Route::post('/crud-update/{id}', [CrudController::class, 'update']);
Route::get('/crud-delete/{id}', [CrudController::class, 'destroy']);

// =============================
// IMPORT / EXPORT
// =============================
Route::get('/crud-export', [CrudController::class, 'export']);
Route::post('/crud-import', [CrudController::class, 'import']);
```


# Step 7: AngularJS CDN (Blade / HTML)
# resources/views/welcome.blade.php
```php
<!DOCTYPE html>
<html lang="en" ng-app="crudApp">
<head>
    <meta charset="UTF-8">
    <title>PHP_Laravel12_Import_Export_Using_Angular.JS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind / App CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- AngularJS -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-4xl bg-white shadow-lg rounded-lg p-8">

    <!-- HEADER -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold mb-2">
            PHP_Laravel12_Import_Export_Using_Angular.JS
        </h1>

        <p class="text-gray-600">
            Laravel 12 + AngularJS CRUD Application<br>
            Manage <b>Title</b> & <b>Description</b>
        </p>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="flex justify-center gap-4 mb-10">
        <a href="/crud"
           class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            View Records
        </a>

        <a href="/crud/create"
           class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Create New
        </a>
    </div>

    <!-- ANGULAR VIEW -->
    <div ng-controller="CrudController">

        <table class="w-full border border-gray-300 text-sm">
            <thead class="bg-gray-200">
            <tr>
                <th class="border px-3 py-2">#</th>
                <th class="border px-3 py-2">Title</th>
                <th class="border px-3 py-2">Description</th>
                <th class="border px-3 py-2">Action</th>
            </tr>
            </thead>

            <tbody>
            <tr ng-repeat="item in items">
                <td class="border px-3 py-2">@{{$index + 1}}</td>
                <td class="border px-3 py-2">@{{item.title}}</td>
                <td class="border px-3 py-2">@{{item.description}}</td>
                <td class="border px-3 py-2 text-center">
                    <a href="/crud/edit/@{{item.id}}"
                       class="text-blue-600 hover:underline">
                        Edit
                    </a>
                </td>
            </tr>
            </tbody>
        </table>

    </div>

</div>

</body>
</html>
```
# Step 8: Create Angular blade file for resource/view/angular folder
# Resource/view/angular/index.blade.php with IMPORT AND EXPORT Button and file 
```php
<!DOCTYPE html>
<html lang="en" ng-app="crudApp">
<head>
    <meta charset="UTF-8">
    <title>CRUD List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ CSRF TOKEN -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AngularJS -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>

    <!-- Angular App -->
    <script src="/js/app.js"></script>
</head>

<body ng-controller="CrudController" ng-init="loadData()" class="bg-light">

<div class="container mt-5">

<!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">CRUD List</h3>

        <div class="d-flex align-items-center gap-2">

            <!-- IMPORT CSV -->
            <input type="file"
                   class="form-control form-control-sm"
                   style="width:200px"
                   file-model="importFile"
                   accept=".csv">

            <button class="btn btn-warning btn-sm" ng-click="importCSV()">
                Import CSV
            </button>

            <!-- EXPORT CSV -->
            <button class="btn btn-success btn-sm" ng-click="exportCSV()">
                Export CSV
            </button>

            <!-- CREATE -->
            <a href="/crud/create" class="btn btn-primary btn-sm">
                + Create New
            </a>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-dark">
                <tr>
                    <th width="10%">ID</th>
                    <th width="25%">Title</th>
                    <th>Description</th>
                    <th width="20%" class="text-center">Action</th>
                </tr>
                </thead>

                <tbody>
                <tr ng-repeat="row in records">
                    <td>@{{ row.id }}</td>
                    <td>@{{ row.title }}</td>
                    <td>@{{ row.description }}</td>
                    <td class="text-center">
                        <a href="/crud/edit?id=@{{ row.id }}" class="btn btn-sm btn-primary">
                            Edit
                        </a>
                        <button class="btn btn-sm btn-danger ms-2"
                                ng-click="remove(row.id)">
                            Delete
                        </button>
                    </td>
                </tr>

                <tr ng-if="records.length == 0">
                    <td colspan="4" class="text-center py-3">
                        No records found
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```
# Resource/view/angular/create.blade.php
```php
<!DOCTYPE html>
<html lang="en" ng-app="crudApp">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ CSRF TOKEN -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AngularJS -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>

    <!-- Angular App -->
    <script src="/js/app.js"></script>
</head>

<body ng-controller="CrudController" class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Create Record</h5>
                </div>

                <div class="card-body">
                    <form ng-submit="store()">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text"
                                   class="form-control"
                                   ng-model="form.title"
                                   placeholder="Enter title"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control"
                                      ng-model="form.description"
                                      placeholder="Enter description"
                                      rows="4"
                                      required></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">
                                Save
                            </button>

                            <a href="/crud" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```
# Resource/view/angular/edit.blade.php
```php
<!DOCTYPE html>
<html lang="en" ng-app="crudApp">
<head>
    <meta charset="UTF-8">
    <title>Edit Record</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ CSRF TOKEN -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AngularJS -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>

    <!-- Angular App -->
    <script src="/js/app.js"></script>
</head>

<body ng-controller="CrudController" ng-init="getSingle()" class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Record</h5>
                </div>

                <div class="card-body">
                    <form ng-submit="update()">

                        <!-- Hidden ID -->
                        <input type="hidden" ng-model="form.id">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text"
                                   class="form-control"
                                   ng-model="form.title"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control"
                                      ng-model="form.description"
                                      rows="4"
                                      required></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>

                            <a href="/crud" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```
# Step 9: Create app.js file into public/js folder
```php
(function () {

    var app = angular.module("crudApp", []);

    // ======================================
    // FILE MODEL DIRECTIVE
    // ======================================
    app.directive('fileModel', function ($parse) {
        return {
            restrict: 'A',
            link: function (scope, element, attrs) {
                var model = $parse(attrs.fileModel);
                element.on('change', function () {
                    scope.$apply(function () {
                        model.assign(scope, element[0].files[0]);
                    });
                });
            }
        };
    });

    // ======================================
    // CONTROLLER
    // ======================================
    app.controller("CrudController", function ($scope, $http) {

        $scope.records = [];
        $scope.form = {};
        $scope.importFile = null;

        // ======================================
        // CSRF TOKEN (SAFE)
        // ======================================
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

        // ======================================
        // LOAD LIST
        // ======================================
        $scope.loadData = function () {
            $http.get("/crud-list")
                .then(function (res) {
                    $scope.records = res.data;
                })
                .catch(function (err) {
                    console.error("Load error", err);
                });
        };

        // ======================================
        // CREATE
        // ======================================
        $scope.store = function () {

            if (!$scope.form.title || !$scope.form.description) {
                alert("Title and Description required");
                return;
            }

            $http.post("/crud-store", $scope.form, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            }).then(function () {
                window.location.href = "/crud";
            }).catch(function (err) {
                console.error("Create error", err);
            });
        };

        // ======================================
        // GET SINGLE
        // ======================================
        $scope.getSingle = function () {
            var id = new URLSearchParams(window.location.search).get("id");
            if (!id) return;

            $http.get("/crud-show/" + id)
                .then(function (res) {
                    $scope.form = res.data;
                })
                .catch(function (err) {
                    console.error("Fetch error", err);
                });
        };

        // ======================================
        // UPDATE
        // ======================================
        $scope.update = function () {

            if (!$scope.form.id) {
                alert("Invalid record");
                return;
            }

            $http.post("/crud-update/" + $scope.form.id, $scope.form, {
                headers: { "X-CSRF-TOKEN": csrfToken }
            }).then(function () {
                window.location.href = "/crud";
            }).catch(function (err) {
                console.error("Update error", err);
            });
        };

        // ======================================
        // DELETE
        // ======================================
        $scope.remove = function (id) {
            if (!confirm("Are you sure?")) return;

            $http.get("/crud-delete/" + id)
                .then(function () {
                    $scope.loadData();
                })
                .catch(function (err) {
                    console.error("Delete error", err);
                });
        };

       // ======================================
        // EXPORT CSV
        // ======================================
        $scope.exportCSV = function () {
            window.location.href = "/crud-export";
        };

        // ======================================
        // IMPORT CSV
        // ======================================
        $scope.importCSV = function () {

            if (!$scope.importFile) {
                alert("Please select CSV file");
                return;
            }

            var formData = new FormData();
            formData.append("file", $scope.importFile);

            $http.post("/crud-import", formData, {
                headers: {
                    "Content-Type": undefined,
                    "X-CSRF-TOKEN": csrfToken
                },
                transformRequest: angular.identity
            }).then(function (res) {

                alert("Imported " + res.data.inserted + " records");

                $scope.importFile = null;
                $scope.loadData();

            }).catch(function (err) {
                console.error("Import error", err);
                alert("Import failed");
            });
        };


    });

})();

```
# Step 10: Now Run Server and paste this url
```php
php artisan serve
http://127.0.0.1:8000/crud
```

<img width="1507" height="404" alt="image" src="https://github.com/user-attachments/assets/9d89aac3-cdde-4060-8502-d79ad96f6a21" />


<img width="1636" height="493" alt="image" src="https://github.com/user-attachments/assets/6a977edd-2a05-4df0-aac4-272423b26440" />
<img width="973" height="343" alt="image" src="https://github.com/user-attachments/assets/f026bf39-ee6f-43bf-88db-c3777d7c6b1d" />

<img width="1529" height="528" alt="image" src="https://github.com/user-attachments/assets/4f534e0b-d24a-4b5a-b500-387191707603" />
<img width="1512" height="585" alt="image" src="https://github.com/user-attachments/assets/fd15df9d-d59f-4e03-b18e-9f9ba3be8764" />











 

 


