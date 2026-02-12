{{-- Scrap-style notification popup: Enable Notifications (Allow / Not Now) --}}
<div class="notification-popup fixed left-1/2 top-5 z-[60] hidden w-[calc(100%-2rem)] max-w-[420px] -translate-x-1/2 rounded-2xl border border-stone-200 bg-white p-5 shadow-xl ring-1 ring-stone-200/80" id="notificationPopup">
    <div class="flex items-center gap-2 text-base font-semibold text-stone-800">
        <span aria-hidden="true">🔔</span>
        <span>Enable Notifications</span>
    </div>
    <p class="mt-2 text-sm leading-relaxed text-stone-600">Stay updated with daily quizzes, contests and important updates!</p>
    <div class="mt-4 flex justify-end gap-2">
        <button type="button" class="notification-popup-deny rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-100 transition-colors" id="notificationDenyBtn">Not Now</button>
        <button type="button" class="notification-popup-allow rounded-xl bg-indigo-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 transition-colors" id="notificationAllowBtn">Allow</button>
    </div>
</div>
