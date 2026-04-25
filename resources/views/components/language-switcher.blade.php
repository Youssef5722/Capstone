<div class="d-flex align-items-center gap-1">
    <form method="POST" action="{{ route('language.switch') }}" class="d-inline">
        @csrf
        <input type="hidden" name="locale" value="ar">
        <button
            type="submit"
            class="btn btn-sm {{ app()->getLocale() === 'ar' ? 'btn-light' : 'btn-outline-light' }}"
            title="{{ __('cms.lang.arabic') }}"
        >
            {{ __('cms.lang.arabic') }}
        </button>
    </form>

    <form method="POST" action="{{ route('language.switch') }}" class="d-inline">
        @csrf
        <input type="hidden" name="locale" value="en">
        <button
            type="submit"
            class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-light' : 'btn-outline-light' }}"
            title="{{ __('cms.lang.english') }}"
        >
            {{ __('cms.lang.english') }}
        </button>
    </form>
</div>
