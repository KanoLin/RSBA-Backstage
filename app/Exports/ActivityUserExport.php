<?php

namespace App\Exports;

use App\Activity;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class ActivityUserExport implements FromQuery, WithStrictNullComparison,WithHeadings,ShouldAutoSize,WithTitle
{
    use Exportable;
    public $id;
    public function headings(): array
    {
        return [
            '姓名',
            '学号',
            '部门',
            '手机'
        ];
    }
    public function __construct(int $id)
    {
        $this->id = $id;
    }
    public function title(): string
    {
        return '活动用户信息表';
    }
    public function query()
    {
        return Activity::query()->find($this->id)->user();
    }
}