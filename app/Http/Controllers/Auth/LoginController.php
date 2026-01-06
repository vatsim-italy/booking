<?php

namespace App\Http\Controllers\Auth;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OAuthController;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $provider;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->provider = new OAuthController();
    }

    public function login(Request $request)
    {
        if (!$request->has('code') || !$request->has('state')) { // User has clicked "login", redirect to Connect
            if ($request->get('booking')) {
                // Check if the booking exists, just to prevent a 404 later on
                $booking = Booking::whereUuid($request->booking)->first();
                if (!empty($booking)) {
                    session()->put('booking', $booking->uuid);
                }
            } elseif ($request->get('event')) {
                // Check if the event exists, just to prevent a 404 later on
                $event = Event::whereSlug($request->event)->first();
                if (!empty($event)) {
                    session()->put('event', $event->slug);
                }
            }
            $authorizationUrl = $this->provider->getAuthorizationUrl(); // Generates state
            $request->session()->put('oauthstate', $this->provider->getState());
            return redirect()->away($authorizationUrl);
        } elseif ($request->input('state') !== session()->pull('oauthstate')) { // State mismatch, error
            flashMessage('error', 'Login failed', 'Something went wrong, please try again');
            return to_route('home');
        } else { // Callback (user has just logged in Connect)
            return $this->verifyLogin($request);
        }
    }

    protected function verifyLogin(Request $request)
    {
        try {
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->input('code')
            ]);
        } catch (IdentityProviderException $e) {
            flashMessage('error', 'Login failed', 'Something went wrong, please try again');
            return to_route('home');
        }
        $resourceOwner = json_decode(json_encode($this->provider->getResourceOwner($accessToken)->toArray()));

        $data = [
            'cid' => OAuthController::getOAuthProperty(config('oauth.mapping_cid'), $resourceOwner),
            'first_name' => OAuthController::getOAuthProperty(config('oauth.mapping_first_name'), $resourceOwner),
            'last_name' => OAuthController::getOAuthProperty(config('oauth.mapping_last_name'), $resourceOwner),
            'email' => OAuthController::getOAuthProperty(config('oauth.mapping_mail'), $resourceOwner),
        ];

        // Check if user has granted us the data we need
        if (
            !$data['cid'] ||
            !$data['first_name'] ||
            !$data['last_name'] ||
            !$data['email']
        ) {
            flashMessage('error', 'Login failed', 'We need you to grant us all marked permissions');
            return to_route('home');
        }

        $this->completeLogin($data, $accessToken);

        if (session('booking')) {
            $booking = Booking::whereUuid(session('booking'))->first();
            session()->forget('booking');
            if (!empty($booking)) {
                if ($booking->status != BookingStatus::BOOKED) {
                    return to_route('bookings.edit', $booking);
                }
                return to_route('bookings.show', $booking);
            }
        } elseif (session('event')) {
            $event = Event::whereSlug(session('event'))->first();
            session()->forget('event');
            if (!empty($event)) {
                return to_route('events.show', $event);
            }
        }
        return to_route('home');
    }

    protected function completeLogin($data, $token): User
    {
        $account = User::firstOrNew(['id' => $data['cid']]);

        $account->name_first = $data['first_name'];
        $account->name_last  = $data['last_name'];
        $account->email      = $data['email'];

        if ($token->getToken() !== null) {
            $account->access_token = $token->getToken();
        }
        if ($token->getRefreshToken() !== null) {
            $account->refresh_token = $token->getRefreshToken();
        }
        if ($token->getExpires() !== null) {
            $account->token_expires = $token->getExpires();
        }
        
        /**$api_data = $this->getATCStatus($data['cid']);
        $account->is_active_atc   = $api_data['atc_active'];
        $account->is_visiting_atc = $api_data['is_visiting'];
        */
        
        if ($account->is_preaccess !== true) {
            $account->is_preaccess = $this->getPreAccessStatus($data['cid']);
        }
        $account->save();
        auth()->loginUsingId($data['cid'], true);
        activity()->log('Login');

        return $account;
    }

    public function logout()
    {
        activity()->log('Logout');
        auth()->logout();
        return to_route('home');
    }

    protected function getATCStatus($cid): array
    {
        $data = [
            'atc_active' => false,
            'is_visiting' => false,
        ];

        try {
            $response = Http::withToken(env('VATITA_API_TOKEN'))
                ->timeout(5) // max 5 seconds
                ->get('https://training.vatita.net/api/users', [
                    'include' => ['allUsers', 'endorsements'],
                ]);

            if (!$response->ok()) {
                \Log::warning("VATITA API returned non-OK response ({$response->status()}) for CID {$cid}");
                return $data; // fail-safe
            }

            $users = $response->json()['data'] ?? [];
            $vatitaUser = collect($users)->firstWhere('id', intval($cid));

            if ($vatitaUser) {
                $visiting = $vatitaUser['endorsements']['visiting'] ?? null;
                $data['atc_active'] = $vatitaUser['atc_active'] ?? false;
                $data['is_visiting'] = is_array($visiting) && count($visiting) > 0;
            }
        } catch (\Throwable $e) {
            \Log::warning("VATITA API unavailable for CID {$cid}: " . $e->getMessage());
        }

        return $data;
    }


    protected function getPreAccessStatus($cid): bool
    {
        try {
            $urlsListResponse = Http::timeout(5)
                ->acceptJson()
                ->get('https://cdn.vatita.net/Eventi/bookings_preaccess/index.json');

            if (!$urlsListResponse->ok()) {
                return false;
            }

            $urls = $urlsListResponse->json();

            if (!is_array($urls)) {
                \Log::warning('Pre-access index.json returned invalid JSON', [
                    'body' => $urlsListResponse->body(),
                ]);
                return false;
            }

        } catch (\Throwable $e) {
            \Log::warning("Failed to fetch pre-access URLs list: " . $e->getMessage());
            return false;
        }

        foreach ($urls as $url) {
            if (!is_string($url)) {
                continue;
            }

            try {
                $response = Http::timeout(5)->acceptJson()->get($url);
                if (!$response->ok()) continue;

                $userIds = $response->json();

                if (!is_array($userIds)) continue;

                if (in_array((string) $cid, $userIds, true)) {
                    return true;
                }
            } catch (\Throwable $e) {
                \Log::warning("Failed to fetch pre-access data from {$url}: " . $e->getMessage());
            }
        }

        return false;
    }

}
