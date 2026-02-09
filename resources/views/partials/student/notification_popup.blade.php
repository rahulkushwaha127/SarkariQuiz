{{-- Scrap-style notification popup: Enable Notifications (Allow / Not Now) --}}
<div class="notification-popup fixed left-1/2 top-5 z-[60] hidden w-[calc(100%-2rem)] max-w-[420px] -translate-x-1/2 rounded-xl border border-white/10 bg-slate-900/95 p-5 shadow-xl ring-1 ring-white/10 backdrop-blur" id="notificationPopup">
    <div class="flex items-center gap-2 text-base font-semibold text-white">
        <span aria-hidden="true">🔔</span>
        <span>Enable Notifications</span>
    </div>
    <p class="mt-2 text-sm leading-relaxed text-slate-300">Stay updated with daily quizzes, contests and important updates!</p>
    <div class="mt-4 flex justify-end gap-2">
        <button type="button" class="notification-popup-deny rounded-xl border border-white/20 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-white/10" id="notificationDenyBtn">Not Now</button>
        <button type="button" class="notification-popup-allow rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400" id="notificationAllowBtn">Allow</button>
    </div>
</div>
