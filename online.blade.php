@include('helperPages.headerFiles')

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Add Select2 CSS for multiple select -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
// Prevent CKEditor and noUiSlider errors by checking if elements exist
$(document).ready(function() {
    // Only initialize CKEditor if the textarea exists
    if (typeof CKEDITOR !== 'undefined' && $('#ckeditor').length) {
        CKEDITOR.replace('ckeditor');
    }
    
    // Only initialize noUiSlider if the slider element exists
    if (typeof noUiSlider !== 'undefined' && $('#nouislider').length) {
        // Your noUiSlider initialization code here
    }
    
    // Fix for share-modal error
    var shareModal = document.getElementById('share-modal');
    if (shareModal) {
        shareModal.addEventListener('show.bs.modal', function(event) {
            // Your modal code here
        });
    }
});
</script>
<body class="theme-black">
    @include('helperPages.navbar')

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
                            <li class="breadcrumb-item"><a href="{{ url('/online') }}">Online</a></li>
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
                                <li class="active" rel="tab1">Working Hours</li>
                                <li rel="tab2">Leave Request</li>
                                <li rel="tab3">Payment Request</li>
                                <li rel="tab4">Overtime</li>
                            </ul>

                            <div class="tab_container" style="background-color: rgb(255, 255, 255);">
                                <!-- Tab 1: Working Hours -->
                                <div id="tab1" class="tab_content active">
                                    <div class="body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-section">
                                                    <h3 class="form-title">Daily Working Hours Registration</h3>
                                                    <form id="workingHoursForm" action="{{ route('working.hours.submit') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="form_type" value="working_hours">
                                                        <input type="hidden" name="user_email" value="{{ Auth::user()->email }}">

                                                        <div class="form-group">
                                                            <label>Employee Name</label>
                                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="team">Team</label>
                                                            <select class="form-control" id="team" name="team" required style="border:1px solid gray;">
                                                                <option value="">Select Team</option>
                                                                <option value="SamalQuds">SamalQuds</option>
                                                                <option value="Sa7at">Sa7at</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="work-date">Date</label>
                                                            <input type="date" id="work-date" name="work_date" class="form-control" required>
                                                        </div>

                                                        <h4 class="form-title">Daily Tasks by Time Periods</h4>

                                                        <div class="time-slots">
                                                            @for($i = 8; $i <= 22; $i+=2)
                                                                <div class="time-slot">
                                                                    <label>{{ $i }}:00 to {{ $i+2 }}:00</label>
                                                                    <input type="text" class="form-control working-hour" data-hours="2" name="working_hours[{{ $i }}]" placeholder="Completed task">
                                                                </div>
                                                            @endfor
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="total-hours">Total Hours Today</label>
                                                            <input type="number" id="total-hours" name="total_hours_today" class="form-control" placeholder="Auto-calculated" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="total-worked">Total Hours Worked</label>
                                                            <input type="number" id="total-worked" name="total_hours_worked" class="form-control" value="{{ Auth::user()->overtime_hours ?? 0 }}" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="available-vacation">Available Vacation Days</label>
                                                            <input type="number" id="available-vacation" name="available_vacation" class="form-control" value="{{ Auth::user()->available_days ?? 0 }}" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="overtime">Overtime (Hours)</label>
                                                            <input type="number" id="overtime" name="overtime" class="form-control" placeholder="Auto-calculated" readonly>
                                                        </div>

                                                        <button type="submit" class="btn"><i class="fas fa-save"></i> Save</button>
                                                        <div id="workingHoursMessage" class="message mt-3"></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Leave Request -->
                                <div id="tab2" class="tab_content">
                                    <div class="body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-section">
                                                    <h3 class="form-title">Leave Request</h3>
                                                    <form id="leaveRequestForm" action="{{ route('leave.submit') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="form_type" value="leave_request">
                                                        <input type="hidden" name="user_email" value="{{ Auth::user()->email }}">

                                                        <div class="form-group">
                                                            <label>Employee Name</label>
                                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="leave-department">Department</label>
                                                            <select class="form-control" id="leave-department" name="department" required style="border:1px solid gray;">
                                                                <option value="">Select Department</option>
                                                                <option value="Management">Management</option>
                                                                <option value="IT">IT</option>
                                                                <option value="HR & Accounting">HR & Accounting</option>
                                                                <option value="Sahat">Sahat</option>
                                                                <option value="Sahat English">Sahat English</option>
                                                                <option value="Qudsuna">Qudsuna</option>
                                                                <option value="Samalquds">Samalquds</option>
                                                                <option value="Digital Media">Digital Media</option>
                                                                <option value="Operations">Operations</option>
                                                            </select>
                                                        </div>

                                                        <h4 class="form-title">Leave Type</h4>
                                                        <div class="leave-types">
                                                            @foreach(['annual' => 'Annual Leave', 'unpaid' => 'Unpaid Leave', 'sick' => 'Sick Leave', 'other' => 'Other Leave'] as $value => $label)
                                                                <div class="leave-type">
                                                                    <label>
                                                                        <input type="radio" name="leave_type" value="{{ $value }}" {{ $loop->first ? 'required' : '' }}>
                                                                        {{ $label }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="leave-reason">Leave Reason</label>
                                                            <textarea id="leave-reason" name="reason" class="form-control" placeholder="Reason..." required></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="leave-start">Start Date & Hour</label>
                                                            <input type="datetime-local" id="leave-start" name="start_date" class="form-control" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="leave-end">End Date & Hour</label>
                                                            <input type="datetime-local" id="leave-end" name="end_date" class="form-control" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="return-datetime">Return Date & Hour</label>
                                                            <input type="datetime-local" id="return-datetime" name="return_datetime" class="form-control" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="leave-days">Number of Days & Hours</label>
                                                            <input type="text" id="leave-days" name="days_hours" class="form-control" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="responsible-person">Responsible Person During Absence (Multiple Selection)</label>
                                                            <select id="responsible-person" name="substitute[]" class="form-control" multiple required>
                                                                @foreach(\App\Models\User::where('id', '!=', Auth::id())->get() as $user)
                                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="balance">Balance</label>
                                                            <input type="number" id="balance" name="balance" class="form-control" value="{{ Auth::user()->available_days ?? 0 }}" readonly>
                                                        </div>

                                                        <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Send Request</button>
                                                        <div id="leaveRequestMessage" class="message mt-3"></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 3: Payment Request -->
                                <div id="tab3" class="tab_content">
                                    <div class="body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-section">
                                                    <h3 class="form-title">Payment Request</h3>
                                                    <form id="paymentRequestForm" action="{{ route('form.submit') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="form_type" value="payment_request">
                                                        <input type="hidden" name="user_email" value="{{ Auth::user()->email }}">
                                                        
                                                        <div class="form-group">
                                                            <label>Employee Name</label>
                                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label for="payment-department">Department</label>
                                                            <select class="form-control" id="payment-department" name="department" required style="border:1px solid gray;">
                                                                <option value="">Select Department</option>
                                                                <option value="Management">Management</option>
                                                                <option value="IT">IT</option>
                                                                <option value="HR & Accounting">HR & Accounting</option>
                                                                <option value="Sahat">Sahat</option>
                                                                <option value="Sahat English">Sahat English</option>
                                                                <option value="Qudsuna">Qudsuna</option>
                                                                <option value="Samalquds">Samalquds</option>
                                                                <option value="Digital Media">Digital Media</option>
                                                                <option value="Operations">Operations</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="payment-date">Date</label>
                                                            <input type="date" id="payment-date" name="date" class="form-control" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="contract-name">Contract Name (Program)</label>
                                                            <input type="text" id="contract-name" name="contract_name" class="form-control" placeholder="Contract name" required>
                                                        </div>

                                                        <h4 class="form-title">Payment Details</h4>

                                                        <div class="form-group">
                                                            <label for="payment-amount">Amount</label>
                                                            <input type="number" id="payment-amount" name="amount" class="form-control" placeholder="Amount" required min="0" step="0.01">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="completed-work">Completed Work</label>
                                                            <textarea id="completed-work" name="completed_work" class="form-control" placeholder="Description of completed work..." required></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="department-manager">Department Manager</label>
                                                            <select id="department-manager" name="manager" class="form-control" required>
                                                                <option value="">Select Manager</option>
                                                                @foreach(\App\Models\User::where('role', 'supervisor')->get() as $manager)
                                                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="monthly-budget">Monthly Budget Remaining</label>
                                                            <input type="text" id="monthly-budget" class="form-control" value="$5,000.00" readonly>
                                                        </div>

                                                        <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Submit Request</button>
                                                        <div id="paymentRequestMessage" class="message mt-3"></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 4: Overtime -->
                                <div id="tab4" class="tab_content">
                                    <div class="body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-section">
                                                    <h3 class="form-title">Overtime Entry</h3>
                                                    <form id="overtimeForm" action="{{ route('overtime.submit') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="user_email" value="{{ Auth::user()->email }}">
                                                        
                                                        <div class="form-group">
                                                            <label>Employee Name</label>
                                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label for="overtime-date">Date & Hour</label>
                                                            <input type="datetime-local" id="overtime-date" name="overtime_datetime" class="form-control" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="overtime-hours">Overtime Hours</label>
                                                            <input type="number" id="overtime-hours" name="overtime_hours" class="form-control" placeholder="Number of hours" required min="1">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="overtime-note">Note</label>
                                                            <textarea id="overtime-note" name="note" class="form-control" placeholder="Reason for overtime"></textarea>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="overtime-balance">Current Overtime Balance</label>
                                                            <input type="number" id="overtime-balance" class="form-control" value="{{ Auth::user()->overtime_hours ?? 0 }}" readonly>
                                                        </div>

                                                        <button type="submit" class="btn"><i class="fas fa-clock"></i> Submit Overtime</button>
                                                        <div id="overtimeMessage" class="message mt-3"></div>
                                                    </form>
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

            <!-- HR Reports Section (Visible only to HR users) -->
            @if(Auth::user()->role == 'hr')
            <div class="row clearfix mt-5">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header">
                            <h2>HR Reports</h2>
                        </div>
                        <div class="body">
                            <form id="hrReportForm">
                                <div class="form-group">
                                    <label for="report-month">Month</label>
                                    <select id="report-month" name="month" class="form-control">
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="report-year">Year</label>
                                    <input type="number" id="report-year" name="year" class="form-control" 
                                           value="{{ date('Y') }}" min="2023" max="2030">
                                </div>
                                <button type="button" id="generate-report" class="btn btn-primary">
                                    Generate Report
                                </button>
                            </form>
                            
                            <div id="hrReportResults" class="mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>

@include('helperPages.footerFiles')

<!-- Add Select2 JS for multiple select -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for multiple select
    $('#responsible-person').select2({
        placeholder: "Select responsible persons",
        allowClear: true
    });

    // Auto-set current date
    var today = new Date().toISOString().split('T')[0];
    $('#work-date, #payment-date').val(today);

    // Set user's available days and overtime balance
    $('#available-vacation').val('{{ Auth::user()->available_days ?? 0 }}');
    $('#overtime-balance').val('{{ Auth::user()->overtime_hours ?? 0 }}');
    $('#balance').val('{{ Auth::user()->available_days ?? 0 }}');
    $('#total-worked').val('{{ Auth::user()->overtime_hours ?? 0 }}');

    // Auto-calculate total hours today for working hours
    function calculateWorkingHours() {
        let total = 0;
        $('.working-hour').each(function() {
            if ($(this).val().trim() !== '') {
                total += parseInt($(this).data('hours'));
            }
        });
        $('#total-hours').val(total);
        
        // Calculate overtime (anything over 8 hours)
        let overtime = Math.max(0, total - 8);
        $('#overtime').val(overtime);
    }

    $('.working-hour').on('input', calculateWorkingHours);

    // Calculate leave duration
    function calculateLeaveDuration() {
        const start = new Date($('#leave-start').val());
        const end = new Date($('#leave-end').val());
        
        if (start && end && start <= end) {
            const diffTime = Math.abs(end - start);
            const diffHours = Math.ceil(diffTime / (1000 * 60 * 60)); 
            const days = Math.floor(diffHours / 24);
            const hours = diffHours % 24;
            
            $('#leave-days').val(`${days} days, ${hours} hours`);
            
            // Set return datetime to end datetime
            $('#return-datetime').val($('#leave-end').val());

            // Check if user has enough balance for paid leave
            const leaveType = $('input[name="leave_type"]:checked').val();
            const availableBalance = parseFloat($('#balance').val());
            
            if (leaveType !== 'unpaid' && days > availableBalance) {
                $('#leaveRequestMessage').html('<div class="alert alert-warning">Insufficient balance. Available: ' + availableBalance + ' days</div>');
            } else {
                $('#leaveRequestMessage').html('');
            }
        }
    }

    $('#leave-start, #leave-end').on('change', calculateLeaveDuration);
    $('input[name="leave_type"]').on('change', calculateLeaveDuration);

    // Tab functionality
    $(".tabs li").click(function() {
        $(".tabs li").removeClass("active");
        $(this).addClass("active");
        $(".tab_content").removeClass("active");
        var activeTab = $(this).attr("rel");
        $("#" + activeTab).addClass("active");
    });

    // AJAX form submission for all forms with improved error handling
    $('#workingHoursForm, #leaveRequestForm, #paymentRequestForm, #overtimeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Only process if this form is in the active tab
        if (!$(this).closest('.tab_content').hasClass('active')) {
            return false;
        }
        
        var form = $(this);
        var formData = form.serialize();
        var url = form.attr('action');
        var messageDiv = form.find('.message');
        
        // Clear previous messages
        messageDiv.html('');
        
        // Show loading indicator
        messageDiv.html('<div class="alert alert-info">Submitting...</div>');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<div class="alert alert-success">' + response.message + '</div>');
                    
                    // Reset form but keep select values
                    form.find('input[type="text"], input[type="number"], input[type="date"], input[type="datetime-local"], textarea').val('');
                    
                    // Reset dates to today
                    var today = new Date().toISOString().split('T')[0];
                    $('#work-date, #payment-date').val(today);
                    
                    // For working hours form, update the balances
                    if (response.total_hours_today !== undefined) {
                        $('#total-hours').val(response.total_hours_today);
                        $('#overtime').val(response.overtime_hours || 0);
                        
                        // Update user balances if returned in response
                        if (response.available_days !== undefined) {
                            $('#available-vacation').val(response.available_days);
                            $('#balance').val(response.available_days);
                        }
                        if (response.overtime_balance !== undefined) {
                            $('#total-worked').val(response.overtime_balance);
                            $('#overtime-balance').val(response.overtime_balance);
                        }
                    }

                    // For leave request, reset the form completely
                    if (form.attr('id') === 'leaveRequestForm') {
                        form[0].reset();
                        $('#responsible-person').val(null).trigger('change');
                        $('#balance').val('{{ Auth::user()->available_days ?? 0 }}');
                    }
                } else {
                    messageDiv.html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<div class="alert alert-danger"><strong>Please fix the following errors:</strong><ul>';
                    
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value + '</li>';
                    });
                    
                    errorHtml += '</ul></div>';
                    messageDiv.html(errorHtml);
                } else {
                    // Other errors
                    messageDiv.html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                }
            }
        });
    });

    // HR Report Generation
    $('#generate-report').click(function() {
        $.ajax({
            url: '{{ route("admin.generateReport") }}',
            type: 'GET',
            data: $('#hrReportForm').serialize(),
            success: function(response) {
                if (response.success) {
                    var html = '<h4>HR Report Results</h4>';
                    
                    // Display alerts
                    if (response.alerts.length > 0) {
                        html += '<div class="alert alert-warning"><h5>Alerts</h5><ul>';
                        response.alerts.forEach(function(alert) {
                            html += '<li>' + alert.message + '</li>';
                        });
                        html += '</ul></div>';
                    } else {
                        html += '<div class="alert alert-info">No alerts found</div>';
                    }
                    
                    // Display report summary
                    html += '<h5>Monthly Summary</h5><table class="table table-bordered"><tr><th>Employee</th><th>Total Hours</th></tr>';
                    for (var email in response.report) {
                        html += '<tr><td>' + response.report[email].name + '</td><td>' + response.report[email].total_hours + '</td></tr>';
                    }
                    html += '</table>';
                    
                    $('#hrReportResults').html(html);
                }
            }
        });
    });

    // Prevent CKEditor and noUiSlider errors by checking if elements exist
    if (typeof CKEDITOR !== 'undefined') {
        // Only initialize if the element exists
        $('textarea[data-editor="ckeditor"]').each(function() {
            if ($(this).length) {
                CKEDITOR.replace(this.id);
            }
        });
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
