@php
    // ensure $disabled exists (parent should pass this)
    $disabled = $disabled ?? false;
@endphp

<div class="mt-5 card participant-section" id="section-qr" style="display:none"
     data-disabled="{{ $disabled ? '1' : '0' }}" aria-disabled="{{ $disabled ? 'true' : 'false' }}">
    <div class="card-header">
        <h5 class="card-title">Event QR Code</h5>
    </div>

    <div class="card-body mt-4 text-center d-flex flex-column align-items-center">
        @if($disabled)
            <div class="alert alert-warning w-100 text-center" role="status">
                <strong>Attendance closed</strong><br>
                This event has ended — the attendance QR code is no longer active.
            </div>

            {{-- optional muted placeholder so layout doesn't jump --}}
            <div class="border rounded d-inline-flex align-items-center justify-content-center"
                 style="width:400px; height:400px; background:#f8f9fa; color:#6c757d;">
                <div>
                    <i class="fas fa-qrcode fa-3x mb-2" aria-hidden="true"></i>
                    <div class="small">QR disabled</div>
                </div>
            </div>

            <p class="mt-3 text-muted">Volunteers can no longer mark attendance for this event.</p>
        @else
            {{-- Live QR: renders only when event still active --}}
            <div>
                {!! QrCode::size(400)->generate(route('ngo.attendance.scan', ['event' => $event->event_id])) !!}
            </div>
            <p class="mt-3">Volunteers scan this QR code to mark attendance.</p>
        @endif
    </div>
</div>
