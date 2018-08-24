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
        $ml = MemberList::find($id);
        $mn = MemberNow::find($id);
        $user = User::where('name', $name)->first();
        $sum = 0;
        DB::transaction(function () {
            $member = $activity->member;
            $award=$activity->member;
            for ($i = 0; $i < 10; $i++)
                $sum += $mn->{$i};

            /* if ((($sum>=$member)&&($member!=0))||(($sum>=award)&&)
            
            
            
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
            } */
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




        $users = $activity->user()
            ->skip($request->start_ord)
            ->take($request->number)
            ->get();
        $i = 0;
        $data = array();
        $usersdata = array();
        foreach ($users as $user) {
            $i++;
            $usersdata[] = [
                'student_id' => $user->stuno,
                'name' => $user->name,
                'department' => config('RSBA.' . $user->department),
                'tele' => $user->tele
            ];
        }
        if ($i == $request->number) {
            array_pop($usersdata);
            $data[] = ['is_end' => false];
        } else $data[] = ['is_end' => true];
        $data[] = ['users' => $usersdata];
        return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => $data
        ]);
    }
    private function activity_query_0(Request $request, $name)
    {
        if ($request->start_id == 0) $startid = Activity::orderby('id', 'desc')->first()->id;
        else $startid = $request->start_id;
        $activities = Activity::where('id', '<=', $startid)
            ->orderBy('id', 'desc')
            ->take($request->number + 1)
            ->get();
        $i = 0;
        $data = array();
        $actsdata = array();
        foreach ($activities as $act) {
            $i++;
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

        }


    }


}