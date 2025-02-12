<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ReportExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */

    private $data;
    private $view;

    public function __construct($view, $data){
        $this->view = $view;
        $this->data = $data;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }
}
