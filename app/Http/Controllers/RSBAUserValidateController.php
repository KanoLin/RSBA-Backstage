<?php
namespace App\Http\Controllers;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class RSBAUserValidateController extends Controller
{
    public function init(Request $request)
    {
        if ($request->session()->has('name'))
            return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => [
                'logined' => true,
                'student_id' => $request->session()->get('student_id'),
                'name' => $request->session()->get('name'),
                'is_manager' => $request->session()->get('is_manager')
            ]
        ]);
        else return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => [
                'logined' => false
            ]
        ]);
    }

    public function signout(Request $request)
    {
        $request->session()->flush();
        return response()->json([
            'err_code' => 0,
            'err_msg' => '',
        ]);
    }

    //登录认证
    public function login(Request $request)
    {
        $server_url = config('RSBA.auth_url');

        $post_data = array(
            'key' => config('RSBA.auth_key'),
            'stuno' => $request->student_id,
            'password' => $request->password
        );
        $post_string = http_build_query($post_data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $server_url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($data, true);
        if ($data['errcode'] != 0)
            return response()->json([
            'err_code' => $data['errcode'],
            'err_msg' => $data['errmsg'],
        ]);

        $data = $data['data'];
        $jud=(($data['grp'] == '主管') || ($data['grp'] == '干事')) ? false : true;
        $request->session()->put([
            'student_id' => $request->student_id,
            'name' => $data['name'],
            'tele'=>$data['mobile'],
            'dep'=> $data['dep'],
            'is_manager' => $jud
            
        ]); 

        return response()->json([
            'err_code' => 0,
            'err_msg' => '',
            'data' => [
                'name' => $data['name'],
                'is_manager' => $jud 
            ]
        ]);
    }
}