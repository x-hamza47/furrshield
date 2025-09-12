@if (session('success'))
<div class="alert alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow-lg rounded-4 custom-alert-success" role="alert" style="z-index:9999; max-width:350px;">
    <i class="bx bx-check-circle fs-3 me-3"></i>
    <div class="d-inline-block">{{ session('success') }}</div>
    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-dismissible fade show position-fixed top-0 end-0 m-4 shadow-lg rounded-4 custom-alert-error" role="alert" style="z-index:9999; max-width:350px;">
    <i class="bx bx-error-circle fs-3 me-3"></i>
    <div class="d-inline-block">{{ session('error') }}</div>
    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif