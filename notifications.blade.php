<head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/ajax.js') }}"></script>
</head>
@include('helperPages.headerFiles')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* for display: none !important; */
    .bootstrap-select > .dropdown-toggle.bs-placeholder {
        display: inline-block !important; /* or block */
    }

    /* Tab System */
    .tabs {
        display: flex;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #f5f7fa 100%);
    }

    .tabs li {
        flex: 1;
        text-align: center;
        padding: 1.2rem 0;
        cursor: pointer;
        position: relative;
        font-weight: 500;
        color: #607d8b;
        transition: all 0.3s ease;
        list-style: none;
    }

    .tabs li:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: #00bcd4;
        transform: translateX(-50%);
        transition: all 0.3s ease;
    }

    .tabs li:hover {
        color: #3f51b5;
        background: rgba(0, 0, 0, 0.02);
    }

    .tabs li.active {
        color: #3f51b5;
        font-weight: 600;
    }

    .tabs li.active:after {
        width: 80%;
    }

    .tab_content {
        padding: 2rem;
        display: block;
    }

    .tab_content.active {
        display: block;
    }
</style>

<body class="theme-black">

    @include('helperPages.navbar')

@php
    $allowedEmails = [
        'Qutaiba.k@samalqudstv.com',
        'saadi.sh@samalqudstv.com',
        'Amr.b@samalqudstv.com',
        'salah.h@samalqudstv.com',
        'hazem.a@samalqudstv.com',
        'abdulrahman.m@samalqudstv.com',
        'n.saeed@samalqudstv.com',
        'mohamad.a@samalqudstv.com'
    ];
    $user = Auth::user();
    $isAdmin = in_array($user->email, $allowedEmails);
