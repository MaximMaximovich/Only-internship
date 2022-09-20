<div class="container">
    <h1>Files</h1>
    <p></p>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">File name</th>
                <th scope="col">Size</th>
                <th scope="col">Created</th>
                <th scope="col">Modified</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($data as $item) : ?>
            <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['size'] ?></td>
                <td><?= $item['created']?></td>
                <td><?= $item['modified']?></td>
                <td>
                    <form action="/detail" method="post">
                        <button type="submit" class="btn btn-outline-primary" name="detail" value=<?= "".$item['path'] ?>>Detail</button>
                    </form>
                </td>
                <td>
                    <form action="/edit" method="post">
                        <button type="submit" class="btn btn-outline-primary" name="edit" value=<?= "".$item['path'] ?>>Rename</button>
                    </form>
                </td>
                <td>
                    <form action="/delete" method="post">
                        <button type="submit" class="btn btn-outline-danger" name="delete" value=<?= "".$item['path'] ?>>Delete</button>
                    </form>
                </td>

            </tr>
        <? endforeach; ?>
        </tbody>
</div>
<div>
    <form action="/upload" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <button type="submit" name="upload" class="btn btn-outline-primary">Add file</button>
    </form>
</div>

