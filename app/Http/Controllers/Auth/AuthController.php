<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\AuthRepository;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Response trait to handle return responses.
     */
    use ResponseTrait;

    /**
     * Auth related functionalities.
     *
     * @var AuthRepository
     */
    public $authRepository;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(AuthRepository $ar)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authRepository = $ar;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Login",
     *     description="Login",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", example="renato.lucena@bionexo.com"),
     *              @OA\Property(property="password", type="string", example="secret")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Login" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     *
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            if ($token = $this->guard()->attempt($credentials)) {
                $data =  $this->respondWithToken($token);
                $maxAttempsAllowed = 3;
                $user = User::where('email', $request->email)->first();
                $user->attempts = $user->attempts + 1;
                $user->save();

                if ($user->attempts >= $maxAttempsAllowed) {
                    $user->islocked = true;
                    $user->lock_date = Carbon::now()->format('Y-m-d H:i:s');
                    $user->save();
                }
                if ($user->islocked == true) {
                    $response = ["message" => 'User does not autorization'];
                    return response()->json($response, 401);
                }
                $user->tokenvalidation = $token;
                $user->attempts = 0;
                $user->last_access = Carbon::now();
                $user->save();
            } else {
                return $this->responseError(null, 'Invalid Email and Password !', Response::HTTP_UNAUTHORIZED);
            }

            return $this->responseSuccess($data, 'Logged In Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Authentication"},
     *     summary="Register User",
     *     description="Register New User",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Jhon Doe"),
     *              @OA\Property(property="email", type="string", example="jhondoe@example.com"),
     *              @OA\Property(property="password", type="string", example="123456"),
     *              @OA\Property(property="password_confirmation", type="string", example="123456")
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Register New User Data" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $requestData = $request->only('name', 'email', 'password', 'password_confirmation');
            $user = $this->authRepository->register($requestData);
            if ($user) {
                if ($token = $this->guard()->attempt($requestData)) {
                    $data =  $this->respondWithToken($token);
                    return $this->responseSuccess($data, 'User Registered and Logged in Successfully', Response::HTTP_OK);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"Authentication"},
     *     summary="Authenticated User Profile",
     *     description="Authenticated User Profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Authenticated User Profile" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function me(): JsonResponse
    {
        try {
            $data = $this->guard()->user();
            return $this->responseSuccess($data, 'Profile Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout",
     *     description="Logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function logout(): JsonResponse
    {
        try {
            $this->guard()->logout();
            return $this->responseSuccess(null, 'Logged out successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh",
     *     description="Refresh",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Refresh" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function refresh(): JsonResponse
    {
        try {
            $data = $this->respondWithToken($this->guard()->refresh());
            return $this->responseSuccess($data, 'Token Refreshed Successfully !');
        } catch (\Exception $e) {
            return $this->responseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token): array
    {
        $data = [[
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60 * 24 * 30, // 43200 Minutes = 30 Days
            'user' => $this->guard()->user()
        ]];
        return $data[0];
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard(): \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
    {
        return Auth::guard();
    }
}
