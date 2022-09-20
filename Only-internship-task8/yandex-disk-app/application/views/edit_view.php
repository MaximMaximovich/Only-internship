<div class="container">
    <h1>Rename</h1>


    <form method="POST" action="rename" class="needs-validation" novalidate>
        <input type="hidden" name="file_path" value="<?=$data['path']?>">
        <div class="row mb-3">
            <label for="file_name" class="col-md-4 col-form-label text-md-end">File Name</label>

            <div class="col-md-6">
                <input id="file_name" type="text" class="form-control" name="file_name" value="<?=$data['name']?>">
            </div>
        </div>

        <div class="row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary" name="submit" value="done">
                    Change Name
                </button>
            </div>
        </div>
    </form>
</div>
