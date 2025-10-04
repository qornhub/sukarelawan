<div class="mt-5 card participant-section" id="section-qr" style="display:none">
    <div class="card-header">
        <h5 class="card-title">Event QR Code</h5>
    </div>
    <div class="card-body mt-4 text-center d-flex flex-column align-items-center">
    {{-- This QR contains event_id + secure token --}}
    <div>
        {!! QrCode::size(400)->generate(route('ngo.attendance.scan', ['event' => $event->event_id])) !!}
    </div>
    <p class="mt-3">Volunteers scan this QR code to mark attendance.</p>
</div>
</div>
