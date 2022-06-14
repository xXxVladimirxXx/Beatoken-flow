<?php

namespace App\Http\Controllers\Api;

use App\Traits\Eloquent\Uploadable;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Mail};
use App\Mail\RestorePassword;
use App\Models\{User};
use App\Services\FlowService;
use App\Http\Requests\{UserRequest, AuthRequest, RegisterRequest};
use JWTAuth;
use JWTAuthException;
use Auth;

class UserController extends Controller
{
    use SendsPasswordResetEmails;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        return User::get();
    }

    public function show(User $user)
    {
        return $user->load('nfts');
    }

    public function getMiddlemanUser()
    {
        return User::where('role_id', 4)->first();
    }

    public function getWithdrawManagerUser()
    {
        return User::where('role_id', 6)->first();
    }

    public function update(User $user, UserRequest $request)
    {
        $user->update($request->only('name', 'role_id', 'flow_email', 'flow_addr', 'twitter_url', 'instagram_url', 'facebook_url'));
        setcookie('user_name', $user->name, strtotime('+3 days'), '/', 'beatoken.com');
        return $user;
    }

    public function currentUser()
    {
        return Auth::user()->load('nfts');
    }

    public function refillBalance(User $user, Request $request, FlowService $flowService)
    {
        $flowService->refillBalance($user, $request);
    }

    public function authenticate(AuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();
        if($user && $user->password == 'confirm') {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_email_or_password',
                ], 400);
            }
        } catch (JWTAuthException $e) {
            info('$e->getMessage()');
            info($e->getMessage());
            return response()->json([
                'response' => 'error',
                'message' => 'failed_to_create_token',
            ], 500);
        }

        $user = User::with('role')->find(Auth::id());

        setcookie('user_name', $user->name, strtotime('+3 days'), '/', 'beatoken.com');
        setcookie('user_picture', $user->full_uri_avatar, strtotime('+3 days'), '/', 'beatoken.com');

        return response()->json(compact('token', 'user'));
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create($request->only('email', 'name', 'password'));
            $password = bcrypt($request->password);
            $user->password = $password;
            $user->save();
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return response()->json([
                'response' => 'error',
                'messageError' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'user' => $user
        ], 200);
    }

    public function changeAvatar(Request $request, User $user)
    {
        $user->avatar_url = Uploadable::uploadPhoto($request->avatar,
            'avatar.jpg',
            Auth::id() . '_user_avatar'
        );
        $user->save();

        setcookie('user_picture', $user->full_uri_avatar, strtotime('+3 days'), '/', 'beatoken.com');

        return response()->json($user, 200);
    }

    public function changePassword(Request $request, User $user)
    {
        $credentials = ['email' => $user->email, 'password' => $request->oldPassword];
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'response' => 'error',
                'message' => 'invalid_password',
            ], 400);
        }
        $user->password = bcrypt($request->newPassword);
        $user->save();

        return response()->json($user, 200);
    }


    public function forgotPassword($email)
    {
        try {
            $oUser = User::where(['email' => $email])->first();

            if (is_null($oUser)) {
                return response()->json(['message' => 'Failed to send the email to restore. User with this email was not found'], 400);
            }

            //so we can have dependency
            $password_broker = $this->broker();
            //create reset password token
            $token = $password_broker->createToken($oUser);

            DB::table('password_resets')->insert(['email' => $oUser->email, 'token' => $token, 'created_at' => new Carbon]);

            Mail::to($oUser->email)
                ->send(new RestorePassword($token));

            return response()->json(['message' => 'Restore Email has been sent to Your Inbox'], 200);
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to send the email to restore. ' . $exception->getMessage()], 400);
        }
    }

    public function checkToken(Request $request)
    {
        $token = DB::table('password_resets')
            ->where([
                ['token', $request->token],
                ['created_at', '>', Carbon::now()->subHours(2)]
            ])
            ->first();

        if (is_null($token)) {
            return response()->json(['message' => 'Restore token is not valid'], 403);
        }

        return response()->json(['message' => 'Restore token is valid', 'success' => true], 200);
    }

    public function setNewPassword(Request $request)
    {
        $token = DB::table('password_resets')
            ->where([
                ['token', $request->token],
                ['created_at', '>', Carbon::now()->subHours(2)]
            ])
            ->first();

        if (is_null($token)) {
            return response()->json(['message' => 'Restore token is not valid'], 403);
        }

        $oUser = User::where('email', $token->email)->first();
        $oUser->password = bcrypt($request->password);
        $oUser->save();

        DB::table('password_resets')
            ->where('token', $request->token)
            ->delete();

        return response()->json(['New password has been set', 'success' => true], 200);
    }


    public function logout()
    {
        setcookie('user_name', '', time() - 3600, '/', 'beatoken.com');
        setcookie('user_picture', '', time() - 3600, '/', 'beatoken.com');
        setcookie('beatoken_session', '', time() - 3600, '/');
        return redirect(env('WP_BEATOKEN', 'https://beatoken.com'));
    }
}
