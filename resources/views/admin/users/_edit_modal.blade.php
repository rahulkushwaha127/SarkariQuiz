<div class="space-y-4">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-sm font-semibold text-slate-900">{{ $user->name }}</div>
            <div class="text-sm text-slate-600">{{ $user->email }}</div>
            @if ($user->username)
                <div class="text-xs text-slate-500">{{ '@' . $user->username }}</div>
            @endif
        </div>
        @if ($user->blocked_at)
            <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">Blocked</span>
        @else
            <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-800">Active</span>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Name</label>
                <input name="name" value="{{ old('name', $user->name) }}" required
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input name="email" type="email" value="{{ old('email', $user->email) }}" required
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Username</label>
                <input name="username" value="{{ old('username', $user->username) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="optional">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">New password</label>
                <input name="password" type="password"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="leave blank to keep current">
                <div class="mt-1 text-xs text-slate-500">Minimum 8 characters.</div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Role</label>
            <select name="role" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" required>
                @foreach ($roles as $roleValue => $roleLabel)
                    <option value="{{ $roleValue }}" @selected($currentRole === $roleValue)>{{ $roleLabel }}</option>
                @endforeach
            </select>
            <div class="mt-1 text-xs text-slate-500">Switch between Student / Creator / Admin / Guest.</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Creator plan</label>
            <select name="plan_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                <option value="">No plan (default)</option>
                @foreach ($plans ?? [] as $plan)
                    <option value="{{ $plan->id }}" @selected(old('plan_id', $user->plan_id) == $plan->id)>
                        {{ $plan->name }} ({{ $plan->durationLabel() }}){{ $plan->price_label ? ' — ' . $plan->price_label : '' }}{{ $plan->is_default ? ' (default)' : '' }}
                    </option>
                @endforeach
            </select>
            <div class="mt-1 text-xs text-slate-500">For creators: limits on quizzes, batches, AI usage.</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Student plan</label>
            <select name="student_plan_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                <option value="">No plan</option>
                @foreach ($studentPlans ?? [] as $sp)
                    <option value="{{ $sp->id }}" @selected(old('student_plan_id', $user->student_plan_id) == $sp->id)>
                        {{ $sp->name }} ({{ $sp->durationLabel() }}){{ $sp->price_label ? ' — ' . $sp->price_label : '' }}
                    </option>
                @endforeach
            </select>
            <div class="mt-1 text-xs text-slate-500">Subscription tier for students (Free, Premium, etc.).</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Guest user</div>
                    <div class="text-sm text-slate-600">Marks this user as a guest (limited).</div>
                </div>
                <label class="inline-flex items-center gap-2">
                    <input type="hidden" name="is_guest" value="0">
                    <input type="checkbox" name="is_guest" value="1"
                           class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                           @checked((bool) $user->is_guest)>
                    <span class="text-sm font-medium text-slate-700">Guest</span>
                </label>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Profile</div>

            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Bio</label>
                    <textarea name="bio" rows="3"
                              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                              placeholder="optional">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Coaching center</label>
                    <input name="coaching_center_name" value="{{ old('coaching_center_name', $user->coaching_center_name) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">City</label>
                    <input name="coaching_city" value="{{ old('coaching_city', $user->coaching_city) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Contact</label>
                    <input name="coaching_contact" value="{{ old('coaching_contact', $user->coaching_contact) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Website</label>
                    <input name="coaching_website" value="{{ old('coaching_website', $user->coaching_website) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>

                @php $links = (array) ($user->social_links ?? []); @endphp
                <div>
                    <label class="block text-sm font-medium text-slate-700">Social: Website</label>
                    <input name="social_links[website]" value="{{ old('social_links.website', $links['website'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Social: YouTube</label>
                    <input name="social_links[youtube]" value="{{ old('social_links.youtube', $links['youtube'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Social: Instagram</label>
                    <input name="social_links[instagram]" value="{{ old('social_links.instagram', $links['instagram'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Social: Telegram</label>
                    <input name="social_links[telegram]" value="{{ old('social_links.telegram', $links['telegram'] ?? '') }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Block user</div>
                    <div class="text-sm text-slate-600">Blocked users can’t log in.</div>
                </div>
                <label class="inline-flex items-center gap-2">
                    <input type="hidden" name="is_blocked" value="0">
                    <input type="checkbox" name="is_blocked" value="1"
                           class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                           @checked((bool) $user->blocked_at)>
                    <span class="text-sm font-medium text-slate-700">Blocked</span>
                </label>
            </div>

            <div class="mt-3">
                <label class="block text-sm font-medium text-slate-700">Reason (optional)</label>
                <input name="blocked_reason" value="{{ old('blocked_reason', $user->blocked_reason) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="e.g. spam / abuse / fake account">
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="text-sm font-semibold text-slate-900">Advanced</div>
            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Google ID</label>
                    <input name="google_id" value="{{ old('google_id', $user->google_id) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Google avatar URL</label>
                    <input name="google_avatar_url" value="{{ old('google_avatar_url', $user->google_avatar_url) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Avatar path</label>
                    <input name="avatar_path" value="{{ old('avatar_path', $user->avatar_path) }}"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                </div>
            </div>
        </div>

        {{-- Sticky action bar so Save is always reachable --}}
        <div class="sticky bottom-0 -mx-5 -mb-4 mt-4 border-t border-slate-200 bg-white px-5 py-4">
            <div class="flex items-center justify-end gap-2">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>

