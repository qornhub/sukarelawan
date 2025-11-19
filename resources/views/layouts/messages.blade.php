{{-- Success --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Error --}}
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-2">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Warning --}}
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mt-2">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Info (optional use) --}}
@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show mt-2">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-2">
        <strong>There were some problems with your submission:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
