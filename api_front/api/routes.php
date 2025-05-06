<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Models\Audit;
use Models\QualityLabel;

use School\Manager\SchoolManager;
use App\Http\Controllers\SocialiteController;

use Api\Collections\QualityLabels as QualityLabelsCollection;
use Api\Collections\Audits as AuditCollection;
use Api\Resources\User as UserResource;
use Api\Controllers\AuditController;
use Api\Controllers\GraphController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return response('authenticated', 200)
            ->header('Content-Type', 'application/json');
    }

    return response('authentication failed', 400)
        ->header('Content-Type', 'application/json');
});

// La redirection vers le provider
Route::get("/redirect/{provider}", [SocialiteController::class, "redirect"])->name('socialite.redirect');

// Le callback du provider
Route::get("/auth/callback/{provider}", [SocialiteController::class, "callback"])->name('socialite.callback');
#endregion auth

Route::middleware(['api'])->group(function () {

    #region auth
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        $user = new UserResource($request->user()->load(['unit']));
        // dd($user);
        return $user->toJson();
    });

    Route::post("/logout", function (Request $request) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect("/login");
    });

    #region gets
    //des labels qualité ex: qualiopi
    Route::get("/qualityLabel", function (Request $request) {
        //NOTE les paramètres de la requête ne sont pas appliqués
        // $q = $request->get('q');
        $q = "all";
        if ($q === "all") {
            $labels = QualityLabel::all();
        }
        if (is_int($q)) {
            $labels = QualityLabel::find($q);
        }

        return new QualityLabelsCollection($labels);
    });

    //des apprenants
    Route::get("/students", function (Request $request) {
        $request->validate([
            'formations'  => ['array'],
            'groups' => ['array'],
            'since' => ['nullable', 'date'],
            'until' => ['nullable', 'date'],
        ]);

        $u = $request->get('until');
        $s = $request->get("since");
        // je résupère du json
        $formations = $request->get("formations");
        $groups = $request->get("groups");

        $data = app(SchoolManager::class)->getStudents(!is_null($formations) ? $formations : [], !is_null($groups) ? $groups : [], $s, $u);

        return $data;
    });

    //des formations
    Route::get("/formations", function (Request $request) {

        //toutes les formations actives à ce jour

        //attention bug intelephance la fonction get ci-dessous est celle d'ypareo
        $formations = app(SchoolManager::class)->getFormations();

        return $formations;
    });

    //des periodes
    Route::get("/periods", function (Request $request) {
        $request->validate([
            'since' => ['nullable', 'date'],
            'until' => ['nullable', 'date'],
        ]);

        $u = $request->get('until');
        $s = $request->get("since");

        $request->request->set("internal", false);

        $periods = app(SchoolManager::class)->getPeriods($s, $u, false);
        return $periods;
    });

    //des groupes
    Route::get('/groups', function (Request $request) {
        $request->validate([
            'formations'  => ['required'],
            'since' => ['nullable', 'date'],
            'until' => ['nullable', 'date'],
        ]);

        $formations = $request->get("formations");

        $u = $request->get('until');
        $s = $request->get("since");

        //définit les périodes sélectionné
        $request->request->set("internal", true);
        $periods = app(SchoolManager::class)->getPeriods($s, $u, false);

        //periode ids array;
        $groups = app(SchoolManager::class)->getGroups($periods, $formations);

        return $groups;
    });

    //des audits
    Route::get("/audits", function (Request $request) {
        $rlabel = $request->get('quality_label');
        $audits = Audit::with(['qualityLabel'])
            ->whereHas('qualityLabel', function ($query) use ($rlabel) {
                $query->where('label', '=', $rlabel);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return new AuditCollection($audits);
    });

    Route::get("/wealths", [GraphController::class, "filteredWealths"])->name('wealths.filtered');
    Route::get("/graph/{view}/{what}/{params?}", [GraphController::class, "getGraph"])->name('api.graph');
    #endregion gets

    #region posts
    Route::post("/audit", [AuditController::class, "index"])->name('audit.summary');
    Route::post("/audit/result", [AuditController::class, "result"])->name('audit.result');
    Route::post("/audit/validate", [AuditController::class, "validateAudit"])->name('audit.validate');
    #endregion posts

    #region visualisation

    #endregion visualisation
});
