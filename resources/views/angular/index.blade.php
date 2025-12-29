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
