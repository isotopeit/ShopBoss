<div class="mb-3">
    <label for="user_id" class="form-label">{{ __('shopboss::shopboss.user') }}</label>
    <select name="user_id" id="user_id" class="form-control" data-control="select2" required @if(isset($branchUser)) disabled @endif>
        <option value="">{{ __('shopboss::shopboss.selectUser') }}</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ (old('user_id', $branchUser->user_id ?? '') == $user->id) ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
        @endforeach
    </select>
    @if(isset($branchUser))
        <input type="hidden" name="user_id" value="{{ $branchUser->user_id }}">
    @endif
</div>
<div class="mb-3">
    <label for="branch_id" class="form-label">{{ __('shopboss::shopboss.branch') }}</label>
    <select name="branch_id" id="branch_id" class="form-control" data-control="select2" required>
        <option value="">{{ __('shopboss::shopboss.selectBranch') }}</option>
        @foreach($branches as $branch)
            <option value="{{ $branch->id }}" {{ (old('branch_id', $branchUser->branch_id ?? '') == $branch->id) ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
        @endforeach
    </select>
</div>
