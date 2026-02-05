@props([
    'title' => 'Dashboard',
    'breadcrumbs' => [['label' => 'Home', 'route' => 'dashboard']],
    'filters' => ['Role', 'Team', 'Email', 'Member', 'Recommendation'],
    'showDatePicker' => true,
    'showFilter' => true,
    'backButton' => false,
    'backUrl' => '#',
])

<div class="page-header">
    {{-- Left Section: Title & Breadcrumbs --}}
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $title }}</h5>
        </div>
        
        @if($breadcrumbs)
            <ul class="breadcrumb">
                @foreach($breadcrumbs as $crumb)
                    <li class="breadcrumb-item">
                        @if(isset($crumb['route']))
                            <a href="{{ route($crumb['route']) }}">{{ $crumb['label'] }}</a>
                        @else
                            {{ $crumb['label'] }}
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Right Section: Date Picker & Filters --}}
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            {{-- Mobile Back Button --}}
            @if($backButton)
                <div class="d-flex d-md-none">
                    <a href="{{ $backUrl }}" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back</span>
                    </a>
                </div>
            @endif

            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                {{-- Date Range Picker --}}
                @if($showDatePicker)
                    <div id="reportrange" class="reportrange-picker d-flex align-items-center">
                        <span class="reportrange-picker-field"></span>
                    </div>
                @endif

                {{-- Filter Dropdown --}}
                @if($showFilter && $filters)
                    <div class="dropdown filter-dropdown">
                        <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10"
                            data-bs-auto-close="outside">
                            <i class="feather-filter me-2"></i>
                            <span>Filter</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            @foreach($filters as $index => $filter)
                                <div class="dropdown-item">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input filter-checkbox" 
                                               id="filter_{{ $index }}" 
                                               data-filter="{{ strtolower($filter) }}"
                                               checked="checked" />
                                        <label class="custom-control-label c-pointer" 
                                               for="filter_{{ $index }}">{{ $filter }}</label>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="dropdown-divider"></div>
                            
                            <a href="javascript:void(0);" class="dropdown-item" onclick="createNewFilter(event)">
                                <i class="feather-plus me-3"></i>
                                <span>Create New</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="manageFilters(event)">
                                <i class="feather-filter me-3"></i>
                                <span>Manage Filter</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Mobile Menu Toggle --}}
        <div class="d-md-none d-flex align-items-center">
            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                <i class="feather-align-right fs-20"></i>
            </a>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
    function createNewFilter(event) {
        event.preventDefault();
        console.log('Create new filter');
        // Implement your create filter logic here
    }

    function manageFilters(event) {
        event.preventDefault();
        console.log('Manage filters');
        // Implement your manage filters logic here
    }

    // Listen for filter changes
    document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const filter = this.dataset.filter;
            const isChecked = this.checked;
            console.log(`Filter "${filter}" is now ${isChecked ? 'enabled' : 'disabled'}`);
            // Emit event or call API to apply filters
        });
    });
</script>
@endPushOnce
