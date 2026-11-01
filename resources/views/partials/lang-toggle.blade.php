{{-- Toggle bahasa berupa tautan /id dan /en (SEO-friendly, tanpa JS) --}}
<div class="lang-toggle" role="group" aria-label="{{ __('portfolio.switch_lang') }}">
  <a class="lang-option {{ $locale === 'en' ? 'active' : '' }}" href="{{ route('home', 'en') }}" hreflang="en">EN</a>
  <span class="lang-sep">|</span>
  <a class="lang-option {{ $locale === 'id' ? 'active' : '' }}" href="{{ route('home', 'id') }}" hreflang="id">ID</a>
</div>
