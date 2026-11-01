@php
    // Ambil teks sesuai bahasa aktif; string biasa dikembalikan apa adanya.
    $t = fn ($v) => is_array($v) ? $v[$locale] : $v;
    $imgSrc = fn ($v) => str_starts_with($v, 'http') ? $v : asset('img/'.$v);
    $cv = asset('assets/Reno-CV.pdf');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ __('portfolio.title') }}</title>
  <meta name="description" content="{{ __('portfolio.description') }}" />
  <link rel="canonical" href="{{ route('home', $locale) }}" />
  @foreach ($data['locales'] as $alt)
    <link rel="alternate" hreflang="{{ $alt }}" href="{{ route('home', $alt) }}" />
  @endforeach
  <meta property="og:type" content="website" />
  <meta property="og:title" content="{{ __('portfolio.title') }}" />
  <meta property="og:description" content="{{ __('portfolio.description') }}" />
  <meta property="og:image" content="{{ asset('img/foto.png') }}" />
  <meta property="og:url" content="{{ route('home', $locale) }}" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Mono:wght@400;500&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/interactions.css') }}" />
  <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
</head>
<body>

  {{-- INTRO SPLASH --}}
  <div id="intro" role="dialog" aria-label="Intro animation">
    <span id="intro-word" class="intro-word"></span>
    <span id="intro-lang" class="intro-lang"></span>
    <div class="intro-bar-wrap"><div id="intro-bar" class="intro-bar"></div></div>
  </div>

  <div class="hero-grid-bg" aria-hidden="true"></div>
  <div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>

  {{-- NAVIGATION --}}
  <nav>
    <a class="nav-logo" href="#hero" style="font-size: 30px">RN</a>

    <ul class="nav-links">
      <li><a href="#hero"><i class="fa-solid fa-house"></i><span>{{ __('portfolio.nav.home') }}</span></a></li>
      <li><a href="#about"><i class="fa-regular fa-user"></i><span>{{ __('portfolio.nav.about') }}</span></a></li>
      <li><a href="#tools"><i class="fa-solid fa-code"></i><span>{{ __('portfolio.nav.skills') }}</span></a></li>
      <li><a href="#experience"><i class="fa-solid fa-briefcase"></i><span>{{ __('portfolio.nav.experience') }}</span></a></li>
      <li><a href="#projects"><i class="fa-regular fa-folder"></i><span>{{ __('portfolio.nav.projects') }}</span></a></li>
      <li><a href="#certificates"><i class="fa-solid fa-award"></i><span>{{ __('portfolio.nav.certificates') }}</span></a></li>
      <li><a href="#contact"><i class="fa-regular fa-envelope"></i><span>{{ __('portfolio.nav.contact') }}</span></a></li>
    </ul>

    <a class="nav-cta" href="#contact">{{ __('portfolio.nav.cta') }}</a>

    @include('partials.lang-toggle')

    <button class="theme-toggle" id="themeToggle" aria-label="{{ __('portfolio.toggle_theme') }}">
      <i class="fa-solid fa-moon"></i>
    </button>
  </nav>

  <div class="lang-toggle-mobile-wrap">
    @include('partials.lang-toggle')
  </div>

  <div class="theme-toggle-mobile-wrap">
    <button class="theme-toggle" id="themeToggleMobile" aria-label="{{ __('portfolio.toggle_theme') }}">
      <i class="fa-solid fa-moon"></i>
    </button>
  </div>

  {{-- HERO --}}
  <section id="hero">
    <div class="hero-glow" aria-hidden="true"></div>

    <div class="hero-content">
      <div class="hero-left">
        <div class="hero-badge fade-up">
          <span class="badge-dot" aria-hidden="true"></span>
          <span>{{ __('portfolio.hero.badge') }}</span>
        </div>

        <h1 class="fade-up delay-1">
          <span>{{ __('portfolio.hero.h1_a') }}</span><br><em>{{ __('portfolio.hero.h1_b') }}</em>
        </h1>

        <div class="hero-typed fade-up delay-2" aria-label="{{ implode(', ', __('portfolio.hero.roles')) }}">
          <span class="typed-prompt" aria-hidden="true">&gt;</span>
          <span id="typedText" data-roles="{{ json_encode(__('portfolio.hero.roles')) }}">{{ __('portfolio.hero.roles')[0] }}</span><span class="typed-caret" aria-hidden="true"></span>
        </div>

        <p class="hero-desc fade-up delay-2">{!! __('portfolio.hero.desc') !!}</p>

        <div class="hero-socials fade-up delay-3">
          @foreach ($data['socials'] as $s)
            <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer" data-tooltip="{{ $s['label'] }}" aria-label="{{ $s['label'] }}">
              <i class="{{ $s['icon'] }}"></i>
            </a>
          @endforeach
          <a href="{{ $cv }}" download data-tooltip="{{ __('portfolio.hero.download_cv') }}" aria-label="{{ __('portfolio.hero.download_cv') }}">
            <i class="fas fa-file-pdf"></i>
          </a>
        </div>

        <div class="hero-actions fade-up delay-3">
          <a href="#projects" class="btn-primary">{{ __('portfolio.hero.view_projects') }}</a>
          <a href="#contact" class="btn-ghost">{{ __('portfolio.hero.contact_me') }}</a>
        </div>
      </div>

      {{-- ID Card Profile --}}
      <div class="hero-right pending-3d fade-up delay-2">
        @php
          $card3d = [
              'org' => __('portfolio.card.org'), 'status' => __('portfolio.card.status'),
              'available' => __('portfolio.card.available'), 'location' => __('portfolio.card.location'),
              'role' => __('portfolio.card.role'), 'roleValue' => __('portfolio.card.role_value'),
              'photo' => asset('img/foto.png'),
              'model' => asset('assets/kartu.glb'),
          ];
        @endphp
        {{-- ID card 3D (Three.js). Kartu HTML di bawah jadi fallback bila WebGL tidak ada. --}}
        <div id="idcard3d" class="id-3d" data-card="{{ json_encode($card3d) }}"></div>
        {{-- Kartu HTML disembunyikan selama 3D dimuat (.pending-3d). Tanpa WebGL, atau bila 3D tak kunjung siap (15 dtk), tampilkan kartu HTML. --}}
        <script>
          (function (box) {
            var gl = false;
            try { var c = document.createElement('canvas'); gl = !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl'))); } catch (e) {}
            var show = function () { box.classList.remove('pending-3d'); };
            if (!gl) return show();
            setTimeout(function () { if (!box.classList.contains('has-3d')) show(); }, 15000);
          })(document.currentScript.parentElement);
        </script>
        <div class="id-card">
          <div class="id-card-lanyard">
            <div class="lanyard-strap"></div>
            <div class="lanyard-clip"></div>
          </div>
          <div class="id-card-body">
            <div class="id-card-header">
              <div class="id-card-org">{{ __('portfolio.card.org') }}</div>
              <div class="id-card-logo">reno</div>
            </div>
            <div class="id-card-photo-wrap">
              <div class="id-card-photo">
                <img src="{{ asset('img/foto.png') }}" alt="Foto Reno" width="400" height="400" />
              </div>
            </div>
            <div class="id-card-info">
              <div class="id-card-name">Reno</div>
              <div class="id-card-title">Indomaret Group</div>
              <div class="id-card-divider"></div>
              <div class="id-card-details">
                <div class="id-detail"><span class="id-detail-label">{{ __('portfolio.card.status') }}</span><span class="id-status-dot"></span><span class="id-detail-val">{{ __('portfolio.card.available') }}</span></div>
                <div class="id-detail"><span class="id-detail-label">{{ __('portfolio.card.location') }}</span><span class="id-detail-val">Jakarta, Indonesia</span></div>
                <div class="id-detail"><span class="id-detail-label">{{ __('portfolio.card.role') }}</span><span class="id-detail-val">{{ __('portfolio.card.role_value') }}</span></div>
              </div>
              <div class="id-card-barcode">
                <div class="barcode-lines">
                  @for ($i = 0; $i < 15; $i++)<span></span>@endfor
                </div>
                <div class="barcode-text">RENO-DA-2026</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  {{-- ABOUT --}}
  <section id="about">
    <div class="about-hero-wrap">
      <div class="about-photo-col fade-up">
        <div class="about-photo-frame">
          <img src="{{ asset('img/profilaboutme1.png') }}" alt="Reno" class="about-photo-img" loading="lazy" />
        </div>
      </div>

      <div class="about-content-col fade-up delay-1">
        <div class="about-title-wrap">
          <div class="about-iam-row">
            <span class="about-iam">{{ __('portfolio.about.iam') }}</span>
            <span class="about-name-badge">RENO</span>
          </div>
          <div class="about-role-row">
            <span class="about-role-badge">{{ __('portfolio.about.role') }}</span>
          </div>
        </div>

        <div class="about-divider-line"></div>

        <div class="about-body-text">
          <p>{!! __('portfolio.about.p1') !!}</p>
          <p>{!! __('portfolio.about.p2') !!}</p>
        </div>

        <a href="{{ $cv }}" download class="about-cv-btn">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          <span>{{ __('portfolio.about.cv') }}</span>
        </a>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  {{-- SKILLS MARQUEE --}}
  <section id="tools">
    <div class="section-wrap">
      <h2 class="section-title fade-up delay-1">{{ __('portfolio.skills.title_a') }} <em>{{ __('portfolio.skills.title_b') }}</em>.</h2>
      <p class="section-sub fade-up delay-2">{{ __('portfolio.skills.sub') }}</p>
    </div>

    @foreach (['row1' => ['track-left', 'delay-3', ''], 'row2' => ['track-right', 'delay-4', 'margin-top:1rem;']] as $row => [$trackClass, $delay, $style])
      <div class="marquee-wrapper fade-up {{ $delay }}" @if ($style) style="{{ $style }}" @endif>
        <div class="marquee-track {{ $trackClass }}">
          {{-- Set kedua adalah duplikat agar loop mulus --}}
          @foreach ([false, true] as $duplicate)
            <div class="marquee-set" @if ($duplicate) aria-hidden="true" @endif>
              @foreach ($data['tools'][$row] as $tool)
                <div class="tool-logo-card">
                  <img src="{{ $imgSrc($tool['img']) }}" alt="{{ $tool['name'] }}" loading="lazy"
                       onerror="this.style.display='none';this.nextElementSibling.style.display='flex'" />
                  <div class="tool-fallback" style="display:none">{{ $tool['fallback'] }}</div>
                  <span>{{ $tool['name'] }}</span>
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </section>

  <div class="divider"></div>

  {{-- EXPERIENCE --}}
  <section id="experience">
    <div class="section-wrap">
      <h2 class="section-title fade-up delay-1">{{ __('portfolio.experience.title_a') }} <em>{{ __('portfolio.experience.title_b') }}</em>.</h2>
      <p class="section-sub fade-up delay-2">{{ __('portfolio.experience.sub') }}</p>

      <div class="timeline fade-up delay-3">
        @foreach ($data['experience'] as $exp)
          <div class="timeline-item">
            <div class="timeline-dot"></div>
            @unless ($loop->last)<div class="timeline-line"></div>@endunless
            <div class="timeline-card">
              <div class="timeline-header">
                <div>
                  <div class="timeline-role">{{ $t($exp['role']) }}</div>
                  <div class="timeline-company">{{ $exp['company'] }}</div>
                </div>
                <div class="timeline-period">{{ $t($exp['period']) }}</div>
              </div>
              <p class="timeline-desc">{{ $t($exp['desc']) }}</p>
              <div class="timeline-tags">
                @foreach ($exp['tags'] as $tag)<span class="tl-tag">{{ $tag }}</span>@endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <div class="divider"></div>

  {{-- PROJECTS --}}
  <section id="projects">
    <div class="section-wrap">
      <h2 class="section-title fade-up delay-1"><span>{{ __('portfolio.projects.title_a') }}</span><br><em>{{ __('portfolio.projects.title_b') }}</em></h2>
      <p class="section-sub fade-up delay-2">{{ __('portfolio.projects.sub') }}</p>

      @php $cats = collect($data['projects'])->pluck('cats')->flatten()->unique()->values(); @endphp
      <div class="project-filters fade-up delay-3" role="group">
        <button type="button" class="filter-chip active" data-filter="all">{{ __('portfolio.projects.filter_all') }}</button>
        @foreach ($cats as $cat)
          <button type="button" class="filter-chip" data-filter="{{ $cat }}">{{ $cat }}</button>
        @endforeach
      </div>

      <div class="projects-grid fade-up delay-3">
        @foreach ($data['projects'] as $p)
          <article class="project-card tilt" data-cats="{{ implode(',', $p['cats']) }}">
            <div class="project-header">
              <div class="project-icon" aria-hidden="true"><i class="{{ $p['icon'] }}"></i></div>
              <div class="project-tags">
                @foreach ($p['tags'] as [$label, $color])
                  <span class="project-tag tag-{{ $color }}">{{ $label }}</span>
                @endforeach
              </div>
            </div>
            <h3 class="project-name">{{ $t($p['name']) }}</h3>
            <p class="project-desc">{{ $t($p['desc']) }}</p>
            <div class="project-meta">
              <div class="project-status">
                <div class="status-dot {{ $p['done'] ? '' : 'wip' }}"></div>
                {{ __($p['done'] ? 'portfolio.projects.done' : 'portfolio.projects.wip') }}
              </div>
              <span>{{ $p['stack'] }}</span>
            </div>
          </article>
        @endforeach

        <div class="project-card add-card" role="button" tabindex="0" aria-label="{{ __('portfolio.projects.more_aria') }}">
          <div class="add-card-inner">
            <div class="add-card-plus" aria-hidden="true">+</div>
            <div>{{ __('portfolio.projects.more') }}</div>
            <div class="add-card-sub">{{ __('portfolio.projects.more_sub') }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  {{-- CERTIFICATES --}}
  <section id="certificates">
    <div class="section-wrap">
      <h2 class="section-title fade-up delay-1">{!! __('portfolio.certificates.title_a') !!} <em>{{ __('portfolio.certificates.title_b') }}</em>.</h2>
      <p class="section-sub fade-up delay-2">{{ __('portfolio.certificates.sub') }}</p>

      <div class="cert-grid fade-up delay-3">
        @foreach ($data['certificates'] as $c)
          <div class="cert-card tilt">
            <div class="cert-img-wrap" data-lightbox role="button" tabindex="0" aria-label="{{ $t($c['name']) }}">
              <img src="{{ asset('img/'.$c['img']) }}" alt="{{ $t($c['name']) }}" loading="lazy">
            </div>
            <div class="cert-info">
              <div class="cert-name">{{ $t($c['name']) }}</div>
              <div class="cert-issuer">{{ $c['issuer'] }}</div>
              <div class="cert-date">{{ $c['year'] }}</div>
            </div>
          </div>
        @endforeach

        <div class="cert-card cert-add-card" aria-label="{{ __('portfolio.certificates.more_aria') }}">
          <div class="add-card-inner">
            <div class="add-card-plus" aria-hidden="true">+</div>
            <div>{{ __('portfolio.certificates.more') }}</div>
            <div class="add-card-sub">{{ __('portfolio.certificates.more_sub') }}</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  {{-- CONTACT --}}
  <section id="contact">
    <div class="contact-container fade-up">
      <div class="contact-left">
        <h2>{{ __('portfolio.contact.title') }}</h2>
        <p>{{ __('portfolio.contact.text') }}</p>

        <div class="contact-info">
          <span aria-hidden="true"><i class="fa-regular fa-envelope"></i></span>
          <div>
            <small>{{ __('portfolio.contact.email') }}</small>
            <strong>{{ $data['contact_email'] }}</strong>
          </div>
        </div>

        <div class="contact-info">
          <span aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
          <div>
            <small>{{ __('portfolio.contact.location') }}</small>
            <strong>Serang, Indonesia</strong>
          </div>
        </div>
      </div>

      <form class="contact-form" id="contactForm" method="POST" action="{{ route('contact.store') }}"
            data-err-send="{{ __('portfolio.contact.err_send') }}" data-err-conn="{{ __('portfolio.contact.err_conn') }}">
        @csrf
        {{-- Honeypot anti-spam --}}
        <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true"
               style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">

        <label for="contact-name">{{ __('portfolio.contact.name') }}</label>
        <input type="text" id="contact-name" name="name" maxlength="100" required placeholder="{{ __('portfolio.contact.name_ph') }}">

        <label for="contact-email">{{ __('portfolio.contact.email_label') }}</label>
        <input type="email" id="contact-email" name="email" maxlength="150" required placeholder="name@email.com">

        <label for="contact-message">{{ __('portfolio.contact.message') }}</label>
        <textarea id="contact-message" name="message" maxlength="3000" required placeholder="{{ __('portfolio.contact.message_ph') }}"></textarea>

        <button type="submit" id="sendBtn">{{ __('portfolio.contact.send') }}</button>
      </form>
    </div>
  </section>

  <footer>
    <span class="footer-name">Reno</span>
    &nbsp;·&nbsp;
    <span>{{ __('portfolio.footer') }}</span>
    &nbsp;·&nbsp;
    {{ date('Y') }}
  </footer>

  <button type="button" class="to-top" id="toTop" aria-label="{{ __('portfolio.to_top') }}"><i class="fa-solid fa-arrow-up"></i></button>

  <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" hidden>
    <button type="button" class="lightbox-close" aria-label="{{ __('portfolio.close') }}">&times;</button>
    <img src="" alt="" id="lightboxImg">
    <div class="lightbox-caption" id="lightboxCap"></div>
  </div>

  <div id="toast" class="toast">{{ __('portfolio.contact.sent') }}</div>

  <script src="{{ asset('js/script.js') }}"></script>
  <script src="{{ asset('js/interactions.js') }}"></script>
  <script src="{{ asset('js/i18n-switch.js') }}"></script>
  @vite('resources/js/idcard.js')
</body>
</html>
