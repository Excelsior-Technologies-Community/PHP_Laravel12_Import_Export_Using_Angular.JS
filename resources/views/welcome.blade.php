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
