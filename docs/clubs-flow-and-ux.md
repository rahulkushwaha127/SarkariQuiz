# Clubs flow – analysis and UX suggestions

## Current flow (as built)

### 1. Join a club
- **Invite:** Admin shares `/clubs/join/{token}`. Guest hits link → login/register → back to same link.
- **Join page:** User sees club name, "Request to join" or "You are already a member" / "Request pending".
- **Admin:** Approves/rejects from club page → "Pending join requests" with Approve/Reject.

### 2. Club home (`/clubs/{id}`)
- **Header:** Club name, owner, your role, "Approval required · Manual points · Master rotation", club status.
- **Admin only:** Invite link (copy), Add member (search).
- **Today’s session:**
  - No session: "No active session" or "Session lobby is open" + member count. **Lobby** button (admin + members).
  - Active session: "Active · Started …", "Current master: X". Admin: Next master, End. Member: **Lobby** or "You're in this session".
- **View last session result** link when there’s an ended session.
- **Members:** Collapsible list (or Live scoreboard when session active with +1 buttons).
- **Admin:** Pending join requests.
- **Back** to clubs list.

### 3. Lobby / session setup (`/clubs/{id}/session`)
- **Session lobby** card + **Back** to club.
- **Your status:** "Loading…" then "You have joined…" or not; **Join session** / **Leave** buttons.
- **Joined members** list (from API).
- **Admin:** **Start session** (disabled until ≥2 joined), tip about removing.

### 4. Active session (same club URL)
- Club page shows Live scoreboard, current master, Next master / End (admin), +1 (master/admin).
- Realtime: session start/end, master change, points via WebSockets.

### 5. After end session
- Redirect to **Session result** (final scoreboard, winner, Back to club).
- Club page shows "View last session result".

---

## Pain points and confusion

1. **Two “sessions” in words:** "Today’s session" vs "Session lobby" – same word, different meaning (lobby vs game session).
2. **Lobby visibility:** "Session lobby is open" only after someone has joined lobby; if admin just opened the tab, members still see "No active session" (correct but can feel empty).
3. **Club page is dense:** Invite link, Add member, Today’s session, Members/scoreboard, Pending requests – a lot for one screen.
4. **No clear “what do I do next”:** New member may not see that they should tap **Lobby** and then **Join session**.
5. **Join flow copy:** "Join session" on lobby page is the real “join”; club page is “go to lobby” – we fixed label to "Lobby" but could reinforce with one short line of copy.
6. **Back vs Lobby:** "Back" at bottom of club page goes to clubs list; **Lobby** is the main CTA – hierarchy could be clearer.
7. **Session result:** One primary CTA "Back to club"; could add "Back to clubs" for consistency.
8. **Empty states:** "No pending requests", "No active session" could be slightly friendlier (e.g. what to do next).

---

## Suggestions (simple to use + better UI/UX)

### A. Copy and hierarchy (quick wins)

- **Club page – Today’s session**
  - When no active session and lobby closed: under "No active session" add one line: *"Open Lobby to start a session or wait for the admin to open it."* (members) / *"Open Lobby to let members join, then start the session."* (admin).
  - When lobby open: keep "Session lobby is open" and "X members in lobby — join to be included." and make **Lobby** the clear primary action (already is).
- **Lobby page**
  - Under "Session lobby" title add one line: *"Tap **Join session** below to be in today’s round. Admin will start when at least 2 have joined."*
- **Session result**
  - Add secondary link: "Back to clubs" next to "Back to club" so users can jump to club list without going via club room.

### B. Simplify club home (progressive disclosure)

- **Invite link (admin):** Keep in header but collapse by default into "Invite members ▼" (expand to show link + copy). Frees space and reduces noise for members (they don’t see it).
- **Add member (admin):** Same idea: "Add member ▼" collapsed by default so the first thing everyone sees is "Today’s session" and **Lobby**.
- **Pending requests (admin):** Collapse by default: "Pending requests (N) ▼" so the main focus is session/lobby.

### C. Clear primary action per screen

- **Club page:** One clear primary CTA: **Lobby** (already). Ensure nothing else competes visually (e.g. "Back" stays secondary).
- **Lobby page:** One primary CTA: **Join session** (when not joined) or **Leave** (when joined). Admin: **Start session** when enabled. "Back" stays secondary.
- **Session result:** Primary = "Back to club"; secondary = "Back to clubs".

### D. States and empty states

- **Club – no session, lobby closed**
  - Member: "No session right now. When the admin opens the lobby, tap **Lobby** to join."
  - Admin: "No session. Tap **Lobby** to open the lobby so members can join, then start the session."
- **Club – lobby open**
  - Already good: "Session lobby is open" + count + **Lobby**.
- **Club – active session**
  - Keep current; ensure "Current master" and Live scoreboard are the focus; **Lobby** for members who haven’t joined yet is correct (they go to lobby and see session started or redirect).

### E. Naming consistency (optional)

- Use **"Lobby"** everywhere for “the waiting room” (club page + any breadcrumb/title).
- Use **"Session"** only for the actual game (active session + result). So: "Today’s session" = the current or last game; "Session lobby" = waiting room. No code change required if we only tighten copy as above.

### F. Mobile / touch

- Ensure **Lobby**, **Join session**, **Start session**, **+1**, **Next master**, **End** are all large enough tap targets (e.g. min 44px height). Current padding already helps; avoid shrinking.
- On session result, ensure "Back to club" and "Back to clubs" are full-width or large buttons on small screens.

### G. Realtime feedback

- When lobby state changes (someone joins/leaves), club page already shows "Session lobby is open" and count (via refresh). Ensure refresh runs on `club.state_changed` so members see updates without reloading.
- When session ends, redirect to result is already in place; consider a short toast on club page: "Session ended" with link "View result" if they didn’t get redirected (e.g. had tab in background).

---

## Implementation priority

1. **High impact, low effort:** A (copy + one line of guidance), C (confirm primary CTAs), D (short empty-state lines).
2. **Medium effort:** B (collapse Invite / Add member / Pending requests by default).
3. **Polish:** Session result "Back to clubs", F (tap targets), G (toast on session end).

If you want, next step can be implementing (1) in the Blade views and one small controller/JS check for (2).
