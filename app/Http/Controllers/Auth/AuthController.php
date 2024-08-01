<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(Request $request){
        return view('auth.index');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $cookie = Cookie::make('jwt_token', $token, 60 * 3);

        return $this->respondWithToken($token)->withCookie($cookie);
    }

    public function register(Request $request){

        $user = User::create([
            'name' => $request->name,
            'email' => $request->emailReg,
            'password' => bcrypt($request->passwordReg),
        ]);

        return response()->json([
            'message' => 'success',
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $token = Cookie::get('jwt_token') ?? $request->bearerToken();
            
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            Auth::setToken($token);
            $user = Auth::authenticate();
            if (!$user) {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            auth()->logout();

            $cookie = Cookie::forget('jwt_token');

            return response()->json(['message' => 'Successfully logged out'])->withCookie($cookie);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
