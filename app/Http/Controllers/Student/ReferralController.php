<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $referralService = app(ReferralService::class);

        $referredCount = $referralService->referredUserSignupCount($user->id);
        $convertedCount = $referralService->referredUserConversionCount($user->id);
        $mRequired = (int) Setting::cachedGet(ReferralService::SETTING_CONVERSIONS_REQUIRED, 1);
        $hasReceivedReward = $user->referralRewardReceived()->exists();
        $referralLink = $user->getReferralLink();

        return view('student.referral.index', [
            'referralLink' => $referralLink,
            'referredCount' => $referredCount,
            'convertedCount' => $convertedCount,
            'mRequired' => $mRequired,
            'hasReceivedReward' => $hasReceivedReward,
        ]);
    }
}
