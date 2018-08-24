<?php
namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Activity;
use App\MemberList;
use App\MemberNow;
use Illuminate\Support\Facades\DB;


class RSBAUserController extends Controller
{
    //用户报名
    public function register(Request $request, $id)
    {
        $name = $request->session()->get('name');
        $activity = Activity::find($id);
        if ($activity == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在！'
        ]);
        $ml = MemberList::find($id);
        $mn = MemberNow::find($id);
        $user = User::where('name', $name)->first();
        $sum = 0;
        DB::transaction(function () {
            $member = $activity->member;
            for ($i = 0; $i < 10; $i++)
                $sum += $mn->{$i};
            if ($activity->type == 0)
                if ($sum < $member) {
                $activity->user()->attach($user->id);
                $mn->{$user->department}++;
                $mn->save();
                $errcode = 0;
                $errmsg = '';
            } else {
                $errcode = 1;
                $errmsg = '总人数已达上限哦！';
            } else if ($mn->{$user->department} < $ml->{$user->department}) {
                $activity->user()->attach($user->id);
                $mn->{$user->department}++;
                $mn->save();
                $errcode = 0;
                $errmsg = '';
            } else {
                $errcode = 2;
                $errmsg = '部门人数已达上限哦！';
            }
        }, 5);
        return response()->json([
            'err_code' => $errcode,
            'err_msg' => $errmsg
        ]);
    }



}