@extends('dashboard.auth.index')

@section('content')
    <h4 class="mb-2">Adventure starts here 🚀</h4>
    <p class="mb-4">Make your app management easy and fun!</p>

    <form id="formAuthentication" class="mb-3" action="{{ route('user.register') }}" method="POST">
      @csrf
        {{-- ? Full Name --}}
        <div class="mb-3">
            <label for="role" class="form-label">Register As</label>
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="vet" {{ old('role') == 'vet' ? 'selected' : '' }}>Vet</option>
                <option value="shelter" {{ old('role') == 'shelter' ? 'selected' : '' }}>Shelter</option>
            </select>
        </div>
        <div class="mb-3 d-flex gap-4">
            <div class="w-100">
                <label for="username" class="form-label">Full Name</label>
                <input type="text" class="form-control  @error('name') is-invalid @enderror" id="username"
                    name="name" placeholder="Enter your full name" value="{{ old('name') }}" />
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- ? Email --}}
            <div class="mb-3 w-100">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" placeholder="Enter your email" value="{{ old('email') }}" />
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ? Password --}}
        <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Password</label>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        {{-- ? Confirm Password --}}
        <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">Confirmed Password</label>
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
                    name="password_confirmation"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3 d-flex gap-4">
            <div class="w-100">
                <label for="phone" class="form-label">Contact </label>
                <input type="text" class="form-control  @error('phone') is-invalid @enderror" id="phone"
                    name="conphonetact" placeholder="0331346498" value="{{ old('phone') }}" />
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms" />
                <label class="form-check-label" for="terms-conditions">
                    I agree to
                    <a href="javascript:void(0);">privacy policy & terms</a>
                </label>
            </div>
        </div>
        <button class="btn btn-primary d-grid w-100">Sign up</button>
    </form>

    <p class="text-center">
        <span>Already have an account?</span>
        <a href="{{ route('login') }}">
            <span>Sign in instead</span>
        </a>
    </p>
    </div>
    </div>
    <!-- Register Card -->
    </div>
    </div>
    </div>
@endsection

