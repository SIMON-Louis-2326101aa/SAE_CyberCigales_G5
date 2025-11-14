<?php

namespace SAE_CyberCigales_G5\Modules\controller;
namespace Modules\Controller;

use SAE_CyberCigales_G5\Modules\Model\ButterflyWayModel;
use SAE_CyberCigales_G5\includes\ViewHandler;

class ButterflyWayController
{
    public function __construct(private ButterflyWayModel $model)
    {
    }

    public function home(): array
    {
        return $this->model->state();
    }
    public function code(): array
    {
        return $this->model->state();
    }

    // actions
    public function start(): void
    {
        $this->model->start();
        header('Location: ?m=papillon&a=home');
        exit;
    }
    public function left(): void
    {
        $this->model->move('L');
        header('Location: ?m=papillon&a=home');
        exit;
    }
    public function right(): void
    {
        $this->model->move('R');
        header('Location: ?m=papillon&a=home');
        exit;
    }
    public function turn(): void
    {
        $this->model->turnBack();
        header('Location: ?m=papillon&a=' . ($this->model->state()['step'] >= 10 ? 'code' : 'home'));
        exit;
    }
    public function submitCode(): void
    {
        $this->model->submitCode($_POST['code'] ?? '');
        header('Location: ?m=papillon&a=code');
        exit;
    }
}
