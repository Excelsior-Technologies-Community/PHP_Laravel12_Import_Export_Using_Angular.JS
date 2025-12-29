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
