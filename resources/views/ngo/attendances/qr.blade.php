<div class="mt-5 card participant-section" id="section-qr" style="display:none">
    <div class="card-header">
        <h5 class="card-title">Event QR Code</h5>
    </div>
    <div class="card-body mt-5 text-center ">
        {{-- This QR contains event_id + secure token --}}
        {!! QrCode::size(400)->generate(route('ngo.attendance.scan', ['event' => $event->event_id])) !!}
        <p class="mt-2">Volunteers scan this QR code to mark attendance.</p>
    </div>
</div>
