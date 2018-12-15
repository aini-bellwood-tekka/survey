<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Socialite;

use App\User;
use App\SocialAccount;

class SocialController extends Controller
{
    protected $redirectTo = '';        //your-redirect-url-after-login

    // twitter

    public function getTwitterAuth()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function getTwitterAuthCallback()
    {
        $twitterUser = Socialite::driver('twitter')->user();

        $user = $this->createOrGetUser($twitterUser, 'twitter');
        Auth::login($user, true);

        
        session(['user_id' => $user->id]);
        session(['user_name' => $user->name]);
        session(['screen_name' => $user->screen_name]);
        session(['logon' => true]);
        
        return redirect($this->redirectTo);
    }

    public function createOrGetUser($providerUser, $provider)
    {
        $account = SocialAccount::firstOrCreate([
            'provider_user_id' => $providerUser->getId(),
            'provider'         => $provider,
        ]);

        if (empty($account->user))
        {
            $user = User::create([
                'name'   => $providerUser->getName(),
                // 'email'  => $providerUser->getEmail(), # 削除 (2017.05.19)
                'avatar' => $providerUser->getAvatar(),
            ]);
            $account->user()->associate($user);
        }

        $account->provider_access_token = $providerUser->token;
        $account->save();

        return $account->user;
    }
}