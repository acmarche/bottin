@if(config('app.ga_tracking'))
    <div
        x-data="{
            show: false,
            accepted: false,
            init() {
                this.accepted = document.cookie.includes('cookie_consent=accepted');
                this.show = !this.accepted;
                if (this.accepted) {
                    this.loadGA();
                }
            },
            accept() {
                document.cookie = 'cookie_consent=accepted; path=/; max-age=' + (365 * 24 * 60 * 60) + '; SameSite=Lax';
                this.accepted = true;
                this.show = false;
                this.loadGA();
            },
            decline() {
                document.cookie = 'cookie_consent=declined; path=/; max-age=' + (365 * 24 * 60 * 60) + '; SameSite=Lax';
                this.show = false;
            },
            loadGA() {
                if (document.getElementById('ga-script')) return;
                const id = '{{ config('app.ga_tracking') }}';
                const script = document.createElement('script');
                script.id = 'ga-script';
                script.async = true;
                script.src = 'https://www.googletagmanager.com/gtag/js?id=' + id;
                document.head.appendChild(script);
                window.dataLayer = window.dataLayer || [];
                window.gtag = function() { dataLayer.push(arguments); };
                gtag('js', new Date());
                gtag('config', id);
            }
        }"
    >
        <div
            x-show="show"
            x-transition
            x-cloak
            class="fixed bottom-0 left-0 right-0 z-50 border-t border-slate-200 bg-white p-4 shadow-lg"
        >
            <div class="mx-auto flex max-w-7xl flex-col items-center gap-4 sm:flex-row sm:justify-between">
                <p class="text-base text-slate-600">
                    Ce site utilise des cookies pour analyser le trafic via Google Analytics.
                </p>
                <div class="flex gap-3">
                    <button
                        x-on:click="decline()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-base font-medium text-slate-600 transition hover:bg-slate-100"
                    >
                        Refuser
                    </button>
                    <button
                        x-on:click="accept()"
                        class="rounded-lg bg-stormy-teal-600 px-4 py-2 text-base font-medium text-white transition hover:bg-stormy-teal-700"
                    >
                        Accepter
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
