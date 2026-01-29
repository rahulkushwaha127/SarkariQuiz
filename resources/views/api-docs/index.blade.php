<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ __('API Documentation') }}</h2>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-slate-400">Base URL:</span>
                <code class="px-2 py-1 bg-gray-100 dark:bg-slate-800 rounded text-sm font-mono">{{ $baseUrl }}</code>
            </div>
        </div>
    </x-slot>

    <div x-data="apiDocs()" class="space-y-6">
        
        <!-- Quick Navigation -->
        <div class="card">
            <div class="flex flex-wrap gap-2 p-2">
                <button @click="activeSection = 'authentication'" 
                    :class="activeSection === 'authentication' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Authentication
                </button>
                <button @click="activeSection = 'profile'" 
                    :class="activeSection === 'profile' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Profile
                </button>
                <button @click="activeSection = 'teams'" 
                    :class="activeSection === 'teams' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Teams
                </button>
                <button @click="activeSection = 'billing'" 
                    :class="activeSection === 'billing' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Billing
                </button>
                <button @click="activeSection = 'plans'" 
                    :class="activeSection === 'plans' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Plans
                </button>
                <button @click="activeSection = 'dashboard'" 
                    :class="activeSection === 'dashboard' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Dashboard
                </button>
            </div>
        </div>

        <!-- Token Generator & Input -->
        <div class="card bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
            <div class="space-y-4">
                <!-- Generate Token Section -->
                <div class="pb-4 border-b border-primary-200 dark:border-primary-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Generate New API Token</h3>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            x-model="tokenName"
                            placeholder="Token name (e.g., Test Token)"
                            class="flex-1 px-3 py-2 rounded-lg border bg-white dark:bg-slate-800 text-gray-900 dark:text-white text-sm"
                        >
                        <button 
                            @click="generateToken()"
                            :disabled="generatingToken"
                            class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-medium transition-colors"
                        >
                            <span x-show="!generatingToken">Generate Token</span>
                            <span x-show="generatingToken">Generating...</span>
                        </button>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-slate-400 mt-2">
                        This will create a new API token for <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})
                    </p>
                </div>

                <!-- Token Input Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                        {{ __('API Token') }}
                        <span class="text-xs text-gray-500 dark:text-slate-400">(for testing authenticated endpoints)</span>
                    </label>
                    <div class="flex gap-2">
                        <input 
                            type="text" 
                            x-model="token"
                            @input="localStorage.setItem('api_token', $event.target.value)"
                            placeholder="Enter your API token here or generate one above..."
                            class="flex-1 px-3 py-2 rounded-lg border bg-white dark:bg-slate-800 text-gray-900 dark:text-white font-mono text-sm"
                        >
                        <button 
                            @click="copyToClipboard(token)"
                            x-show="token"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium transition-colors"
                            title="Copy token"
                        >
                            Copy
                        </button>
                        <button 
                            @click="token = ''; localStorage.removeItem('api_token')"
                            x-show="token"
                            class="px-4 py-2 text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white border border-gray-300 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authentication Section -->
        <div x-show="activeSection === 'authentication'" class="space-y-4">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Authentication</h3>
                
                <!-- Register -->
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Register</h4>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-medium">POST</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/auth/register</code>
                    <div class="bg-gray-50 dark:bg-slate-900 rounded-lg p-4 mb-3">
                        <pre class="text-xs overflow-x-auto"><code class="language-json">{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}</code></pre>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('POST', '/auth/register', {'Content-Type': 'application/json'}, {name: 'John Doe', email: 'john@example.com', password: 'password123', password_confirmation: 'password123'}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <!-- Login -->
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Login</h4>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-medium">POST</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/auth/login</code>
                    <div class="bg-gray-50 dark:bg-slate-900 rounded-lg p-4 mb-3">
                        <pre class="text-xs overflow-x-auto"><code class="language-json">{
  "email": "john@example.com",
  "password": "password123"
}</code></pre>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('POST', '/auth/login', {'Content-Type': 'application/json'}, {email: 'john@example.com', password: 'password123'}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <!-- Get User -->
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Authenticated User</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/auth/user</code>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 mb-3">
                        <p class="text-xs text-yellow-800 dark:text-yellow-200">Requires authentication token</p>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/auth/user', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <!-- Logout -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Logout</h4>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-medium">POST</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/auth/logout</code>
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 mb-3">
                        <p class="text-xs text-yellow-800 dark:text-yellow-200">Requires authentication token</p>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('POST', '/auth/logout', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div x-show="activeSection === 'profile'" x-cloak class="space-y-4">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile</h3>
                
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Profile</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/profile</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/profile', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Update Profile</h4>
                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-xs font-medium">PUT</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/profile</code>
                    <div class="bg-gray-50 dark:bg-slate-900 rounded-lg p-4 mb-3">
                        <pre class="text-xs overflow-x-auto"><code class="language-json">{
  "name": "John Updated",
  "email": "john.updated@example.com"
}</code></pre>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('PUT', '/profile', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE'), 'Content-Type': 'application/json'}, {name: 'John Updated', email: 'john.updated@example.com'}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Update Password</h4>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-medium">POST</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/profile/password</code>
                    <div class="bg-gray-50 dark:bg-slate-900 rounded-lg p-4 mb-3">
                        <pre class="text-xs overflow-x-auto"><code class="language-json">{
  "current_password": "oldpassword",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}</code></pre>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('POST', '/profile/password', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE'), 'Content-Type': 'application/json'}, {current_password: 'oldpassword', password: 'newpassword123', password_confirmation: 'newpassword123'}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>
            </div>
        </div>

        <!-- Teams Section -->
        <div x-show="activeSection === 'teams'" x-cloak class="space-y-4">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Teams</h3>
                
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">List Teams</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/teams</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/teams', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Create Team</h4>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-medium">POST</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/teams</code>
                    <div class="bg-gray-50 dark:bg-slate-900 rounded-lg p-4 mb-3">
                        <pre class="text-xs overflow-x-auto"><code class="language-json">{
  "name": "My Team",
  "description": "Team description",
  "website": "https://example.com"
}</code></pre>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('POST', '/teams', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE'), 'Content-Type': 'application/json'}, {name: 'My Team', description: 'Team description'}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Team Members</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/teams/{id}/members</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/teams/1/members', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div x-show="activeSection === 'billing'" x-cloak class="space-y-4">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Billing</h3>
                
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Billing Info</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/billing</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/billing', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Subscription</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/billing/subscription</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/billing/subscription', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Subscribe to Plan</h4>
                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs font-medium">POST</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/billing/subscribe</code>
                    <div class="bg-gray-50 dark:bg-slate-900 rounded-lg p-4 mb-3">
                        <pre class="text-xs overflow-x-auto"><code class="language-json">{
  "plan_code": "basic_monthly",
  "provider": "manual"
}</code></pre>
                    </div>
                    <button @click="copyToClipboard(getCurlCommand('POST', '/billing/subscribe', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE'), 'Content-Type': 'application/json'}, {plan_code: 'basic_monthly', provider: 'manual'}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>
            </div>
        </div>

        <!-- Plans Section -->
        <div x-show="activeSection === 'plans'" x-cloak class="space-y-4">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Plans</h3>
                
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">List Plans</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded text-xs font-medium">Public</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/plans</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/plans'))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Plan Details</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded text-xs font-medium">Public</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/plans/{id}</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/plans/1'))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Current Plan</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/plans/current</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/plans/current', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>
            </div>
        </div>

        <!-- Dashboard Section -->
        <div x-show="activeSection === 'dashboard'" x-cloak class="space-y-4">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dashboard</h3>
                
                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Dashboard Overview</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/dashboard</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/dashboard', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">Get Dashboard Statistics</h4>
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs font-medium">GET</span>
                    </div>
                    <code class="block mb-2 text-sm text-gray-600 dark:text-slate-400">{{ $baseUrl }}/dashboard/stats</code>
                    <button @click="copyToClipboard(getCurlCommand('GET', '/dashboard/stats', {'Authorization': 'Bearer ' + (token || 'YOUR_TOKEN_HERE')}))" 
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline cursor-pointer">
                        Copy cURL
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1">
                    <h4 class="font-medium text-blue-900 dark:text-blue-200 mb-1">API Information</h4>
                    <ul class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
                        <li>• All endpoints require <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">Authorization: Bearer {token}</code> header (except public endpoints)</li>
                        <li>• Rate Limits: Public (60/min), Auth (10/min), Protected (120/min)</li>
                        <li>• Base URL: <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">{{ $baseUrl }}</code></li>
                        <li>• All responses are in JSON format</li>
                        <li>• Get your token by logging in via <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">/auth/login</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function apiDocs() {
            return {
                activeSection: 'authentication',
                token: localStorage.getItem('api_token') || '',
                baseUrl: '{{ $baseUrl }}',
                generatingToken: false,
                tokenName: 'Test Token ' + new Date().toLocaleDateString(),
                copyToClipboard(text) {
                    // Handle both string and function that returns string
                    const textToCopy = typeof text === 'function' ? text() : text;
                    
                    // Fix escaped newlines for proper copy
                    const cleanText = textToCopy.replace(/\\n/g, '\n').replace(/\\'/g, "'");
                    
                    if (!navigator.clipboard) {
                        // Fallback for older browsers
                        const textarea = document.createElement('textarea');
                        textarea.value = cleanText;
                        textarea.style.position = 'fixed';
                        textarea.style.opacity = '0';
                        document.body.appendChild(textarea);
                        textarea.select();
                        try {
                            document.execCommand('copy');
                            if (window.toast) {
                                window.toast('Copied to clipboard!', 'success');
                            } else {
                                alert('Copied to clipboard!');
                            }
                        } catch (err) {
                            if (window.toast) {
                                window.toast('Failed to copy', 'error');
                            }
                        }
                        document.body.removeChild(textarea);
                        return;
                    }
                    
                    navigator.clipboard.writeText(cleanText).then(() => {
                        if (window.toast) {
                            window.toast('Copied to clipboard!', 'success');
                        } else {
                            alert('Copied to clipboard!');
                        }
                    }).catch((err) => {
                        console.error('Failed to copy:', err);
                        if (window.toast) {
                            window.toast('Failed to copy', 'error');
                        } else {
                            alert('Failed to copy to clipboard');
                        }
                    });
                },
                getCurlCommand(method, endpoint, headers = {}, data = null) {
                    let curl = `curl -X ${method} ${this.baseUrl}${endpoint}`;
                    
                    // Add headers
                    Object.entries(headers).forEach(([key, value]) => {
                        curl += ` \\\n  -H "${key}: ${value}"`;
                    });
                    
                    // Add data if provided
                    if (data) {
                        curl += ` \\\n  -d '${JSON.stringify(data)}'`;
                    }
                    
                    return curl;
                },
                async generateToken() {
                    if (!this.tokenName.trim()) {
                        if (window.toast) {
                            window.toast('Please enter a token name', 'error');
                        }
                        return;
                    }
                    this.generatingToken = true;
                    try {
                        const response = await fetch('{{ route('admin.api-docs.generate-token') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ name: this.tokenName })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.token = data.data.token;
                            localStorage.setItem('api_token', this.token);
                            if (window.toast) {
                                window.toast('Token generated successfully!', 'success');
                            }
                        } else {
                            if (window.toast) {
                                window.toast(data.message || 'Failed to generate token', 'error');
                            }
                        }
                    } catch (error) {
                        if (window.toast) {
                            window.toast('Error generating token', 'error');
                        }
                    } finally {
                        this.generatingToken = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>

