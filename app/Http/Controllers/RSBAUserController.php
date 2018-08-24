<?php
namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Activity;
use App\MemberList;
use App\MemberNow;
use App\User;
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
        if (($activity->type == 0) && ($activity->time < date("Y-M-D h:m:s")))
            return response()->json([
            'err_code' => 6,
            'err_msg' => '活动已开始！'
        ]);
        if (($activity->type == 1) && ($activity->time > date("Y-M-D h:m:s")))
            return response()->json([
            'err_code' => 6,
            'err_msg' => '活动未开始！'
        ]);
        $ml = MemberList::find($id);
        $mn = MemberNow::find($id);
        $user = User::where('name', $name)->first();
        $sum = 0;
        $errcode = -1;
        $errmsg = '完了，凉凉';
        DB::transaction(function () {
            $member = $activity->member;
            $award = $activity->member;
            $current = $activity->current_member;
            if (($current >= $member) || ($current >= $award)) {
                $errcode = 1;
                $errmsg = '总人数已达上限哦！';
            } else if ($mn->{$user->department} >= $ml->{$user->department}) {
                $errcode = 2;
                $errmsg = '部门人数已达上限哦！';
            } else {
                $activity->current_member++;
                $mn->{$user->department}++;
                $activity->save();
                $mn->save();
                $activity->user()->attach($user->id);
                $errcode = 0;
                $errmsg = '';
            }
        }, 5);
        return response()->json([
            'err_code' => $errcode,
            'err_msg' => $errmsg
        ]);
    }

    //浏览活动情况
    public function activity_query(Request $request)
    {
        $name = $request->session()->get('name');
        $user = User::where('name', $name)->first();
        if ($user == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '你，不存在'
        ]);
        if ($request->start_id == 0) $startid = Activity::orderby('id', 'desc')->first()->id;
        else $startid = $request->start_id;
        switch ($request->type) {
            case 0:
                $activities = Activity::where('id', '<=', $startid)
                    ->orderBy('id', 'desc')
                    ->take($request->number + 1)
                    ->get();
                break;
            case 1:
                $activities = Activity::whereDoesntHave('user', function ($query) {
                    $query->where('id', $user->id);
                })->orderBy('id', 'desc')
                    ->take($request->number + 1)
                    ->get();
                break;
            case 2:
                $activities = Activity::whereHas('user', function ($query) {
                    $query->where('id', $user->id);
                })->orderBy('id', 'desc')
                    ->take($request->number + 1)
                    ->get();
                break;
            case 3:
                $activities = Activity::where('publisher', $user->name)
                    ->orderBy('id', 'desc')
                    ->take($request->number + 1)
                    ->get();
                break;
        }

        $i = 0;
        $data = array();
        $actsdata = array();
        foreach ($activities as $act) {
            $i++;
            $ary = array();
            $ary = [
                'id' => $act->id,
                'is_publisher' => ($name == $act->publisher) ? true : false,
                'registered' => ($act->user()->where('name', $name) == null) ? false : true,
                'type' => $act->type,
                'title' => $act->title,
                'details' => $act->details
            ];
            if ($act->type == 0) {
                $ary['action_time'] = $act->time;
                $ary['member'] = $act->member;
            } else {
                $ary['book_time'] = $act->time;
                $ary['award'] = $act->award;
            }
            $ary['current_member'] = $act->current_member;
            if (($act->type == 1) && (MemberList::find($act->id)->{$user->department} <= MemberNow::find($act->id)->{$user->department}))
                $ary['is_department_full'] = true;
            else $ary['is_department_full'] = false;
            $actsdata[] = $ary;
        }
        if ($i == $request->number + 1) {
            array_pop($actsdata);
            $data['is_end'] = false;
        } else $data['is_end'] = true;
        $data['activity'] = $actsdata;
        return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => $data
        ]);
    }


}