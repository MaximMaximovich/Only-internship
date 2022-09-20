<?php
class Controller_Upload extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model_Upload();
    }

    function action_index()
    {
        $this->model->upload($_FILES['file']);
        header("Location: /files");
    }

}