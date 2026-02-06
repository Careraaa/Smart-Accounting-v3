@extends('layouts.auth-layout')

@section('title', 'Register - Smart Accounting')

@section('content')
    <h2 class="fs-20 fw-bolder mb-4">Create Account</h2>
    <h4 class="fs-13 fw-bold mb-2">Register for Smart Accounting System</h4>
    <p class="fs-12 fw-medium text-muted">Set up your account to get started with our platform.</p>

    <form method="POST" action="{{ route('register') }}" class="w-100 mt-4 pt-2">
        @csrf

        <div class="mb-4">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                   name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                   placeholder="John Doe">
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autocomplete="email" 
                   placeholder="your@email.com">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="new-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                   name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="role" class="form-label">Role</label>
            <select id="role" class="form-control @error('role') is-invalid @enderror" 
                    name="role" required>
                <option value="">Select a role</option>
                <option value="remittance_clerk" {{ old('role') === 'remittance_clerk' ? 'selected' : '' }}>Remittance Clerk</option>
                <option value="hr" {{ old('role') === 'hr' ? 'selected' : '' }}>HR</option>
                <option value="accountant" {{ old('role') === 'accountant' ? 'selected' : '' }}>Accountant</option>
            </select>
            @error('role')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
            <label class="form-check-label" for="terms">
                I agree to the <a href="#" class="text-primary">Terms & Conditions</a>
            </label>
        </div>

        <div class="mt-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Create Account</button>
        </div>
    </form>

    <div class="mt-5 text-muted text-center">
        <span>Already have an account?</span>
        <a href="{{ route('login') }}" class="fw-bold">Sign In</a>
    </div>
@endsection
    <!--! Footer Script !-->
    <!--! ================================================================ !-->
    <!--! BEGIN: Vendors JS !-->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="assets/vendors/js/lslstrength.min.js"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="assets/js/common-init.min.js"></script>
    <!--! END: Apps Init !-->
    <!--! BEGIN: Theme Customizer  !-->
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <!--! END: Theme Customizer !-->
</body>

</html>