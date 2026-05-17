/**
 * crazygames.js  —  CrazyGames SDK v3 integration for Ski Manager
 *
 * Implements the "In-game account (full)" scenario:
 *   - Detect whether we're running inside the CrazyGames iframe
 *   - Auto-login / auto-register users via server-side JWT verification
 *   - Hide the regular login form inside the CG context
 *   - Listen for guests logging in during gameplay
 *   - Show a non-blocking "Login with CrazyGames" button for guests
 */
(function () {
    'use strict';

    // ----------------------------------------------------------------
    //  Config
    // ----------------------------------------------------------------
    var VERIFY_URL = (window.Settings && Settings.base_url)
        ? Settings.base_url + 'crazygames_controller/verify_token'
        : '/crazygames_controller/verify_token';

    // ----------------------------------------------------------------
    //  CSS injected when we detect the CG context
    // ----------------------------------------------------------------
    var CG_STYLES = [
        /* Hide username/password form and Google sign-in inside CG iframe */
        '.cg-context #login_form_wrapper { display: none !important; }',
        '.cg-context .google-signin-btn  { display: none !important; }',
        '.cg-context .register-link      { display: none !important; }',
        /* CG login button — small, top-right, non-blocking */
        '#cg-login-btn {',
        '  position: fixed; top: 12px; right: 12px; z-index: 9999;',
        '  background: #ff6a00; color: #fff; border: none;',
        '  padding: 6px 14px; border-radius: 6px; font-size: 13px;',
        '  cursor: pointer; display: none;',
        '}',
        '.cg-context #cg-login-btn { display: block; }',
        '.cg-context.cg-logged-in #cg-login-btn { display: none; }',
        /* Loading overlay shown before auto-login reload to prevent flash */
        '#cg-loading {',
        '  position: fixed; inset: 0; background: rgba(0,0,0,0.7);',
        '  display: flex; align-items: center; justify-content: center;',
        '  color: #fff; font-size: 16px; z-index: 99999;',
        '}',
    ].join('\n');

    function injectStyles() {
        var s = document.createElement('style');
        s.textContent = CG_STYLES;
        document.head.appendChild(s);
    }

    function showLoadingOverlay() {
        var el = document.createElement('div');
        el.id = 'cg-loading';
        el.textContent = 'Logging you in\u2026';
        document.body.appendChild(el);
    }

    // ----------------------------------------------------------------
    //  Login button
    // ----------------------------------------------------------------
    function addLoginButton(SDK) {
        if (document.getElementById('cg-login-btn')) return;
        var btn = document.createElement('button');
        btn.id          = 'cg-login-btn';
        btn.textContent = 'Login with CrazyGames';
        btn.addEventListener('click', function () {
            btn.disabled = true;
            SDK.user.showAuthPrompt()
                .then(function (user) {
                    if (!user) { btn.disabled = false; return; }
                    return SDK.user.getUserToken()
                        .then(function (token) {
                            if (!token) return;
                            showLoadingOverlay();
                            verifyAndLogin(token, function (ok) {
                                if (ok) {
                                    window.location.reload();
                                } else {
                                    document.getElementById('cg-loading').remove();
                                    btn.disabled = false;
                                }
                            });
                        });
                })
                .catch(function () { btn.disabled = false; });
        });
        document.body.appendChild(btn);
    }

    // ----------------------------------------------------------------
    //  Send JWT to backend
    // ----------------------------------------------------------------
    function verifyAndLogin(token, onDone) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', VERIFY_URL, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.timeout = 15000;
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) { if (onDone) onDone(true); return; }
                } catch (e) { /* fall through */ }
            }
            if (onDone) onDone(false);
        };
        xhr.onerror = xhr.ontimeout = function () { if (onDone) onDone(false); };
        xhr.send(JSON.stringify({ token: token }));
    }

    // ----------------------------------------------------------------
    //  Auth listener — fires when a guest logs in during gameplay
    // ----------------------------------------------------------------
    function setupAuthListener(SDK) {
        SDK.user.addAuthListener(function (user) {
            if (!user || !user.userId) return;
            SDK.user.getUserToken()
                .then(function (token) {
                    if (!token) return;
                    showLoadingOverlay();
                    verifyAndLogin(token, function (ok) {
                        if (ok) {
                            window.location.reload();
                        } else {
                            document.getElementById('cg-loading').remove();
                        }
                    });
                })
                .catch(function () {});
        });
    }

    // ----------------------------------------------------------------
    //  Main entry point
    // ----------------------------------------------------------------
    function init() {
        if (typeof window.CrazyGames === 'undefined' || !window.CrazyGames.SDK) {
            return;
        }

        var SDK = window.CrazyGames.SDK;

        SDK.init()
            .then(function () {
                // isUserAccountAvailable is false when embedded outside CrazyGames
                // (e.g. normal site visit, other embedders). Only activate CG mode
                // when we are genuinely on the CrazyGames platform.
                if (!SDK.user.isUserAccountAvailable) {
                    return;
                }

                injectStyles();
                document.body.classList.add('cg-context');
                addLoginButton(SDK);
                setupAuthListener(SDK);

                var alreadyLoggedIn = window.Settings && Settings.is_logged_in;

                return SDK.user.getUserToken()
                    .then(function (token) {
                        if (!token) {
                            // Guest — can play without an account
                            return;
                        }

                        if (alreadyLoggedIn) {
                            document.body.classList.add('cg-logged-in');
                            return;
                        }

                        // Logged-in CG user not yet in game session — auto-login
                // Use sessionStorage flag to prevent a reload loop if the session
                // cookie can't be set (e.g. unexpected browser restriction).
                if (sessionStorage.getItem('cg_login_done')) {
                    sessionStorage.removeItem('cg_login_done');
                    // Already tried once — session didn't persist, play as guest
                    return;
                }
                showLoadingOverlay();
                verifyAndLogin(token, function (ok) {
                    if (ok) {
                        sessionStorage.setItem('cg_login_done', '1');
                        window.location.reload();
                    } else {
                        document.getElementById('cg-loading').remove();
                    }
                });
                    })
                    .catch(function () {
                        // userNotAuthenticated — guest, that's fine
                    });
            })
            .catch(function () {
                // SDK.init() failed — not CrazyGames, do nothing
            });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
