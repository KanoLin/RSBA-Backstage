<?php
namespace App\Http\Controllers;

use App\Exports\RSBAExport;
use App\Activity;
use App\Exports\ActivityUserExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RSBAExportController extends Controller
{
    public function export0()
    {
        return (new RSBAExport)->download('百步梯活动管理系统'.date('Y-m-d H:i:s').'.xlsx');
    }
    public function export(Request $request,$id)
    {
        return (new ActivityUserExport($id))->download(Activity::find($id)->title.'-人员报名表.xlsx');
    }
}