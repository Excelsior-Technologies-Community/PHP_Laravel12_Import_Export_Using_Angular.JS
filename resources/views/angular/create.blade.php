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
