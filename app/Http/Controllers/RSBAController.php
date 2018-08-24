<?php
namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Activity;
use App\MemberList;
use App\MemberNow;
use App\SignUp;


class RSBAController extends Controller
{
    //管理员发起志愿者活动
    public function volunteer(Request $request)
    {

        if (!$request->has(['title', 'details', 'action_time', 'member']))
            return response()->json([
            'err_code' => 2,
            'err_msg' => '数据不足！',
        ]);
        $va = new Activity;
        $va->title = $request->title;
        $va->type = 0;
        $va->publisher = 'test';//$request->session()->get('user');
        $va->details = $request->details;
        $va->time = $request->action_time;
        $va->member = $request->member;


        $ml = new MemberList;

        $mn = new MemberNow;


        if ($va->save() && $ml->save() && $mn->save())
            return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'id' => $va->id
        ]);
        else return response()->json([
            'err_code' => 3,
            'err_msg' => '保存失败！'
        ]);
    }
    
    //管理员发起福利活动
    public function award(Request $request)
    {

        if (!$request->has(['title', 'details', 'book_time', 'award', 'member_list']))
            return response()->json([
            'err_code' => 2,
            'err_msg' => '数据不足！',
        ]);

        if ($request->award > array_sum($request->member_list))
            return response()->json([
            'err_code' => 8,
            'err_msg' => '限制人数少于奖品数哦！',
        ]);

        $va = new Activity;
        $va->title = $request->title;
        $va->type = 1;
        $va->publisher = 'test';//$request->session()->get('user');
        $va->details = $request->details;
        $va->time = $request->book_time;
        $va->award = $request->award;


        $ml = new MemberList;
        $member_arr = $request->member_list;
        for ($i = 0; $i < 10; $i++) {
            $ml->{$i} = $member_arr[$i];
        }

        $mn = new MemberNow;
        if ($va->save() && $ml->save() && $mn->save())
            return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'id' => $va->id
        ]);
        else return response()->json([
            'err_code' => 3,
            'err_msg' => '保存失败！'
        ]);
    }

    //管理员查询各部门报名人数
    public function member_query(Request $request, $id)
    {

        $ml = MemberList::find($id);
        $mn = MemberNow::find($id);
        if (($ml == null) || ($mn == null))
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在'
        ]);
        $ml = $ml->toArray();
        $mn = $mn->toArray();
        unset($ml['id']);
        unset($mn['id']);
        unset($ml['created_at']);
        unset($mn['created_at']);
        unset($ml['updated_at']);
        unset($mn['updated_at']);
        $member_arr = array_values($ml);
        $current_arr = array_values($mn);
        return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => [
                'member_list' => $member_arr,
                'current_member_list' => $current_arr
            ]
        ]);
    }

    //管理员浏览报名用户信息
    public function userinfo_query(Request $request, $id)
    {
        $activity = Activity::find($id);
        if ($activity == null)
            return response()->json([
            'err_code' => 4,
            'err_msg' => '活动不存在'
        ]);
        $users = $activity->user()
            ->skip($request->start_ord - 1)
            ->take($request->number + 1)
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
        if ($i == $request->number + 1) {
            array_pop($usersdata);
            $data['is_end'] = false;
        } else $data['is_end'] = true;
        $data['users'] = $usersdata;
        return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => $data
        ]);
    }




}