@php $docTitle = $docTitle ?? ''; @endphp
<div class="doc-header">
    <div class="header-left">
        <div class="header-logo">
            {{-- Logo: 2 red parallelograms on top, 3 below --}}
            <svg width="56" height="40" viewBox="0 0 56 40" xmlns="http://www.w3.org/2000/svg">
                {{-- Top-left diamond --}}
                <polygon points="0,0 16,0 11,15 0,15" fill="#A00D25" />
                {{-- Top-right diamond --}}
                <polygon points="18,0 34,0 29,15 13,15" fill="#E31E24" />
                {{-- Bottom-left diamond --}}
                <polygon points="0,17 11,17 8,30 0,30" fill="#E31E24" />
                {{-- Bottom-middle diamond --}}
                <polygon points="13,17 29,17 24,30 8,30" fill="#A00D25" />
                {{-- Bottom-right diamond --}}
                <polygon points="31,17 47,17 42,30 26,30" fill="#E31E24" />
            </svg>
        </div>
        <div class="header-brand">
            <div class="brand-name">BAHAR TRADING</div>
            <div class="brand-tag">QUALITY . TRUST . COMMITMENT</div>
        </div>
    </div>
    <div class="header-right">
        <div class="doc-title">{{ $docTitle }}</div>
        <div class="address">
            600 SK Mujib Road<br>
            Wali Mansion, Chowmuhani<br>
            Chittagong, Bangladesh
        </div>
        <div class="contact">
            <span>+8801841050607</span>
            <svg class="icon" width="12" height="12" viewBox="0 0 24 24" fill="#C8102E">
                <path
                    d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
            </svg>
        </div>
        <div class="contact">
            <span>baharglobaltrading@gmail.com</span>
            <svg class="icon" width="12" height="12" viewBox="0 0 24 24" fill="#C8102E">
                <path
                    d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
            </svg>
        </div>
    </div>
</div>

<div class="separator">
    <div class="sep-thick"></div>
    <div class="sep-thin"></div>
</div>
