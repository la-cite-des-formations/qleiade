<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Models\User;
use GuzzleHttp\Exception\ClientException;

class SocialiteController extends Controller
{
    // Les tableaux des providers autorisés
    // protected $providers = [ "google", "github", "facebook" ];
    protected $providers = ["google"];



    // public function index()
    // {
    //     return view("auth.connect");
    // }
    // # La vue pour les liens vers les providers
    // public function loginRegister()
    // {
    //     return view("socialite.login-register");
    // }

    # redirection vers le provider
    public function redirect(Request $request)
    {
        $provider = $request->provider;
        // dd($provider);
        // On vérifie si le provider est autorisé
        if (in_array($provider, $this->providers)) {
            // return Socialite::driver($provider)->stateless()->redirect(); // On redirige vers le provider
            return response()->json([
                'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
            ]);
        } else {

            abort(401); // Si le provider n'est pas autorisé
        }
    }

    // Callback du provider
    public function callback(Request $request)
    {

        $provider = $request->provider;

        if (in_array($provider, $this->providers)) {

            // Les informations provenant du provider
            try {
                /** @var SocialiteUser $socialiteUser */
                $data = Socialite::driver('google')->stateless()->user();
            } catch (ClientException $e) {
                return response()->json(['error' => 'Invalid credentials provided.'], 422);
            }

            // Les informations de l'utilisateur
            $user = $data->user;

            # Social login - register
            $email = $data->getEmail(); // L'adresse email
            $name = $data->getName(); // le nom

            # 1. On récupère l'utilisateur à partir de l'adresse email
            $user = User::where("email", $email)->first();

            # 2. Si l'utilisateur existe
            if (isset($user)) {

                // Mise à jour des informations de l'utilisateur
                $user->name = $name;
                $user->save();

                # 3. Si l'utilisateur n'existe pas, on kick, ou on l'enregistre
            } else {
                abort(403);
                // // Enregistrement de l'utilisateur
                // $user = User::create([
                //     'name' => $name,
                //     'email' => $email,
                //     'password' => bcrypt("emilie") // On attribue un mot de passe
                // ]);
            }

            # 4. On connecte l'utilisateur
            auth()->login($user);

            # 5. On redirige l'utilisateur vers /home
            if (auth()->check()) return response()->json([
                'user' => $user,
            ]);
            // if (auth()->check()) return redirect(route('home'));
        }
        abort(404);
    }
}