@endphp


    <section class="content blog-page">
        <div class="container">
            <div class="block-header">
                <div class="row clearfix">
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <h2>{{ $title }}</h2>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <ul class="breadcrumb float-md-right padding-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="zmdi zmdi-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/notifications') }}">Notifications</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card" style="background-color:transparent;">
                        <div class="body">
                            <ul class="tabs" style="background-color: rgb(255, 255, 255);">
                                <li class="active" rel="tab1">Notifications</li>
                                @if($isAdmin)
                                    <li rel="tab2">Reports</li>
                                @endif
                            </ul>

                            <div class="tab_container" style="background-color: rgb(255, 255, 255);">
                                <!-- Tab 1: Notifications -->
                                <div class="container-fluid">
                                    <div id="tab1" class="tab_content active">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="card-title">{{ $title }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        @if(session('success'))
                                                            <div class="alert alert-success">
                                                                {{ session('success') }}
                                                            </div>
                                                        @endif
                                    
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-hover">
                                                                <thead>
                                                                    @if($isAdmin)
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Type</th>
                                                                            <th>User</th>
                                                                            <th>Request Data</th>
                                                                            <th>Status</th>
                                                                            <th>Submitted At</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    @else
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Type</th>
                                                                            <th>Request Data</th>
                                                                            <th>Status</th>
                                                                            <th>Submitted At</th>
                                                                        </tr>
                                                                    @endif
                                                                </thead>
                                    
                                                                <tbody>
                                                                    @if($isAdmin)
                                                                        {{-- Admins see all requests --}}
                                                                        @forelse($get_data as $index => $request)
                                                                            <tr id="request-row-{{ $request->id }}">
                                                                                <td>{{ $index + 1 }}</td>
                                                                                <td>
                                                                                    <span class="badge badge-info">
                                                                                        {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    <small class="text-muted">{{ $request->user_email ?? 'N/A' }}</small>
                                                                                </td>
                                                                                <td>
                                                                                    <button class="btn btn-sm btn-info view-details"
                                                                                            data-toggle="modal"
                                                                                            data-target="#detailsModal"
                                                                                            data-request='{!! json_encode([
                                                                                                "id" => $request->id,
                                                                                                "type" => $request->type,
                                                                                                "status" => $request->status,
                                                                                                "user_name" => ($request->user->first_name ?? '') . ' ' . ($request->user->last_name ?? '') ?: "N/A",
                                                                                                "user_email" => $request->user->email ?? "N/A",
                                                                                                "created_at" => $request->created_at->format("M d, Y H:i"),
                                                                                                "form_data" => $request->form_data
                                                                                            ]) !!}'>
                                                                                        View Details
                                                                                    </button>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge
                                                                                        @if($request->status == 'pending') badge-warning
                                                                                        @elseif($request->status == 'accepted') badge-success
                                                                                        @else badge-danger
                                                                                        @endif" id="status-badge-{{ $request->id }}">
                                                                                        {{ ucfirst($request->status) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                                                                <td id="actions-cell-{{ $request->id }}">
                                                                                    @if($request->status == 'pending')
                                                                                        <button class="btn btn-sm btn-success accept-request" data-id="{{ $request->id }}">Accept</button>
                                                                                        <button class="btn btn-sm btn-danger reject-request" data-id="{{ $request->id }}">Reject</button>
                                                                                    @else
                                                                                        <span class="text-muted">Processed</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="7" class="text-center">No requests found</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    @else
                                                                        {{-- Non-admins see only their own requests --}}
                                                                        @php
                                                                            $userRequests = $get_data->filter(function($request) use ($user) {
                                                                                return $request->user_email === $user->email;
                                                                            });
                                                                        @endphp
                                                                
                                                                        @forelse($userRequests as $index => $request)
                                                                            <tr>
                                                                                <td>{{ $index + 1 }}</td>
                                                                                <td>
                                                                                    <span class="badge badge-info">
                                                                                        {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    <button class="btn btn-sm btn-info view-details"
                                                                                            data-toggle="modal"
                                                                                            data-target="#detailsModal"
                                                                                            data-request='{!! json_encode([
                                                                                                "id" => $request->id,
                                                                                                "type" => $request->type,
                                                                                                "status" => $request->status,
                                                                                                "user_name" => ($request->user->first_name ?? '') . ' ' . ($request->user->last_name ?? '') ?: "N/A",
                                                                                                "user_email" => $request->user->email ?? "N/A",
                                                                                                "created_at" => $request->created_at->format("M d, Y H:i"),
                                                                                                "form_data" => $request->form_data
                                                                                            ]) !!}'>
                                                                                        View Details
                                                                                    </button>
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge
                                                                                        @if($request->status == 'pending') badge-warning
                                                                                        @elseif($request->status == 'accepted') badge-success
                                                                                        @else badge-danger
                                                                                        @endif">
                                                                                        {{ ucfirst($request->status) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="5" class="text-center">No requests found</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    @endif
                                                                </tbody>
                                                                                            
                                                            </table>
                                                        </div>
                                                                                            
                                                        <div class="d-flex justify-content-center">
                                                            {{ $get_data->links() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($isAdmin)
                            <!-- Tab 2: Reports (Admin Only) -->
                            <div id="tab2" class="tab_content" style="background-color: rgb(255, 255, 255);">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="card-title">
                                                        <i class="fa fa-chart-bar"></i> Reports Dashboard
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    
                                                    <!-- Report Generation Controls -->
                                                    <div class="row mb-4">
                                                        <div class="col-md-6">
                                                            <div class="card border-primary">
                                                                <div class="card-header bg-primary text-white">
                                                                    <h6 class="mb-0"><i class="fa fa-user"></i> Individual User Report</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <form id="individualReportForm">
                                                                        <div class="form-group">
                                                                            <label>Select User:</label>
                                                                            <select class="chosen-select" multiple name="reporters[]">
                                                                                @foreach($staff_data as $g)
                                                                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Date Range:</label>
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <input type="date" class="form-control" id="user_start_date" placeholder="Start Date">
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="date" class="form-control" id="user_end_date" placeholder="End Date">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Request Type:</label>
                                                                            <select class="form-control chosen-select" id="user_request_type" name="user_request_type">
                                                                                <option value="">All Types</option>
                                                                                @foreach($requestTypes as $type)
                                                                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Status:</label>
                                                                            <select class="form-control chosen-select" id="user_status" name="user_status">
                                                                                <option value="">All Statuses</option>
                                                                                <option value="pending">Pending</option>
                                                                                <option value="accepted">Accepted</option>
                                                                                <option value="rejected">Rejected</option>
                                                                            </select>
                                                                        </div>

                                                                        <button type="submit" class="btn btn-primary btn-block">
                                                                            <i class="fa fa-download"></i> Generate Individual Report
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card border-success">
                                                                <div class="card-header bg-success text-white">
                                                                    <h6 class="mb-0"><i class="fa fa-users"></i> All Users Report</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <form id="allUsersReportForm">
                                                                        <div class="form-group">
                                                                            <label>Date Range:</label>
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <input type="date" class="form-control" id="all_start_date" placeholder="Start Date">
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <input type="date" class="form-control" id="all_end_date" placeholder="End Date">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>All Requests Type:</label>
                                                                            <select class="form-control chosen-select" id="all_request_type" name="all_request_type">
                                                                                <option value="">All Types</option>
                                                                                @foreach($requestTypes as $type)
                                                                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Status:</label>
                                                                            <select class="form-control chosen-select" id="user_status" name="user_status">
                                                                                <option value="">All Statuses</option>
                                                                                <option value="pending">Pending</option>
                                                                                <option value="accepted">Accepted</option>
                                                                                <option value="rejected">Rejected</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Report Format:</label>
                                                                            <select class="form-control chosen-select" id="all_report_format" name="all_report_format">
                                                                                <option value="summary">Summary Report</option>
                                                                                <option value="detailed">Detailed Report</option>
                                                                                <option value="statistics">Statistics Report</option>
                                                                            </select>
                                                                        </div>
                                                                        <button type="submit" class="btn btn-success btn-block">
                                                                            <i class="fa fa-download"></i> Generate All Users Report
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Quick Stats -->
                                                    <div class="row mb-4">
                                                        <div class="col-md-12">
                                                            <div class="card border-info">
                                                                <div class="card-header bg-info text-white">
                                                                    <h6 class="mb-0"><i class="fa fa-chart-pie"></i> Quick Statistics</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <div class="text-center">
                                                                                <h3 class="text-warning">{{ $get_data->where('status', 'pending')->count() }}</h3>
                                                                                <small>Pending Requests</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="text-center">
                                                                                <h3 class="text-success">{{ $get_data->where('status', 'accepted')->count() }}</h3>
                                                                                <small>Accepted Requests</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="text-center">
                                                                                <h3 class="text-danger">{{ $get_data->where('status', 'rejected')->count() }}</h3>
                                                                                <small>Rejected Requests</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="text-center">
                                                                                <h3 class="text-primary">{{ $get_data->count() }}</h3>
                                                                                <small>Total Requests</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Request Type Breakdown -->
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h6 class="mb-0"><i class="fa fa-chart-bar"></i> Request Types Breakdown</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-sm table-striped">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Request Type</th>
                                                                                    <th class="text-center">Total</th>
                                                                                    <th class="text-center">Pending</th>
                                                                                    <th class="text-center">Accepted</th>
                                                                                    <th class="text-center">Rejected</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach($requestTypes as $type)
                                                                                    @php
                                                                                        $typeRequests = $get_data->where('type', $type);
                                                                                        $total = $typeRequests->count();
                                                                                        $pending = $typeRequests->where('status', 'pending')->count();
                                                                                        $accepted = $typeRequests->where('status', 'accepted')->count();
                                                                                        $rejected = $typeRequests->where('status', 'rejected')->count();
                                                                                    @endphp
                                                                                    <tr>
                                                                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong></td>
                                                                                        <td class="text-center"><span class="badge badge-primary">{{ $total }}</span></td>
                                                                                        <td class="text-center"><span class="badge badge-warning">{{ $pending }}</span></td>
                                                                                        <td class="text-center"><span class="badge badge-success">{{ $accepted }}</span></td>
                                                                                        <td class="text-center"><span class="badge badge-danger">{{ $rejected }}</span></td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Additional Report Options -->
                                                    <div class="row mt-4">
                                                        <div class="col-md-12">
                                                            <div class="card border-secondary">
                                                                <div class="card-header bg-secondary text-white">
                                                                    <h6 class="mb-0"><i class="fa fa-cog"></i> Additional Report Options</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <button class="btn btn-outline-info btn-block" id="generateMonthlyReport">
                                                                                <i class="fa fa-calendar-alt"></i> Monthly Summary
                                                                            </button>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <button class="btn btn-outline-warning btn-block" id="generateDepartmentReport">
                                                                                <i class="fa fa-building"></i> Department-wise Report
                                                                            </button>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <button class="btn btn-outline-success btn-block" id="exportAllData">
                                                                                <i class="fa fa-database"></i> Export All Data
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Request Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="requestDetails">
                    <!-- Details will be loaded here by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Only attach if #share-modal exists
    const shareModal = document.getElementById('share-modal');
    if (shareModal) {
        // add any share-modal event code here if needed
    }

    console.log("Document ready - initializing event handlers");

    // ------------------ Tab switching ------------------
    $('.tabs li').click(function () {
        var tab_id = $(this).attr('rel');
        $('.tabs li').removeClass('active');
        $('.tab_content').removeClass('active');
        $(this).addClass('active');
        $("#" + tab_id).addClass('active');
    });

    // ------------------ View details modal ------------------
    $(document).on('click', '.view-details', function () {
        let rawData = $(this).attr('data-request')
            .replace(/&quot;/g, '"')
            .replace(/&#039;/g, "'")
            .replace(/&amp;/g, "&");

        let requestData = {};
        try {
            requestData = JSON.parse(rawData);
            if (typeof requestData.form_data === 'string') {
                try { requestData.form_data = JSON.parse(requestData.form_data); } catch {}
            }
        } catch (e) {
            $('#requestDetails').html(
                `<div class="alert alert-danger">
                   <h6>Error parsing request data</h6>
                   <p><strong>Error:</strong> ${e.message}</p>
                 </div>`
            );
            return;
        }

        let detailsHtml = '<div class="table-responsive"><table class="table table-bordered table-striped">';
        detailsHtml += '<thead class="thead-dark"><tr><th colspan="2">Request Information</th></tr></thead><tbody>';
        detailsHtml += `<tr><td><strong>Request ID</strong></td><td>${requestData.id || 'N/A'}</td></tr>`;
        detailsHtml += `<tr><td><strong>Type</strong></td><td><span class="badge badge-info">${(requestData.type || 'N/A').replace(/_/g, ' ').toUpperCase()}</span></td></tr>`;
        detailsHtml += `<tr><td><strong>Status</strong></td><td><span class="badge ${getStatusBadgeClass(requestData.status || 'pending')}">${(requestData.status || 'pending').toUpperCase()}</span></td></tr>`;
        detailsHtml += `<tr><td><strong>Submitted At</strong></td><td>${requestData.created_at || 'N/A'}</td></tr>`;
        detailsHtml += '</tbody>';

        // Form data
        detailsHtml += '<thead class="thead-light"><tr><th colspan="2">Form Details</th></tr></thead><tbody>';
        if (requestData.form_data && typeof requestData.form_data === "object") {
            let hasData = false;
            for (let key in requestData.form_data) {
                if (key !== '_token' && key !== 'form_type') {
                    hasData = true;
                    let val = requestData.form_data[key];
                    if (val === null || val === undefined || val === '') val = '<span class="text-muted">Not provided</span>';
                    else if (Array.isArray(val)) val = val.join(', ');
                    else if (typeof val === 'object') val = formatObjectValue(val);
                    detailsHtml += `<tr><td><strong>${formatFieldName(key)}</strong></td><td>${val}</td></tr>`;
                }
            }
            if (!hasData) detailsHtml += '<tr><td colspan="2" class="text-muted">No form data available</td></tr>';
        } else {
            detailsHtml += '<tr><td colspan="2" class="text-muted">No form data available</td></tr>';
        }
        detailsHtml += '</tbody></table></div>';

        $('#requestDetails').html(detailsHtml);
        $('#detailsModal').modal('show');
    });

    function formatObjectValue(obj) {
        let result = '';
        for (let key in obj) {
            let v = obj[key];
            if (v && (typeof v !== 'string' || v.trim() !== '')) {
                result += `<div>${key}: ${v}</div>`;
            }
        }
        return result || '<span class="text-muted">No data</span>';
    }

    // ------------------ Accept / Reject ------------------
    $(document).on('click', '.accept-request, .reject-request', function () {
        let requestId = $(this).data('id');
        let action = $(this).hasClass('accept-request') ? 'accept' : 'reject';
        if (!confirm(`Are you sure you want to ${action} this request?`)) return;

        let row = $('#request-row-' + requestId);
        let statusBadge = $('#status-badge-' + requestId);
        let actionsCell = $('#actions-cell-' + requestId);

        row.find('.accept-request, .reject-request').prop('disabled', true);
        actionsCell.html('<span class="text-muted">Processing...</span>');

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: '{{ route("process.request") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', request_id: requestId, action: action },
            success: function (response) {
                if (response.status === 'success') {
                    statusBadge.removeClass('badge-warning badge-success badge-danger')
                               .addClass(action === 'accept' ? 'badge-success' : 'badge-danger')
                               .text(action.charAt(0).toUpperCase() + action.slice(1));
                    actionsCell.html('<span class="text-muted">Processed</span>');
                    alert(response.message);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                alert('AJAX error: ' + error);
                actionsCell.html(
                    '<button class="btn btn-sm btn-success accept-request" data-id="' + requestId + '">Accept</button>' +
                    '<button class="btn btn-sm btn-danger reject-request" data-id="' + requestId + '">Reject</button>'
                );
            }
        });
    });

    // ------------------ Report forms ------------------
    $('#individualReportForm').on('submit', function (e) {
        e.preventDefault();
        let params = {
            reporters: $(this).find('[name="reporters[]"]').val(),
            start_date: $('#user_start_date').val(),
            end_date: $('#user_end_date').val(),
            request_type: $('#user_request_type').val(),
            status: $('#user_status').val()
        };
        if (!params.user_email) return alert('Please select a user');
        toggleBtn(this, true);
        generateReport('individual', params).always(() => toggleBtn(this, false));
    });

    $('#allUsersReportForm').on('submit', function (e) {
        e.preventDefault();
        let params = {
            start_date: $('#all_start_date').val(),
            end_date: $('#all_end_date').val(),
            request_type: $('#all_request_type').val(),
            status: $('#all_status').val(),
            format: $('#all_report_format').val()
        };
        toggleBtn(this, true);
        generateReport('all_users', params).always(() => toggleBtn(this, false));
    });
    $('#downloadReportBtn').on('click', function() {
    let data = {
        reporters: $('#reporters').val(), // array of user IDs
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        request_type: $('#request_type').val(),
        status: $('#status').val(),
    };

    $.ajax({
            url: '/notifications/generate-report/all_users',
            method: 'POST',
            data: data,
            xhrFields: {
                responseType: 'blob' // important for file download
            },
            success: function(blob, status, xhr) {
                let filename = xhr.getResponseHeader('Content-Disposition')
                    .split('filename=')[1];
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename.replace(/"/g, '');
                link.click();
            },
            error: function(err) {
                alert('Error generating report.');
            }
        });
    });

    $('#generateMonthlyReport').click(() => {
        let d = new Date();
        generateReport('monthly', {
            start_date: new Date(d.getFullYear(), d.getMonth(), 1).toISOString().split('T')[0],
            end_date:   new Date(d.getFullYear(), d.getMonth() + 1, 0).toISOString().split('T')[0]
        });
    });
    $('#generateDepartmentReport').click(() => generateReport('department', {}));
    $('#exportAllData').click(() => generateReport('export_all', {}));

    // ------------------ Report AJAX ------------------
    function generateReport(type, params) {
        return $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: '{{ url("/generate-report") }}/' + type,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', ...params },
            xhrFields: { responseType: 'blob' }, // we expect a Blob
            processData: true,
            contentType: false,
            success: function (data, status, xhr) {
                if (data instanceof Blob) {
                    const url = window.URL.createObjectURL(data);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = getFilenameFromHeaders(xhr) || defaultFilename(type, params);
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    showNotification('success', 'Report generated and downloaded successfully!');
                }
            },
            error: function (xhr) {
                if (xhr.response instanceof Blob) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        try {
                            const err = JSON.parse(reader.result);
                            showNotification('error', err.error || err.message || 'Error generating report.');
                        } catch {
                            showNotification('error', 'Error generating report.');
                        }
                    };
                    reader.readAsText(xhr.response);
                } else {
                    showNotification('error', 'Error generating report.');
                }
            }
        });
    }

    function toggleBtn(form, disable) {
        const btn = $(form).find('button[type="submit"]');
        btn.prop('disabled', disable)
           .html(disable ? '<i class="fa fa-spinner fa-spin"></i> Generating…' : btn.data('orig') || btn.text());
        if (!btn.data('orig')) btn.data('orig', btn.text());
    }

    function defaultFilename(type, params) {
        const date = new Date().toISOString().slice(0,10).replace(/-/g,'');
        switch (type) {
            case 'individual': return `individual_${(params.user_email||'user').split('@')[0]}_${date}.csv`;
            case 'all_users':  return `all_users_${date}.csv`;
            case 'monthly':    return `monthly_${date}.csv`;
            case 'department': return `department_${date}.csv`;
            case 'export_all': return `complete_export_${date}.csv`;
            default:           return `report_${date}.csv`;
        }
    }

    function getFilenameFromHeaders(xhr) {
        const cd = xhr.getResponseHeader('Content-Disposition');
        const match = cd && cd.match(/filename="(.+)"/);
        return match ? match[1] : null;
    }

    function showNotification(type, msg) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const html = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${msg}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                      </div>`;
        $('.card-body').first().prepend(html);
        setTimeout(() => $('.alert').fadeOut(), 5000);
    }

    function getStatusBadgeClass(status) {
        switch ((status || '').toLowerCase()) {
            case 'pending': return 'badge-warning';
            case 'accepted': return 'badge-success';
            case 'rejected': return 'badge-danger';
            default: return 'badge-secondary';
        }
    }

    function formatFieldName(name) {
        const map = {
            user_email: 'User Email', work_date: 'Work Date', working_hours: 'Working Hours',
            total_hours_today: 'Total Hours Today', total_hours_worked: 'Total Hours Worked',
            available_vacation: 'Available Vacation Days', overtime: 'Overtime Hours',
            leave_type: 'Leave Type', start_date: 'Start Date', end_date: 'End Date',
            return_datetime: 'Return Date', days_hours: 'Duration', substitute: 'Responsible Person',
            balance: 'Balance', contract_name: 'Contract Name', amount: 'Amount',
            completed_work: 'Completed Work', manager: 'Manager',
            overtime_datetime: 'Overtime Date', overtime_hours: 'Overtime Hours', note: 'Notes'
        };
        return map[name] || name.replace(/_/g, ' ')
                               .split(' ')
                               .map(w => w.charAt(0).toUpperCase() + w.slice(1))
                               .join(' ');
    }
});
</script>


<style>
/* Force standard select styling */
#team, #payment-department, #leave-department, #responsible-person, #department-manager {
    display: block !important;
    opacity: 1 !important;
    position: static !important;
    height: auto !important;
    width: 100% !important;
}

/* Hide bootstrap select elements */
/* .bootstrap-select, .btn-group.bootstrap-select {
    display: none !important;
} */

/* Select2 styling */
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 3px;
    min-height: 38px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #3f51b5;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 2px 8px;
    margin: 3px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: 5px;
}

.error-field {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.leave-types.error-field {
    border: 2px solid #dc3545 !important;
    border-radius: 8px;
    padding: 10px;
    background-color: rgba(220, 53, 69, 0.05) !important;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: none;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border-left: 4px solid #ffc107;
}

.alert ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.alert li {
    margin-bottom: 5px;
}

.leave-type.selected {
    border-color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
}

/* Tab System */
.tabs {
    display: flex;
    border-radius: 8px 8px 0 0;
    overflow: hidden;
    background: linear-gradient(135deg, #ffffff 0%, #f5f7fa 100%);
}

.tabs li {
    flex: 1;
    text-align: center;
    padding: 1.2rem 0;
    cursor: pointer;
    position: relative;
    font-weight: 500;
    color: #607d8b;
    transition: all 0.3s ease;
    list-style: none;
}

.tabs li:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 3px;
    background: #00bcd4;
    transform: translateX(-50%);
    transition: all 0.3s ease;
}

.tabs li:hover {
    color: #3f51b5;
    background: rgba(0, 0, 0, 0.02);
}

.tabs li.active {
    color: #3f51b5;
    font-weight: 600;
}

.tabs li.active:after {
    width: 80%;
}

.tab_content {
    padding: 2rem;
    display: block;
}

.tab_content.active {
    display: block;
}

.form-section {
    margin-bottom: 25px;
    padding: 20px;
    border-radius: 8px;
    background: #f9f9f9;
}

.form-title {
    font-size: 1.2rem;
    color: #3f51b5;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e0e0e0;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #263238;
}

.time-slots {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.time-slot {
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background: white;
}

.leave-types {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.leave-type {
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn {
    padding: 12px 25px;
    background-color: #3f51b5;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
}

.btn:hover {
    background-color: #303f9f;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

@media (max-width: 768px) {
    .tabs {
        flex-direction: column;
    }
    .time-slots, .leave-types {
        grid-template-columns: 1fr;
    }
    .tab_content {
        padding: 1rem;
    }
}
</style>

    @include('helperPages.footerFiles')