<?php

namespace App\Livewire\Concerns;

use Mary\Traits\Toast;

trait Notifies
{
    use Toast;

    public function notifySuccess(string $title, ?string $description = null): void
    {
        $this->success($title, $description);
    }

    public function notifyError(string $title, ?string $description = null): void
    {
        $this->error($title, $description);
    }

    public function notifyWarning(string $title, ?string $description = null): void
    {
        $this->warning($title, $description);
    }

    public function notifyInfo(string $title, ?string $description = null): void
    {
        $this->info($title, $description);
    }
}
