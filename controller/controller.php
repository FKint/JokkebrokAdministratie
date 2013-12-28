<?php
class Controller {
    protected $model;
    public function __construct($model) {
        $this->model = $model;
    }

    public function execute() {
        if(!isset($_GET['action'])) {
            return;
        }
        $action = $_GET['action'];
        switch($action) {
            case 'login':
                if($this->model->getSession()->login($_POST)){
                    $this->reloadPage();
                }
                break;
            case 'logout':
                $this->model->getSession()->logout();
                $this->reloadPage();
                break;
            default:
                return;
        }
    }

    //Don't shoot the coder for putting this in Controller :(
    private function reloadPage() {
        header('Location: index.php');
        exit ;
    }

}
?>