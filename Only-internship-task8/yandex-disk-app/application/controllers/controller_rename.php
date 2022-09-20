<?php
class Controller_Rename extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Rename();
    }

    function action_index()
    {
        $this->model->rename($_POST['file_path'], $_POST['file_name']);
        header("Location: /files");
    }

}